<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\RepairTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Stock status uses each product's own low_stock_threshold column
     * (same rule as the Inventory page).
     *
     * A product that is in stock and sold LESS THAN OR EQUAL to this many units
     * in the selected period is "Slow Moving". Sold 0 = "Dead Stock".
     */
    private const SLOW_MOVING_MAX_QTY = 5;

    public function index(Request $request)
    {
        return view('reports.reports-index', $this->buildData($request));
    }

    public function pdf(Request $request)
    {
        $data = $this->buildData($request);
        $data['generatedBy'] = auth()->user()?->name ?? 'System';

        return Pdf::loadView('reports.reports-pdf', $data)
            ->download('hyh-report-' . $data['startDate']->toDateString() . '-to-' . $data['endDate']->toDateString() . '.pdf');
    }

    private function buildData(Request $request): array
    {
        [$filter, $startDate, $endDate] = $this->resolvePeriod($request);

        $rangeLabel = $startDate->isSameDay($endDate)
            ? $startDate->format('M d, Y')
            : $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');

        /*
        |--------------------------------------------------------------------------
        | SUMMARY CARDS
        |--------------------------------------------------------------------------
        | Retail  -> sales (status = completed, date = sale_date)
        | Repairs -> repair_tickets (status = completed, date = date_completed)
        */

        $salesQuery = fn () => Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate]);

        $repairsQuery = fn () => RepairTicket::where('status', 'completed')
            ->whereBetween('date_completed', [$startDate, $endDate]);

        $totalSales       = (float) $salesQuery()->sum('total_amount');
        $salesCount       = $salesQuery()->count();

        $repairRevenue    = (float) $repairsQuery()->sum('final_price');
        $repairsCompleted = $repairsQuery()->count();

        $totalTransactions = $salesCount + $repairsCompleted;
        $netRevenue        = $totalSales + $repairRevenue;

        // Profit (retail only): (selling price - product cost) x qty.
        // Repair tickets have no cost column in the schema, so no cost is invented for them.
        $profit = (float) (SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->selectRaw('SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as total_profit')
            ->value('total_profit') ?? 0);

        /*
        |--------------------------------------------------------------------------
        | BEST SELLING RETAIL PRODUCTS (top 5 by units sold, only products actually sold)
        |--------------------------------------------------------------------------
        */

        $bestSellingProducts = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->groupBy('products.id', 'products.product_name')
            ->select(
                'products.product_name',
                DB::raw('SUM(sale_items.quantity) as qty_sold'),
                DB::raw('SUM(sale_items.quantity * sale_items.unit_price) as sales_amount')
            )
            ->havingRaw('SUM(sale_items.quantity) > 0')
            ->orderByDesc('qty_sold')
            ->orderByDesc('sales_amount')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SLOW MOVING & DEAD STOCK (5 items)
        |--------------------------------------------------------------------------
        | Sales are aggregated per product in a sub-query FIRST (completed sales in the
        | selected range only), then joined to products. This avoids the old bug where
        | sale_items outside the date range were still being summed.
        | Only products that still have stock are listed (no stock = no stagnant capital).
        */

        $soldPerProduct = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->groupBy('sale_items.product_id')
            ->selectRaw('
                sale_items.product_id,
                SUM(sale_items.quantity) as qty_sold,
                SUM(sale_items.quantity * sale_items.unit_price) as sales_amount
            ');

        $slowMovingProducts = Product::active()->leftJoinSub($soldPerProduct, 'sold', 'sold.product_id', '=', 'products.id')
            ->where('products.stock_quantity', '>', 0)
            ->whereRaw('COALESCE(sold.qty_sold, 0) <= ?', [self::SLOW_MOVING_MAX_QTY])
            ->select(
                'products.product_name',
                'products.stock_quantity',
                DB::raw('COALESCE(sold.qty_sold, 0) as qty_sold'),
                DB::raw('COALESCE(sold.sales_amount, 0) as sales_amount')
            )
            ->orderBy('qty_sold')                       // dead stock (0) first, then slowest
            ->orderByDesc('products.stock_quantity')    // most stagnant stock first
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MOST REQUESTED REPAIR SERVICES (top 5 by number of requests)
        |--------------------------------------------------------------------------
        | Requests = tickets created in the range. Revenue = final_price of the
        | tickets in that group that are actually completed.
        */

        $topRepairServices = RepairTicket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('service_type')
            ->groupBy('service_type')
            ->select(
                'service_type',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN final_price ELSE 0 END) as revenue")
            )
            ->orderByDesc('total_requests')
            ->orderBy('service_type')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | INVENTORY STATUS (current snapshot - not date based)
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::active()->count();
        $inStock       = Product::active()->whereColumn('stock_quantity', '>', 'low_stock_threshold')->count();
        $lowStock      = Product::active()->where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
        $outOfStock    = Product::active()->where('stock_quantity', '<=', 0)->count();

        /*
        |--------------------------------------------------------------------------
        | STAFF PERFORMANCE
        |--------------------------------------------------------------------------
        */

        $staffPerformance = User::withCount([
                'sales as completed_sales_count' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'completed')
                        ->whereBetween('sale_date', [$startDate, $endDate]);
                },
                'assignedRepairTickets as completed_repairs_count' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'completed')
                        ->whereBetween('date_completed', [$startDate, $endDate]);
                },
            ])
            ->get()
            ->map(function ($user) use ($startDate, $endDate) {
                $salesRevenue = $user->sales()
                    ->where('status', 'completed')
                    ->whereBetween('sale_date', [$startDate, $endDate])
                    ->sum('total_amount');

                $repairRevenue = $user->assignedRepairTickets()
                    ->where('status', 'completed')
                    ->whereBetween('date_completed', [$startDate, $endDate])
                    ->sum('final_price');

                $user->revenue_generated = (float) $salesRevenue + (float) $repairRevenue;

                return $user;
            })
            ->sortByDesc('revenue_generated')
            ->take(10)
            ->values();

        // Last 8 weeks, retail vs repair revenue + total trajectory + insight
        // cards (same series shape as the dashboard Sales Overview, so the two
        // charts render identically; no Daily/Weekly/Monthly tabs here because
        // the page already has its own Today/Week/Month/Custom filter row).
        $seriesStart = Carbon::now()->subWeeks(7)->startOfWeek();
        $seriesEnd = Carbon::now()->endOfWeek();
        $weeklySeries = $this->revenueSeries($seriesStart, $seriesEnd);
        $insights = $this->salesInsights($seriesStart, $seriesEnd);

        return [
            'filter' => $filter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'rangeLabel' => $rangeLabel,

            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
            'netRevenue' => $netRevenue,
            'profit' => $profit,
            'repairsCompleted' => $repairsCompleted,

            'bestSellingProducts' => $bestSellingProducts,
            'slowMovingProducts' => $slowMovingProducts,
            'topRepairServices' => $topRepairServices,

            'totalProducts' => $totalProducts,
            'inStock' => $inStock,
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,

            'staffPerformance' => $staffPerformance,
            'weeklySeries' => $weeklySeries,
            'insights' => $insights,
        ];
    }

    /**
     * Weekly retail vs repair revenue for the Sales Overview chart, with
     * per-week totals, completed-repair counts, retail units sold, and the
     * peak week index (same shape as the dashboard chart series).
     * Groups by day in SQL (works on MySQL + SQLite + Postgres), buckets in PHP.
     */
    private function revenueSeries(Carbon $start, Carbon $end): array
    {
        $sales = DB::table('sales')
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$start, $end])
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->selectRaw('DATE(sale_date) as d, SUM(total_amount) as total')
            ->pluck('total', 'd');

        $saleQty = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->groupBy(DB::raw('DATE(sales.sale_date)'))
            ->selectRaw('DATE(sales.sale_date) as d, SUM(sale_items.quantity) as q')
            ->pluck('q', 'd');

        $repairs = DB::table('repair_tickets')
            ->where('status', 'completed')
            ->whereBetween('date_completed', [$start, $end])
            ->groupBy(DB::raw('DATE(date_completed)'))
            ->selectRaw('DATE(date_completed) as d, SUM(final_price) as total, COUNT(*) as c')
            ->get();
        $repairSums = [];
        $repairCounts = [];
        foreach ($repairs as $row) {
            $repairSums[$row->d] = (float) $row->total;
            $repairCounts[$row->d] = (int) $row->c;
        }

        $agg = [];
        $cursor = $start->copy()->startOfDay();
        $last = $end->copy()->endOfDay();
        while ($cursor->lte($last)) {
            $key = $cursor->copy()->startOfWeek()->toDateString();
            $agg[$key] ??= ['label' => $cursor->copy()->startOfWeek()->format('M d'), 'retail' => 0.0, 'repair' => 0.0, 'retailQty' => 0, 'repairCount' => 0];
            $day = $cursor->toDateString();
            $agg[$key]['retail'] += (float) ($sales[$day] ?? 0);
            $agg[$key]['repair'] += $repairSums[$day] ?? 0;
            $agg[$key]['retailQty'] += (int) ($saleQty[$day] ?? 0);
            $agg[$key]['repairCount'] += $repairCounts[$day] ?? 0;
            $cursor->addDay();
        }

        $rows = array_values($agg);
        $totals = [];
        foreach ($rows as $b) {
            $totals[] = round($b['retail'] + $b['repair'], 2);
        }
        $peakIdx = null;
        if ($totals && max($totals) > 0) {
            $peakIdx = array_search(max($totals), $totals, true);
            $rows[$peakIdx]['label'] .= ' (Peak)';
        }

        return [
            'labels' => array_column($rows, 'label'),
            'retail' => array_map(fn ($b) => round($b['retail'], 2), $rows),
            'repair' => array_map(fn ($b) => round($b['repair'], 2), $rows),
            'total' => $totals,
            'retailQty' => array_map(fn ($b) => (int) $b['retailQty'], $rows),
            'repairCount' => array_map(fn ($b) => (int) $b['repairCount'], $rows),
            'peakIdx' => $peakIdx,
        ];
    }

    /**
     * Bottom insight cards for the Sales Overview panel, computed over the
     * given window with portable queries (same DATE() grouping as above).
     */
    private function salesInsights(Carbon $start, Carbon $end): array
    {
        $sales = DB::table('sales')
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$start, $end])
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->selectRaw('DATE(sale_date) as d, SUM(total_amount) as total')
            ->pluck('total', 'd');

        $repairs = DB::table('repair_tickets')
            ->where('status', 'completed')
            ->whereBetween('date_completed', [$start, $end])
            ->groupBy(DB::raw('DATE(date_completed)'))
            ->selectRaw('DATE(date_completed) as d, SUM(final_price) as total')
            ->pluck('total', 'd');

        // Peak operational weekday by combined revenue.
        $byWeekday = [];
        foreach ($sales as $day => $total) {
            $dow = Carbon::parse($day)->format('l');
            $byWeekday[$dow] = ($byWeekday[$dow] ?? 0) + (float) $total;
        }
        foreach ($repairs as $day => $total) {
            $dow = Carbon::parse($day)->format('l');
            $byWeekday[$dow] = ($byWeekday[$dow] ?? 0) + (float) $total;
        }
        $peakDay = null;
        if ($byWeekday && max($byWeekday) > 0) {
            $name = array_search(max($byWeekday), $byWeekday, true);
            $peakDay = ['day' => $name.'s', 'revenue' => round(max($byWeekday), 2)];
        }

        // Top grossing retail category by revenue.
        $topCategory = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->groupBy('categories.id', 'categories.category_name')
            ->selectRaw('categories.category_name as name, SUM(sale_items.quantity * sale_items.unit_price) as revenue')
            ->orderByDesc('revenue')
            ->first();

        // Retail conversion: repair customers in window who also bought retail.
        $repairCustomers = RepairTicket::join('devices', 'devices.id', '=', 'repair_tickets.device_id')
            ->where('repair_tickets.status', 'completed')
            ->whereBetween('repair_tickets.date_completed', [$start, $end])
            ->distinct()
            ->pluck('devices.customer_id')
            ->filter()
            ->unique()
            ->values();
        $buyers = Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$start, $end])
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id')
            ->unique();
        $conversion = $repairCustomers->isNotEmpty()
            ? round(100 * $repairCustomers->intersect($buyers)->count() / $repairCustomers->count(), 1)
            : 0.0;

        return [
            'peakDay' => $peakDay,
            'topCategory' => $topCategory ? [
                'name' => $topCategory->name,
                'revenue' => round((float) $topCategory->revenue, 2),
            ] : null,
            'conversion' => $conversion,
        ];
    }

    /**
     * Turn the request into [filter, start, end]. Bad/missing input falls back safely.
     */
    private function resolvePeriod(Request $request): array
    {
        $filter = $request->get('filter', 'month');

        if (!in_array($filter, ['today', 'week', 'month', 'custom'], true)) {
            $filter = 'month';
        }

        switch ($filter) {
            case 'today':
                $start = Carbon::today();
                $end   = Carbon::today()->endOfDay();
                break;

            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end   = Carbon::now()->endOfWeek();
                break;

            case 'custom':
                try {
                    $start = Carbon::parse($request->input('start_date', Carbon::today()))->startOfDay();
                } catch (\Throwable $e) {
                    $start = Carbon::today();
                }

                try {
                    $end = Carbon::parse($request->input('end_date', $start))->endOfDay();
                } catch (\Throwable $e) {
                    $end = $start->copy()->endOfDay();
                }

                if ($start->gt($end)) {
                    [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                }
                break;

            default:
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
                break;
        }

        return [$filter, $start, $end];
    }
}