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
    <link rel="stylesheet" href="{{ asset('css/topbar-user.css') }}">
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
                <p class="nav-group-label">MAIN</p>
                <a href="{{ auth()->check() && auth()->user()->isStaff() ? url('/staff/dashboard') : url('/dashboard') }}" class="nav-link active">
                    <i class="fa-solid fa-table-cells-large"></i>
                    Dashboard
                </a>

                <a href="{{ url('/pos') }}" class="nav-link">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>

                <p class="nav-group-label">MANAGEMENT</p>
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

@if(auth()->check() && auth()->user()->isAdmin())
                <p class="nav-group-label">ADMIN</p>
                <a href="{{ url('/reports') }}" class="nav-link">
                    <i class="fa-solid fa-chart-column"></i>
                    Reports
                </a>

                <a href="{{ url('/categories') }}" class="nav-link">
                    <i class="fa-solid fa-tags"></i>
                    Categories
                </a>

                <a href="{{ url('/suppliers') }}" class="nav-link">
                    <i class="fa-solid fa-truck-field"></i>
                    Suppliers
                </a>

                <a href="{{ url('/service-types') }}" class="nav-link">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    Service Types
                </a>

                <a href="{{ url('/user-management') }}" class="nav-link">
                    <i class="fa-solid fa-users"></i>
                    User Management
                </a>
@endif
            </nav>

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

                    <div class="profile-dropdown">
                        <button type="button" class="profile-btn" aria-haspopup="true" onclick="toggleProfileMenu(event)">
                            <div class="avatar"></div>
                            <div class="profile-info">
                                <h4>{{ auth()->user()->name }}</h4>
                                <span>{{ auth()->user()->roleLabel() }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="profile-menu" hidden>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" title="Log out">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    <script>
                    function toggleProfileMenu(event) {
                        event.stopPropagation();
                        var menu = event.currentTarget.parentElement.querySelector('.profile-menu');
                        var willOpen = menu.hasAttribute('hidden');
                        document.querySelectorAll('.profile-menu').forEach(function (m) { m.setAttribute('hidden', ''); });
                        if (willOpen) { menu.removeAttribute('hidden'); }
                    }
                    document.addEventListener('click', function () {
                        document.querySelectorAll('.profile-menu').forEach(function (m) { m.setAttribute('hidden', ''); });
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') { document.querySelectorAll('.profile-menu').forEach(function (m) { m.setAttribute('hidden', ''); }); }
                    });
                    </script>
                </div>
            </header>

            <section class="welcome-section">
                <div>
                    <h1>Welcome, {{ auth()->user()->name }}!</h1>
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

                <a href="{{ auth()->user()->isAdmin() ? url('/reports') . '?filter=today' : url('/transaction-history') . '?date=today' }}" class="stat-card stat-link" title="{{ auth()->user()->isAdmin() ? 'View reports' : "View today's transactions" }}">
                    <div class="stat-top">
                        <h5>TODAY'S SALES</h5>
                        <div class="icon-box">
                            <i class="fa-solid fa-money-bill"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">₱{{ number_format($todaySales, 2) }}</h2>
                </a>

                <a href="{{ url('/transaction-history') }}?date=today" class="stat-card stat-link" title="View transaction history">
                    <div class="stat-top">
                        <h5>TODAY'S TRANSACTIONS</h5>
                        <div class="icon-box">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">{{ $totalTransactions }}</h2>
                </a>

                <a href="{{ url('/inventory') }}?status=low_stock" class="stat-card stat-link" title="View low stock inventory">
                    <div class="stat-top">
                        <h5>LOW STOCK ITEMS</h5>
                        <div class="icon-box warning">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">{{ $lowStockCount }}</h2>
                </a>

                <a href="{{ url('/repair-management') }}?status=pending" class="stat-card stat-link" title="View pending repairs">
                    <div class="stat-top">
                        <h5>PENDING REPAIRS</h5>
                        <div class="icon-box">
                            <i class="fa-solid fa-toolbox"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">{{ $pendingRepairs }}</h2>
                </a>
            </section>

            <!-- Sales Overview -->
            <section class="analytics-grid">

                <div class="analytics-card">

                    <div class="analytics-header">
                        <div>
                            <div class="title-row">
                                <h2>Sales Overview</h2>
                            </div>

                            <p>
                                Sales comparison of repairs and accessories
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

                        <div class="legend-item">
                            <span class="dot line"></span>
                            Total Revenue Trajectory
                        </div>
                    </div>

                    <!-- CHART: retail vs repair revenue (Chart.js) -->
                    <div class="chart-placeholder" style="position:relative;"><canvas id="salesChart"></canvas></div>

                    <!-- BOTTOM METRICS -->
                    <div class="analytics-bottom">
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fa-regular fa-calendar"></i>
                            </div>

                            <div>
                                <span>Peak Operational Day</span>
                                <h4>{{ $insights['peakDay'] ? $insights['peakDay']['day'] . ' (₱' . number_format($insights['peakDay']['revenue'], 2) . ')' : '—' }}</h4>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fa-solid fa-tags"></i>
                            </div>

                            <div>
                                <span>Top Grossing Category</span>
                                <h4>{{ $insights['topCategory'] ? $insights['topCategory']['name'] . ' (₱' . number_format($insights['topCategory']['revenue'], 2) . ')' : '—' }}</h4>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fa-solid fa-bag-shopping"></i>
                            </div>

                            <div>
                                <span>Retail Conversion Rate</span>
                                <h4>{{ number_format($insights['conversion'], 1) }}% (of repair clients)</h4>
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
                        $lowStockProducts = \App\Models\Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
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
                                <td>#{{ ($t->type === 'Retail' ? 'S' : 'R') . $t->record_id }}</td>
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
                                        <a href="{{ url('/sales/' . $t->record_id) }}?print=1" title="Print receipt"><i class="fa-solid fa-print"></i></a>
                                        @else
                                        <a href="{{ url('/repair-management/' . $t->record_id) }}" title="View ticket"><i class="fa-regular fa-file-lines"></i></a>
                                        <i class="fa-solid fa-print" style="opacity:.4" title="No receipt for repairs"></i>
                                        @endif
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

            <!-- Staff Performance -->
            <section class="transactions-card" id="staff-performance">
                <div class="transactions-header">
                    <div>
                        <h2>Staff Performance</h2>

                        <p>
                            Individual productivity and total revenue contribution
                        </p>
                    </div>

                    <form action="{{ url('/dashboard') }}#staff-performance" method="GET" class="filters" style="width: auto;">
                        <div class="filter-dropdown">
                            <select name="staff_role" onchange="this.form.submit()">
                                <option value="all" {{ $staffRole === 'all' ? 'selected' : '' }}>Role: All</option>
                                <option value="admin" {{ $staffRole === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ $staffRole === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>
                    </form>

                </div>

                <!-- Table -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Repairs Completed</th>
                                <th>Sales</th>
                                <th>Revenue Generated</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($staffPerformance as $staff)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div style="width:38px; height:38px; border-radius:50%; background:#e0e7ff; color:#3730a3; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0;">
                                            {{ collect(explode(' ', trim($staff->name)))->filter()->map(fn ($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('') }}
                                        </div>
                                        <div style="font-weight:600;">{{ $staff->name }}</div>
                                    </div>
                                </td>

                                <td>
                                    <span style="display:inline-block; padding:5px 12px; border-radius:999px; font-size:12px; font-weight:600; background:{{ $staff->role === 'admin' ? '#e0e7ff; color:#3730a3;' : '#dcfce7; color:#15803d;' }}">
                                        {{ $staff->roleLabel() }}
                                    </span>
                                </td>

                                <td>{{ $staff->completed_repairs_count }} device{{ $staff->completed_repairs_count == 1 ? '' : 's' }}</td>
                                <td>{{ $staff->completed_sales_count }} sale{{ $staff->completed_sales_count == 1 ? '' : 's' }}</td>
                                <td>₱{{ number_format($staff->revenue_generated, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">No staff activity found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
            function money(v) {
                return '₱' + Number(v).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            function rangeData(r) {
                return {
                    labels: r.labels,
                    datasets: [
                        { type: 'bar', label: 'Repair Services', data: r.repair, backgroundColor: '#1e293b', borderRadius: 6, order: 2 },
                        { type: 'bar', label: 'Device Accessories', data: r.retail, backgroundColor: '#60a5fa', borderRadius: 6, order: 2 },
                        { type: 'line', label: 'Total Revenue Trajectory', data: r.total, borderColor: '#2563eb', backgroundColor: '#2563eb', borderWidth: 2.5, pointRadius: 4, pointHoverRadius: 6, tension: 0.35, order: 1 }
                    ]
                };
            }
            var chart = new Chart(el, {
                type: 'bar',
                data: rangeData(series.weekly),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function (items) {
                                    var label = items[0].label;
                                    var r = chart.$range;
                                    if (r && r.peakIdx !== null && items[0].dataIndex === r.peakIdx) {
                                        label += ' (Peak Volume)';
                                    }
                                    return label;
                                },
                                label: function (ctx) {
                                    var i = ctx.dataIndex;
                                    var r = chart.$range;
                                    if (ctx.datasetIndex === 2) {
                                        var line = 'Total Rev: ' + money(ctx.parsed.y);
                                        if (i > 0 && r) {
                                            var prev = r.total[i - 1] || 0;
                                            if (prev > 0) {
                                                var pct = Math.round(((r.total[i] - prev) / prev) * 100);
                                                line += ' (' + (pct >= 0 ? '+' : '') + pct + '% vs prev)';
                                            }
                                        }
                                        return line;
                                    }
                                    if (ctx.datasetIndex === 0) {
                                        return 'Repairs: ' + (r ? r.repairCount[i] : 0) + ' fixed · ' + money(ctx.parsed.y);
                                    }
                                    return 'Retail: ' + (r ? r.retailQty[i] : 0) + ' items · ' + money(ctx.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: function (v) { return '₱' + v; } } },
                        x: {
                            ticks: {
                                color: function (c) {
                                    var label = c.tick.label || '';
                                    return label.indexOf('(Peak)') !== -1 ? '#1d4ed8' : '#6b7280';
                                }
                            }
                        }
                    }
                }
            });
            chart.$range = series.weekly;
            document.querySelectorAll('.period-tabs button').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.period-tabs button').forEach(function (b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    var r = series[btn.getAttribute('data-range')] || series.weekly;
                    chart.$range = r;
                    chart.data.labels = r.labels;
                    chart.data.datasets[0].data = r.repair;
                    chart.data.datasets[1].data = r.retail;
                    chart.data.datasets[2].data = r.total;
                    chart.update();
                });
            });
        })();
    </script>


    <script>
        document.querySelectorAll('.menu-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var collapsed = document.body.classList.toggle('nav-collapsed');
                var sidebar = document.querySelector('.sidebar');
                var content = document.querySelector('.main-content');
                if (sidebar) { sidebar.style.display = collapsed ? 'none' : ''; }
                if (content) { content.style.marginLeft = collapsed ? '0px' : ''; }
                setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 320);
            });
        });

        document.querySelectorAll('form').forEach(function (form) {
            if (form.id === 'pos-filter-form') return;
            var searchInput = form.querySelector('input[name="search"]');
            if (!searchInput) return;
            var liveSearchTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(liveSearchTimer);
                liveSearchTimer = setTimeout(function () { form.requestSubmit(); }, 450);
            });
        });
    </script>
</body>

</html>