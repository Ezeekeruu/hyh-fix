<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Product;
use App\Models\RepairTicket;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 10;
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
        $lowStockCount = Product::where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)->count();

        // ---- Sales Overview chart (Chart.js): retail vs repair revenue ----
        $chart = [
            'daily'   => $this->revenueSeries(Carbon::today()->subDays(6), Carbon::today()->endOfDay(), 'day'),
            'weekly'  => $this->revenueSeries(Carbon::now()->subWeeks(7)->startOfWeek(), Carbon::now()->endOfWeek(), 'week'),
            'monthly' => $this->revenueSeries(Carbon::now()->subMonths(5)->startOfMonth(), Carbon::now()->endOfMonth(), 'month'),
        ];

        $topRepairService = RepairTicket::whereNotNull('service_type')
            ->groupBy('service_type')
            ->selectRaw('service_type, COUNT(*) as c')
            ->orderByDesc('c')
            ->value('service_type');

        $topRetailId = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->groupBy('sale_items.product_id')
            ->selectRaw('sale_items.product_id, SUM(sale_items.quantity) as q')
            ->orderByDesc('q')
            ->value('sale_items.product_id');
        $topRetailProduct = $topRetailId ? Product::find($topRetailId)?->product_name : null;

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
            'topRepairService'  => $topRepairService,
            'topRetailProduct'  => $topRetailProduct,
        ];
    }

    /**
     * Retail vs repair revenue buckets for the Sales Overview chart.
     * Groups by day in SQL (works on MySQL + SQLite), buckets in PHP.
     */
    private function revenueSeries(Carbon $start, Carbon $end, string $bucket): array
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

            $agg[$key] ??= ['label' => $label, 'retail' => 0.0, 'repair' => 0.0];
            $day = $cursor->toDateString();
            $agg[$key]['retail'] += (float) ($sales[$day] ?? 0);
            $agg[$key]['repair'] += (float) ($repairs[$day] ?? 0);
            $cursor->addDay();
        }

        return [
            'labels' => array_column($agg, 'label'),
            'retail' => array_map(fn ($b) => round($b['retail'], 2), array_values($agg)),
            'repair' => array_map(fn ($b) => round($b['repair'], 2), array_values($agg)),
        ];
    }
}