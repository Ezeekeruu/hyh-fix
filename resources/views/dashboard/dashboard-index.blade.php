<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HYH FIX Dashboard</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
                <a href="{{ url('/dashboard') }}" class="nav-link active">
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

                <a href="{{ url('/reports') }}" class="nav-link">
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
                    <h2>DASHBOARD</h2>
                </div>

                <div class="topbar-right">
                    <button class="notification-btn">
                        <i class="fa-regular fa-bell"></i>
                    </button>

                    <div class="profile">
                        <div class="avatar"></div>
                        <div class="profile-info">
                            <h4>Sonayah Faisal</h4>
                            <span> Manager</span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <section class="welcome-section">
                <div>
                    <h1>Welcome, Sonayah!</h1>
                    <p>Here is what's happening at HYH Fix today</p>
                </div>
                <div class="date-card">
                    <i class="fa-regular fa-calendar"></i>
                    <div>
                        <h5 id="date"></h5>
                        <span id="time"></span>
                    </div>
                </div>
            </section>

            <!-- Cards -->
            <section class="stats-grid">

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>TODAY'S SALES</h5>
                        <div class="icon-box">
                            <i class="fa-solid fa-money-bill"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">₱{{ number_format($todaySales, 2) }}</h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>TOTAL TRANSACTIONS</h5>
                        <div class="icon-box">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">{{ $totalTransactions }}</h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>LOW STOCK ITEMS</h5>
                        <div class="icon-box warning">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">{{ $lowStockCount }}</h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>PENDING REPAIRS</h5>
                        <div class="icon-box">
                            <i class="fa-solid fa-toolbox"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">{{ $pendingRepairs }}</h2>
                </div>
            </section>

            <!-- Sales Overview -->
            <section class="analytics-grid">

                <div class="analytics-card">

                    <div class="analytics-header">
                        <div>
                            <div class="title-row">
                                <h2>Sales Overview & Revenue</h2>
                            </div>

                            <p>
                                Accumulated revenue this week
                            </p>
                        </div>

                        <div class="period-tabs">
                            <button type="button" data-range="daily">Daily</button>
                            <button type="button" data-range="weekly" class="active">Weekly</button>
                            <button type="button" data-range="monthly">Monthly</button>
                        </div>
                    </div>

                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="dot dark"></span>
                            Repair Services
                        </div>

                        <div class="legend-item">
                            <span class="dot blue"></span>
                            Device Accessories
                        </div>
                    </div>

                    <!-- CHART: retail vs repair revenue (Chart.js) -->
                    <div class="chart-placeholder" style="position:relative;"><canvas id="salesChart"></canvas></div>

                    <!-- BOTTOM METRICS -->
                    <div class="analytics-bottom">
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fa-solid fa-mobile-screen"></i>
                            </div>

                            <div>
                                <span>Top Repair Service</span>
                                <h4>{{ $topRepairService ?? '—' }}</h4>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fa-solid fa-plug-circle-bolt"></i>
                            </div>

                            <div>
                                <span>Top Retail Accessory</span>
                                <h4>{{ $topRetailProduct ?? '—' }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LOW STOCK -->
                <div class="low-stock-card">
                    <div class="low-stock-header">
                        <h2>Low Stock Products</h2>
                        <a href="{{ url('/inventory') }}" class="manage-btn">Manage</a>
                    </div>

                    <div class="stock-list">
                        @php
                        $lowStockProducts = \App\Models\Product::where('stock_quantity', '<=', 10)
                            ->orderBy('stock_quantity')
                            ->get();
                            @endphp

                            @forelse($lowStockProducts as $product)
                            <div class="stock-item">
                                <div class="stock-image">
                                    @if(!empty($product->image_path))
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->product_name }}">
                                    @else
                                    <i class="fa-solid fa-box-open"></i>
                                    @endif
                                </div>

                                <div class="stock-info">
                                    <h4>{{ $product->product_name }}</h4>
                                    <span class="{{ $product->stock_quantity == 0 ? 'text-danger' : '' }}">
                                        {{ $product->stock_quantity }} left
                                    </span>
                                </div>

                                <div class="stock-badge {{ $product->stock_quantity == 0 ? 'out-of-stock' : 'low-stock' }}">
                                    {{ $product->stock_quantity == 0 ? 'Out of Stock' : 'Low Stock' }}
                                </div>
                            </div>
                            @empty
                            <p class="no-stock-text">All products are adequately stocked.</p>
                            @endforelse
                    </div>
                </div>
            </section>

            <!-- Recent Transactions -->
            <section class="transactions-card" id="transactions">
                <div class="transactions-header">
                    <div>
                        <h2>Recent Transactions</h2>

                        <p>
                            Combined Point of Sale checkouts and completed repair work orders
                        </p>
                    </div>

                    <!-- Filters -->
                    <form action="{{ url('/dashboard') }}#transactions" method="GET" class="filters">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search #ID, customer, or repair service...">
                            <button type="submit" class="search-btn">Search</button>
                        </div>

                        <div class="filter-dropdown">
                            <select name="status" onchange="this.form.submit()">
                                <option value="all" {{ !in_array(request('status'), ['complete','in_progress']) ? 'selected' : '' }}>Status: All</option>
                                <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>Complete</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            </select>
                        </div>

                        <div class="filter-dropdown small-select">
                            <select name="payment" onchange="this.form.submit()">
                                <option value="all" {{ !in_array(request('payment'), ['cash','gcash']) ? 'selected' : '' }}>Payment: All</option>
                                <option value="cash" {{ request('payment') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="gcash" {{ request('payment') === 'gcash' ? 'selected' : '' }}>GCash</option>
                            </select>
                        </div>

                        <a href="{{ url('/dashboard') }}#transactions" class="btn-filter-icon" title="Clear filters">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                        </a>
                    </form>

                </div>

                <!-- Table -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Customer & Product/Service</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Timestamp</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($transactions as $t)
                            <tr>
                                <td>#{{ $t->record_id }}</td>
                                <td>
                                    <div style="display:block;font-weight:600;">
                                        {{ $t->customer_name }}
                                    </div>

                                    <div style="display:block;color:#6b7280; font-size:16px;">
                                        {{ $t->item_name ?? '—' }}
                                    </div>

                                    @if($t->type === 'Retail' && $t->item_count > 1)
                                    <div style="display:block;color:#9ca3af;font-size:14px;">
                                        + {{ $t->item_count - 1 }} item{{ ($t->item_count - 1) > 1 ? 's' : '' }}
                                    </div>
                                    @endif
                                </td>

                                <td>{{ $t->type }}</td>
                                <td>₱{{ number_format($t->amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('M d, Y h:i A') }}</td>
                                <td>{{ ['cash' => 'Cash', 'gcash' => 'GCash'][strtolower($t->method ?? '')] ?? ($t->method ?? '—') }}</td>
                                <td>{{ ucfirst($t->status) }}</td>

                                <td>
                                    <div class="actions">
                                        @if($t->type === 'Retail')
                                        <a href="{{ url('/sales/' . $t->record_id) }}" title="View receipt"><i class="fa-regular fa-file-lines"></i></a>
                                        @else
                                        <i class="fa-regular fa-file-lines" style="opacity:.4" title="No receipt view for repairs"></i>
                                        @endif
                                        <i class="fa-solid fa-print"></i>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">No transactions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <button type="button"
                        {{ $transactions->onFirstPage() ? 'disabled' : '' }}
                        onclick="window.location='{{ $transactions->previousPageUrl() }}'">Prev</button>

                    <div class="pages">
                        @for($p = max(1, $transactions->currentPage() - 1); $p <= min($transactions->lastPage(), $transactions->currentPage() + 1); $p++)
                            <button type="button"
                                class="{{ $p === $transactions->currentPage() ? 'active' : '' }}"
                                onclick="window.location='{{ $transactions->url($p) }}'">{{ $p }}</button>
                            @endfor
                    </div>

                    <button type="button"
                        {{ $transactions->hasMorePages() ? '' : 'disabled' }}
                        onclick="window.location='{{ $transactions->nextPageUrl() }}'">Next</button>
                </div>

                <div class="pagination-info">
                    Showing <strong>{{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }}</strong>
                    out of <strong>{{ $transactions->total() }}</strong> transactions
                </div>
            </section>
        </main>
    </div>

    <script>
        function updateDateTime() {
            const now = new Date();

            const dateOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const timeOptions = {
                weekday: 'long',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            };

            document.getElementById('date').textContent = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('time').textContent = now.toLocaleTimeString('en-US', timeOptions);
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            var el = document.getElementById('salesChart');
            if (!el || typeof Chart === 'undefined') return;
            var series = @json($chart);
            var chart = new Chart(el, {
                type: 'bar',
                data: {
                    labels: series.weekly.labels,
                    datasets: [
                        { label: 'Repair Services', data: series.weekly.repair, backgroundColor: '#1e293b', borderRadius: 6 },
                        { label: 'Device Accessories', data: series.weekly.retail, backgroundColor: '#60a5fa', borderRadius: 6 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { callback: function (v) { return '₱' + v; } } } }
                }
            });
            document.querySelectorAll('.period-tabs button').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.period-tabs button').forEach(function (b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    var r = series[btn.getAttribute('data-range')] || series.weekly;
                    chart.data.labels = r.labels;
                    chart.data.datasets[0].data = r.repair;
                    chart.data.datasets[1].data = r.retail;
                    chart.update();
                });
            });
        })();
    </script>

</body>

</html>