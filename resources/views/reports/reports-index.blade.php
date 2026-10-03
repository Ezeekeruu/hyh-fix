<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HYH FIX - Reports</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
</head>

<body>
    <div class="dashboard">

        <!--  Sidebar  -->
        <aside class="sidebar">
            <div class="logo">
                <h1>HYH <span>FIX</span></h1>
                <p>Cellphone Repair & Accessories</p>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ url('/dashboard') }}" class="nav-link">
                    <i class="fa-solid fa-table-cells-large"></i>
                    Dashboard
                </a>

                <a href="{{ url('/pos') }}" class="nav-link">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>

                <a href="{{ url('/repair-management') }}" class="nav-link ">
                    <i class="fa-solid fa-wrench"></i>
                    Repair Management
                </a>

                <a href="{{ url('/transaction-history') }}" class="nav-link">
                    <i class="fa-regular fa-clipboard"></i>
                    Transaction History
                </a>

                <a href="{{ url('/inventory') }}" class="nav-link">
                    <i class="fa-solid fa-box"></i>
                    Inventory
                </a>

                <a href="{{ url('/reports') }}" class="nav-link active">
                    <i class="fa-solid fa-chart-column"></i>
                    Reports
                </a>

                <a href="{{ url('/user-management') }}" class="nav-link">
                    <i class="fa-solid fa-users"></i>
                    User Management
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ url('/logout') }}" class="nav-link logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Log Out
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">

            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-btn">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>REPORTS</h2>
                </div>

                <div class="topbar-right">
                    <button class="notification-btn">
                        <i class="fa-regular fa-bell"></i>
                    </button>

                    <div class="profile">
                        <div class="avatar"></div>
                        <div class="profile-info">
                            <h4></h4>
                            <span></span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <!-- Date Range + Export -->
            <section class="page-actions">
                <div class="range-wrap">
                    <form method="GET" action="{{ url('/reports') }}" class="range-tabs">
                        <button type="submit" name="filter" value="today" class="{{ $filter === 'today' ? 'active' : '' }}">Today</button>
                        <button type="submit" name="filter" value="week" class="{{ $filter === 'week' ? 'active' : '' }}">This Week</button>
                        <button type="submit" name="filter" value="month" class="{{ $filter === 'month' ? 'active' : '' }}">This Month</button>
                        <button type="button" id="customDateToggle" class="custom-date {{ $filter === 'custom' ? 'active' : '' }}">
                            <i class="fa-regular fa-calendar"></i>
                            Custom Date
                        </button>
                    </form>

                    <form method="GET" action="{{ url('/reports') }}" id="customRangeForm"
                        class="custom-range-form {{ $filter === 'custom' ? 'show' : '' }}">
                        <input type="hidden" name="filter" value="custom">
                        <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" required>
                        <span>to</span>
                        <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" required>
                        <button type="submit" class="apply-btn">Apply</button>
                    </form>
                </div>

                <div class="export-actions">
                    <a href="{{ route('reports.pdf', request()->query()) }}" class="export-btn pdf" style="text-decoration:none;">
                        <span class="export-icon"><i class="fa-regular fa-file-pdf"></i></span>
                        Export PDF
                    </a>
                </div>
            </section>

            <!-- Summary Cards -->
            <section class="stats-grid">

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>TOTAL SALES</h5>
                        <div class="icon-box">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">₱{{ number_format($totalSales, 2) }}</h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>TOTAL TRANSACTIONS</h5>
                        <div class="icon-box">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">{{ number_format($totalTransactions) }}</h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>NET REVENUE & PROFIT</h5>
                        <div class="icon-box success">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">₱{{ number_format($netRevenue, 2) }}</h2>
                    <p class="stat-sub">Profit: ₱{{ number_format($profit, 2) }}</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>REPAIRS COMPLETED</h5>
                        <div class="icon-box">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">{{ number_format($repairsCompleted) }}</h2>
                </div>
            </section>

            <!-- Sales Overview -->
            <section class="analytics-card">

                <div class="analytics-header">
                    <div>
                        <div class="title-row">
                            <h2>Sales Overview</h2>
                        </div>

                        <p>Sales comparison of repairs, and accessories over the last 8 weeks.</p>
                    </div>
                </div>



                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="dot dark"></span>
                        Repair Services
                    </div>

                    <div class="legend-item">
                        <span class="dot blue"></span>
                        Retail & Parts
                    </div>

                    <div class="legend-item">
                        <span class="dash"></span>
                        Total Revenue Trajectory
                    </div>
                </div>

                <!-- CHART: repairs vs retail revenue, last 8 weeks (Chart.js) -->
                <div class="chart-placeholder" id="salesOverviewChart" style="position:relative;"><canvas id="salesChart"></canvas></div>
            </section>

            <!-- Insight Row -->
            <section class="insight-row">
                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-regular fa-calendar"></i></div>
                    <div>
                        <span>Peak Operational Day</span>
                        <h4></h4>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                    <div>
                        <span>Top Grossing Category</span>
                        <h4></h4>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                    <div>
                        <span>Retail Conversion Rate</span>
                        <h4></h4>
                    </div>
                </div>
            </section>

            <!-- Best-Selling / Slow-Moving -->
            <section class="panel-grid">

                <div class="panel-card">
                    <div class="panel-header">
                        <div>
                            <div class="panel-header-title">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <h2>Best-Selling Retail Products</h2>
                            </div>
                            <div class="panel-header-sub">Top retail products based on units sold during the selected period.</div>
                        </div>
                        <span class="panel-meta">{{ $rangeLabel }}</span>
                    </div>

                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="num">Sold</th>
                                <th class="num">Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bestSellingProducts as $product)
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumb"><i class="fa-solid fa-box"></i></div>
                                        <span class="product-name">{{ $product->product_name }}</span>
                                    </div>
                                </td>
                                <td class="num">{{ number_format($product->qty_sold) }}</td>
                                <td class="num">₱{{ number_format($product->sales_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="empty-row">No retail sales in the selected period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="panel-card">
                    <div class="panel-header warning">
                        <div>
                            <div class="panel-header-title">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <h2>Slow-Moving & Dead Stock Alert</h2>
                            </div>
                            <div class="panel-header-sub">Products with low or no sales movement during the selected period.</div>
                        </div>
                        <span class="panel-meta">{{ $rangeLabel }}</span>
                    </div>

                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="num">Sold</th>
                                <th class="num">Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($slowMovingProducts as $product)
                            @php $isDead = (int) $product->qty_sold === 0; @endphp
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumb alert"><i class="fa-solid fa-box"></i></div>
                                        <div>
                                            <span class="product-name">{{ $product->product_name }}</span>
                                            <div class="product-sub">
                                                <span class="status-badge {{ $isDead ? 'dead' : 'slow' }}">
                                                    {{ $isDead ? 'Dead Stock' : 'Slow Moving' }}
                                                </span>
                                                <span>{{ number_format($product->stock_quantity) }} in stock</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="num">{{ number_format($product->qty_sold) }}</td>
                                <td class="num">₱{{ number_format($product->sales_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="empty-row">No slow-moving or dead stock for the selected period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Most Requested Repairs / Inventory Status -->
            <section class="panel-grid">

                <div class="panel-card">
                    <div class="panel-header">
                        <div>
                            <div class="panel-header-title">
                                <i class="fa-solid fa-briefcase"></i>
                                <h2>Most Requested Repair Services</h2>
                            </div>
                            <div class="panel-header-sub">Most frequently requested repair services during the selected period.</div>
                        </div>
                        <span class="panel-meta">{{ $rangeLabel }}</span>
                    </div>

                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th class="num">Requests</th>
                                <th class="num">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topRepairServices as $service)
                            <tr>
                                <td>{{ $service->service_type }}</td>
                                <td class="num">{{ number_format($service->total_requests) }}</td>
                                <td class="num">₱{{ number_format($service->revenue, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="empty-row">No repair requests in the selected period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="panel-card">
                    <div class="panel-header">
                        <div class="panel-header-title">
                            <i class="fa-solid fa-box"></i>
                            <h2>Inventory Status</h2>
                        </div>
                        <span class="panel-meta">{{ number_format($totalProducts) }} total products</span>
                    </div>

                    @php
                    $pctIn = $totalProducts > 0 ? round($inStock / $totalProducts * 100, 2) : 0;
                    $pctLow = $totalProducts > 0 ? round($lowStock / $totalProducts * 100, 2) : 0;
                    $pctOut = $totalProducts > 0 ? round($outOfStock / $totalProducts * 100, 2) : 0;
                    @endphp

                    <div class="stock-progress">
                        <span class="in-stock" @style(["width: {$pctIn}%"])></span>
                        <span class="low-stock" @style(["width: {$pctLow}%"])></span>
                        <span class="out-stock" @style(["width: {$pctOut}%"])></span>
                    </div>

                    <div class="stock-summary">
                        <div class="stock-summary-item">
                            <div class="dot-label"><span class="dot green"></span> In Stock</div>
                            <h3>{{ number_format($inStock) }}</h3>
                        </div>

                        <div class="stock-summary-item low">
                            <div class="dot-label"><span class="dot blue"></span> Low Stock</div>
                            <h3>{{ number_format($lowStock) }}</h3>
                            <small>Needs reorder</small>
                        </div>

                        <div class="stock-summary-item out">
                            <div class="dot-label"><span class="dot red"></span> Out of Stock</div>
                            <h3>{{ number_format($outOfStock) }}</h3>
                            <small>Urgent restock</small>
                        </div>
                    </div>

                    <a href="{{ url('/inventory') }}" class="panel-btn">
                        <i class="fa-solid fa-warehouse"></i>
                        Open Inventory Manager
                    </a>
                </div>
            </section>

            <!-- Staff Performance -->
            <section class="staff-card">
                <div class="staff-header">
                    <div>
                        <h2>Staff Performance</h2>
                        <div class="staff-header-sub">Individual productivity, and total revenue contribution</div>
                    </div>

                    <div class="staff-header-right">
                        <span class="panel-meta">{{ $rangeLabel }}</span>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="staff-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th class="num">Repairs Completed</th>
                                <th class="num">Sales</th>
                                <th class="num">Revenue Generated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($staffPerformance as $staff)
                            @php
                            $staffName = $staff->name ?? $staff->full_name ?? $staff->username ?? 'Unknown';
                            $initials = collect(explode(' ', trim($staffName)))
                            ->filter()->take(2)
                            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                            ->implode('');
                            $role = $staff->role ?? '';
                            $roleClass = stripos($role, 'tech') !== false ? 'tech'
                            : (stripos($role, 'clerk') !== false ? 'clerk'
                            : (stripos($role, 'secretary') !== false ? 'secretary' : 'staff-tech'));
                            @endphp
                            <tr>
                                <td>
                                    <div class="staff-name">
                                        <div class="staff-avatar">{{ $initials }}</div>
                                        {{ $staffName }}
                                    </div>
                                </td>
                                <td><span class="role-badge {{ $roleClass }}">{{ $role !== '' ? ucfirst($role) : '—' }}</span></td>
                                <td class="num">{{ number_format($staff->completed_repairs_count) }}</td>
                                <td class="num">{{ number_format($staff->completed_sales_count) }}</td>
                                <td class="num revenue">₱{{ number_format($staff->revenue_generated, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-row">No staff activity for the selected period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>


    <script>
        document.getElementById('customDateToggle').addEventListener('click', function() {
            document.getElementById('customRangeForm').classList.toggle('show');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            var el = document.getElementById('salesChart');
            if (!el || typeof Chart === 'undefined') return;
            var series = @json($weeklySeries);
            new Chart(el, {
                type: 'bar',
                data: {
                    labels: series.labels,
                    datasets: [
                        { label: 'Repair Services', data: series.repair, backgroundColor: '#1e293b', borderRadius: 6 },
                        { label: 'Retail & Parts', data: series.retail, backgroundColor: '#60a5fa', borderRadius: 6 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { callback: function (v) { return '₱' + v; } } } }
                }
            });
        })();
    </script>

</body>

</html>