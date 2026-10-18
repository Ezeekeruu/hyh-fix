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
                <a href="{{ auth()->check() && auth()->user()->isStaff() ? url('/staff/dashboard') : url('/dashboard') }}" class="nav-link">
                    <i class="fa-solid fa-table-cells-large"></i>
                    Dashboard
                </a>

@if(auth()->check() && auth()->user()->canAccessPos())
                <a href="{{ url('/pos') }}" class="nav-link">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>
@endif

                <p class="nav-group-label">MANAGEMENT</p>
                <a href="{{ url('/repair-management') }}" class="nav-link ">
                    <i class="fa-solid fa-wrench"></i>
                    Repair Management
                </a>

@if(auth()->check() && auth()->user()->canViewTransactions())
                <a href="{{ url('/transaction-history') }}" class="nav-link">
                    <i class="fa-regular fa-clipboard"></i>
                    Transaction History
                </a>
@endif

                <a href="{{ url('/inventory') }}" class="nav-link">
                    <i class="fa-solid fa-box"></i>
                    Inventory
                </a>

@if(auth()->check() && auth()->user()->isAdmin())
                <p class="nav-group-label">ADMIN</p>
                <a href="{{ url('/reports') }}" class="nav-link active">
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
                    <h2>REPORTS</h2>
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

                        <!-- Keep current period/date range when filtering by product or service type -->
                        <input type="hidden" name="filter" value="{{ $filter }}">
                        <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
                        <input type="hidden" name="end_date" value="{{ $endDate->toDateString() }}">
                    </form>

                    <form method="GET" action="{{ url('/reports') }}" id="customRangeForm"
                        class="custom-range-form {{ $filter === 'custom' ? 'show' : '' }} filters-inline">
                        <input type="hidden" name="filter" value="custom">
                        <label class="filter-label">Product</label>
                        <select name="product_id" onchange="this.form.submit()">
                            <option value="" {{ $selectedProduct ? '' : 'selected' }}>All Products</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $selectedProduct && $selectedProduct->id == $p->id ? 'selected' : '' }}>
                                {{ $p->product_name }}
                            </option>
                            @endforeach
                        </select>

                        <label class="filter-label">Service Type</label>
                        <select name="service_type" onchange="this.form.submit()">
                            <option value="" {{ $selectedServiceType ? '' : 'selected' }}>All Service Types</option>
                            @foreach($serviceTypes as $s)
                            <option value="{{ $s->name }}" {{ $selectedServiceType == $s->name ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                            @endforeach
                        </select>

                        <input type="date" name="start_date" required>
                        <span class="date-sep">to</span>
                        <input type="date" name="end_date" required>
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
                        <h5>NET REVENUE</h5>
                        <div class="icon-box success">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">₱{{ number_format($netRevenue, 2) }}</h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>PROFIT</h5>
                        <div class="icon-box success">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                    </div>
                    <h2 class="stat-value">₱{{ number_format($profit, 2) }}</h2>
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

                        <p>Sales comparison of repairs and accessories over the last 8 weeks.</p>
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

                <!-- CHART: repairs vs retail revenue, last 8 weeks (Chart.js) -->
                <div class="chart-placeholder" id="salesOverviewChart" style="position:relative;"><canvas id="salesChart"></canvas></div>
            </section>

            @if($selectedProduct)
            <!-- Product Sales Detail -->
            <section class="analytics-card" style="margin-top: 24px;">
                <div class="analytics-header">
                    <div>
                        <h2>Product Sales — {{ $selectedProduct->product_name }}</h2>
                        <p>{{ $rangeLabel }} · SKU: {{ $selectedProduct->sku }} · Sold {{ $productQtySold }} unit{{ $productQtySold != 1 ? 's' : '' }} · ₱{{ number_format($productSalesAmount, 2) }}</p>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>SALE #</th>
                                <th>CUSTOMER</th>
                                <th class="num">QTY</th>
                                <th class="num">UNIT PRICE</th>
                                <th class="num">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productSales as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->sale_date)->format('M d, Y h:i A') }}</td>
                                <td>#{{ $item->sale_id }}</td>
                                <td>{{ $item->customer_name ?? 'Walk-in' }}</td>
                                <td class="num">{{ number_format($item->quantity) }}</td>
                                <td class="num">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="num">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="empty-row">No completed retail sales for this product in the selected period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            @endif

            @if($selectedServiceType)
            <!-- Service-Type Repair Detail -->
            <section class="analytics-card" style="margin-top: 24px;">
                <div class="analytics-header">
                    <div>
                        <h2>Repair Requests — {{ $selectedServiceType }}</h2>
                        <p>{{ $rangeLabel }} · {{ $repairCount }} request{{ $repairCount != 1 ? 's' : '' }} · ₱{{ number_format($repairAmount, 2) }}</p>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>DATE RECEIVED</th>
                                <th>CUSTOMER</th>
                                <th>DEVICE</th>
                                <th>STATUS</th>
                                <th class="num">FINAL PRICE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repairRecords as $ticket)
                            <tr>
                                <td>{{ $ticket->date_received? \Carbon\Carbon::parse($ticket->date_received)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $ticket->device?->customer?->name ?? 'Walk-in' }}</td>
                                <td>{{ $ticket->device?->brand }} {{ $ticket->device?->model }}</td>
                                <td><span class="status-badge {{ str_replace('_', '-', $ticket->status) }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
                                <td class="num">₱{{ number_format($ticket->final_price ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="empty-row">No repair requests for this service type in the selected period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            @endif

            <!-- Full Detailed Transaction Report -->
            <section class="analytics-card report-detail-section" style="margin-top: 24px;">
                <div class="analytics-header">
                    <div>
                        <h2>All Transactions — {{ $rangeLabel }}</h2>
                        <p>{{ $allTransactions->count() }} total records · Retail sales &amp; repair tickets</p>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="report-table detail-table">
                        <thead>
                            <tr>
                                <th>DATE &amp; TIME</th>
                                <th>TYPE</th>
                                <th>REF #</th>
                                <th>CUSTOMER</th>
                                <th>ITEM / DEVICE</th>
                                <th class="num">QTY</th>
                                <th class="num">AMOUNT</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allTransactions as $txn)
                            <tr>
                                <td>{{ $txn->date_label }}</td>
                                <td>
                                    @if($txn->type === 'retail')
                                    <span style="color:#2563eb; font-weight:600;">Retail</span>
                                    @else
                                    <span style="color:#1d4ed8; font-weight:600;">Repair</span>
                                    @endif
                                </td>
                                <td>{{ $txn->ref }}</td>
                                <td>{{ $txn->customer }}</td>
                                <td>{{ $txn->item }}</td>
                                <td class="num">{{ number_format($txn->qty) }}</td>
                                <td class="num">₱{{ number_format($txn->amount, 2) }}</td>
                                <td><span class="status-badge {{ $txn->status === 'Completed' ? 'in-stock' : 'low-stock' }}">{{ $txn->status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="empty-row">No transactions found in the selected period.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="5">TOTAL</td>
                                <td class="num">{{ number_format($allTransactions->sum('qty')) }}</td>
                                <td class="num">₱{{ number_format($allTransactions->sum('amount'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <!-- Insight Row -->
            <section class="insight-row">
                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-regular fa-calendar"></i></div>
                    <div>
                        <span>Peak Operational Day</span>
                        <h4>{{ $insights['peakDay'] ? $insights['peakDay']['day'] . ' (₱' . number_format($insights['peakDay']['revenue'], 2) . ')' : '—' }}</h4>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-solid fa-tags"></i></div>
                    <div>
                        <span>Top Grossing Category</span>
                        <h4>{{ $insights['topCategory'] ? $insights['topCategory']['name'] . ' (₱' . number_format($insights['topCategory']['revenue'], 2) . ')' : '—' }}</h4>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                    <div>
                        <span>Retail Conversion Rate</span>
                        <h4>{{ number_format($insights['conversion'], 1) }}% (of repair clients)</h4>
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
            var r = @json($weeklySeries);
            function money(v) {
                return '₱' + Number(v).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            new Chart(el, {
                type: 'bar',
                data: {
                    labels: r.labels,
                    datasets: [
                        { type: 'bar', label: 'Repair Services', data: r.repair, backgroundColor: '#1e293b', borderRadius: 6, order: 2 },
                        { type: 'bar', label: 'Device Accessories', data: r.retail, backgroundColor: '#60a5fa', borderRadius: 6, order: 2 },
                        { type: 'line', label: 'Total Revenue Trajectory', data: r.total, borderColor: '#2563eb', backgroundColor: '#2563eb', borderWidth: 2.5, pointRadius: 4, pointHoverRadius: 6, tension: 0.35, order: 1 }
                    ]
                },
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
                                    if (r.peakIdx !== null && items[0].dataIndex === r.peakIdx) {
                                        label += ' (Peak Volume)';
                                    }
                                    return label;
                                },
                                label: function (ctx) {
                                    var i = ctx.dataIndex;
                                    if (ctx.datasetIndex === 2) {
                                        var line = 'Total Rev: ' + money(ctx.parsed.y);
                                        if (i > 0) {
                                            var prev = r.total[i - 1] || 0;
                                            if (prev > 0) {
                                                var pct = Math.round(((r.total[i] - prev) / prev) * 100);
                                                line += ' (' + (pct >= 0 ? '+' : '') + pct + '% vs prev)';
                                            }
                                        }
                                        return line;
                                    }
                                    if (ctx.datasetIndex === 0) {
                                        return 'Repairs: ' + r.repairCount[i] + ' fixed · ' + money(ctx.parsed.y);
                                    }
                                    return 'Retail: ' + r.retailQty[i] + ' items · ' + money(ctx.parsed.y);
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