<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Product;
use App\Models\RepairTicket;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request)
    {
        if (auth()->user()?->role !== 'admin') {
            return redirect()->route('staff.dashboard');
        }

        return view('dashboard.dashboard-index', $this->dashboardData($request));
    }

    public function staffDashboard(Request $request)
    {
        if (auth()->user()?->role === 'admin') {
            return redirect()->route('dashboard');
        }

        return view('dashboard.staff-dashboard', $this->dashboardData($request));
    }

    private function dashboardData(Request $request): array
    {
        $customerCount = Customer::count();
        $deviceCount   = Device::count();
        $productCount  = Product::count();

        $pendingRepairs = RepairTicket::where('status', 'pending')->count();
        $completedSales = Sale::where('status', 'completed')->count();

        // ---- Summary cards ----
        $start = Carbon::today();
        $end   = Carbon::today()->endOfDay();

        $salesToday = Sale::where('status', 'completed')
            ->whereBetween('sale_date', [$start, $end])
            ->sum('total_amount');

        $repairsToday = RepairTicket::where('status', 'completed')
            ->whereBetween('date_completed', [$start, $end])
            ->sum('final_price');

        $todaySales    = $salesToday + $repairsToday;
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();

        // ---- Sales Overview chart (Chart.js): retail vs repair bars + total line ----
        $chart = [
            'daily'   => $this->revenueSeries(Carbon::today()->subDays(6), Carbon::today()->endOfDay(), 'day'),
            'weekly'  => $this->revenueSeries(Carbon::now()->subWeeks(7)->startOfWeek(), Carbon::now()->endOfWeek(), 'week'),
            'monthly' => $this->revenueSeries(Carbon::now()->subMonths(5)->startOfMonth(), Carbon::now()->endOfMonth(), 'month'),
        ];

        // ---- Bottom insight cards (trailing 8 weeks, same window as the chart) ----
        $insights = $this->salesInsights(
            Carbon::now()->subWeeks(7)->startOfWeek(),
            Carbon::now()->endOfWeek()
        );

        // ---- Staff performance table (admin dashboard; same trailing window) ----
        $periodStart = Carbon::now()->subWeeks(7)->startOfWeek();
        $periodEnd = Carbon::now()->endOfWeek();

        $staffPerformance = User::withCount([
            'sales as completed_sales_count' => function ($q) use ($periodStart, $periodEnd) {
                $q->where('status', 'completed')
                    ->whereBetween('sale_date', [$periodStart, $periodEnd]);
            },
            'assignedRepairTickets as completed_repairs_count' => function ($q) use ($periodStart, $periodEnd) {
                $q->where('status', 'completed')
                    ->whereBetween('date_completed', [$periodStart, $periodEnd]);
            },
        ])
            ->get()
            ->map(function ($user) use ($periodStart, $periodEnd) {
                $salesRevenue = $user->sales()
                    ->where('status', 'completed')
                    ->whereBetween('sale_date', [$periodStart, $periodEnd])
                    ->sum('total_amount');

                $repairRevenue = $user->assignedRepairTickets()
                    ->where('status', 'completed')
                    ->whereBetween('date_completed', [$periodStart, $periodEnd])
                    ->sum('final_price');

                $user->revenue_generated = (float) $salesRevenue + (float) $repairRevenue;

                return $user;
            })
            ->sortByDesc('revenue_generated')
            ->values();

        $staffRole = $request->query('staff_role', 'all');
        if (in_array($staffRole, ['admin', 'staff'], true)) {
            $staffPerformance = $staffPerformance->where('role', $staffRole)->values();
        } else {
            $staffRole = 'all';
        }

        // ---- Transactions: Retail (sales) UNION Repair (repair_tickets) ----
        // Both SELECTs must have the same columns in the same order.
        $retail = DB::table('sales')
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.status', 'completed')
            ->selectRaw("
                sales.id AS record_id,
                'Retail' AS type,
                COALESCE(customers.name, 'Walk-in Customer') AS customer_name,
                (SELECT p.product_name
                   FROM sale_items si
                   JOIN products p ON p.id = si.product_id
                  WHERE si.sale_id = sales.id
                  ORDER BY si.id
                  LIMIT 1) AS item_name,
                (SELECT COUNT(*) FROM sale_items si WHERE si.sale_id = sales.id) AS item_count,
                NULL AS service_text,
                sales.total_amount AS amount,
                sales.sale_date AS transaction_date,
                sales.payment_method AS method,
                'Complete' AS status
            ");

        // CONCAT() is MySQL-only, so build the device label with the
        // concatenation operator each driver supports (sqlite uses ||).
        $deviceLabel = DB::getDriverName() === 'sqlite'
            ? "(devices.brand || ' ' || devices.model)"
            : "CONCAT(devices.brand, ' ', devices.model)";

        $repairs = DB::table('repair_tickets')
            ->join('devices', 'devices.id', '=', 'repair_tickets.device_id')
            ->join('customers', 'customers.id', '=', 'devices.customer_id')
            ->whereIn('repair_tickets.status', ['completed', 'in_progress'])
            ->selectRaw("
                repair_tickets.id AS record_id,
                'Repair' AS type,
                customers.name AS customer_name,
                {$deviceLabel} AS item_name,
                1 AS item_count,
                repair_tickets.problem_description AS service_text,
                CASE WHEN repair_tickets.status = 'completed'
                     THEN repair_tickets.final_price
                     ELSE repair_tickets.quotation_price END AS amount,
                COALESCE(repair_tickets.date_completed, repair_tickets.date_received) AS transaction_date,
                NULL AS method,
                CASE repair_tickets.status
                     WHEN 'completed'   THEN 'Complete'
                     WHEN 'in_progress' THEN 'In Progress'
                END AS status
            ");

        $base = DB::query()->fromSub($retail->unionAll($repairs), 't');

        // Card = today's transactions (same combined list, filtered to the
        // current day). whereDate works on the derived table on MySQL + SQLite.
        $totalTransactions = (clone $base)->whereDate('transaction_date', Carbon::today())->count();

        // ---- Search + filters ----
        $search  = trim((string) $request->query('search', ''));
        $status  = $request->query('status', 'all');
        $payment = $request->query('payment', 'all');

        $statusMap = ['complete' => 'Complete', 'in_progress' => 'In Progress'];

        $transactions = (clone $base)
            ->when($search !== '', function ($q) use ($search) {
                $idTerm = ltrim($search, '#');
                $like   = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);

                $q->where(function ($w) use ($idTerm, $like) {
                    if ($idTerm !== '' && ctype_digit($idTerm)) {
                        $w->orWhere('record_id', (int) $idTerm);
                    }
                    $w->orWhere('customer_name', 'like', "%{$like}%")
                      ->orWhere('service_text', 'like', "%{$like}%");
                });
            })
            ->when(isset($statusMap[$status]), fn ($q) => $q->where('status', $statusMap[$status]))
            ->when(in_array($payment, ['cash', 'gcash'], true),
                   fn ($q) => $q->whereRaw('LOWER(method) = ?', [$payment]))
            ->orderByDesc('transaction_date')
            ->orderByDesc('record_id')
            ->paginate(self::PER_PAGE)
            ->withQueryString()
            ->fragment('transactions');

        return [
            'customerCount'     => $customerCount,
            'deviceCount'       => $deviceCount,
            'productCount'      => $productCount,
            'pendingRepairs'    => $pendingRepairs,
            'completedSales'    => $completedSales,
            'todaySales'        => $todaySales,
            'totalTransactions' => $totalTransactions,
            'lowStockCount'     => $lowStockCount,
            'transactions'      => $transactions,
            'chart'             => $chart,
            'insights'          => $insights,
            'staffPerformance'  => $staffPerformance,
            'staffRole'         => $staffRole,
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
     * Revenue buckets for the Sales Overview chart, with per-bucket totals,
     * completed-repair counts, retail units sold, and the peak bucket index.
     * Groups by day in SQL (works on MySQL + SQLite + Postgres), buckets in PHP.
     */
    private function revenueSeries(Carbon $start, Carbon $end, string $bucket): array
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
            if ($bucket === 'week') {
                $key = $cursor->copy()->startOfWeek()->toDateString();
                $label = $cursor->copy()->startOfWeek()->format('M d');
            } elseif ($bucket === 'month') {
                $key = $cursor->format('Y-m');
                $label = $cursor->format('M');
            } else {
                $key = $cursor->toDateString();
                $label = $cursor->format('D');
            }

            $agg[$key] ??= ['label' => $label, 'retail' => 0.0, 'repair' => 0.0, 'retailQty' => 0, 'repairCount' => 0];
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
}