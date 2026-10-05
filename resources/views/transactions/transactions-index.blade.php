<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - HYH FIX</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topbar-user.css') }}">
</head>

<body>
    <div class="dashboard">

        <!-- Sidebar Navigation -->
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

                <a href="{{ url('/pos') }}" class="nav-link">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>

                <p class="nav-group-label">MANAGEMENT</p>
                <a href="{{ url('/repair-management') }}" class="nav-link ">
                    <i class="fa-solid fa-wrench"></i>
                    Repair Management
                </a>

                <a href="{{ url('/transaction-history') }}" class="nav-link active">
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

        <!-- Main Content Area -->
        <main class="main-content">

            <!-- Top Navigation Bar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-btn" type="button">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>TRANSACTION HISTORY</h2>
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

            <!-- Search and Filter Section -->
            <section class="filter-section">
                <div class="inventory-actions-bar">

                    <!-- Combined Filter Form -->
                    <form action="{{ url('/transaction-history') }}" method="GET" style="display: flex; gap: 8px; align-items: center; width: 100%; flex-wrap: wrap;">

                        <!-- 1. Search Box -->
                        <div class="search-box" style="flex: 1 1 240px; min-width: 200px;">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, SKU, customer, phone...">
                            <button type="submit" class="search-btn">
                                Search
                            </button>
                        </div>

                        <!-- 2. Date Dropdown & Custom Range Inputs -->
                        <div class="filter-dropdown" style="display: flex; gap: 6px; align-items: center;">
                            <select name="date" id="dateFilterSelect" onchange="toggleCustomDateInputs(this.value)">
                                <option value="all" {{ request('date') == 'all' || !request('date') ? 'selected' : '' }}>All Dates</option>
                                <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="this_week" {{ request('date') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="this_month" {{ request('date') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="custom" {{ request('date') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>

                            <!-- Custom Date Inputs -->
                            <div id="customDateInputs"
                                @if(request('date') != 'custom') style="display: none;" @endif>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" style="padding: 6px 8px; border: 1px solid #ccc; border-radius: 6px; font-size: 13px;">
                                <span style="font-weight: 500; font-size: 13px; color: #666;">to</span>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" style="padding: 6px 8px; border: 1px solid #ccc; border-radius: 6px; font-size: 13px;">
                                <button type="submit" class="search-btn" style="padding: 6px 10px; font-size: 13px;">Apply</button>
                            </div>
                        </div>

                        <!-- 3. Payment Method Dropdown (Shorter Width & Text) -->
                        <div class="filter-dropdown" style="max-width: 140px;">
                            <select name="payment_method" onchange="this.form.submit()">
                                <option value="all" {{ request('payment_method') == 'all' || !request('payment_method') ? 'selected' : '' }}>All Methods</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="gcash" {{ request('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                            </select>
                        </div>

                        <!-- 4. Status Dropdown (Shorter Width & Text) -->
                        <div class="filter-dropdown" style="max-width: 130px;">
                            <select name="status" onchange="this.form.submit()">
                                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="voided" {{ request('status') == 'voided' ? 'selected' : '' }}>Voided</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        <!-- 5. Clear Filters Button -->
                        <a href="{{ url('/transaction-history') }}" class="btn-filter-icon" title="Clear filters">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                        </a>

                    </form>

                </div>
            </section>

            <!-- Recent Logs Table Card -->
            <section class="logs-card">
                <div class="logs-header">
                    <h3>
                        Recent Logs
                        <span class="badge-count">
                            {{ $sales->total() }}
                        </span>
                    </h3>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>TRANSACTION ID</th>
                                <th>SKU</th>
                                <th>CUSTOMER</th>
                                <th>ITEM/SERVICE</th>
                                <th>AMOUNT</th>
                                <th>PAYMENT</th>
                                <th>DATE/TIME</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($sales as $sale)

                            <tr>

                                <td class="tx-id">
                                    #{{ $sale->id }}
                                </td>

                                <td class="sku">

                                    @php
                                    $firstItem = $sale->saleItems->first();
                                    @endphp

                                    <div class="sku-title">
                                        {{ $firstItem?->product?->sku ?? 'N/A' }}
                                    </div>

                                    @if($sale->saleItems->count() > 1)

                                    <div class="sku-sub">
                                        +{{ $sale->saleItems->count() - 1 }} others
                                    </div>

                                    @endif

                                </td>

                                <td class="customer-name">
                                    {{ $sale->customer->name ?? 'Walk-in Customer' }}
                                </td>

                                <td class="item-service">

                                    @php
                                    $firstItem = $sale->saleItems->first();
                                    @endphp

                                    <div class="item-title">
                                        {{ $firstItem?->product?->product_name ?? 'N/A' }}
                                    </div>

                                    <div class="item-sub">
                                        {{ $sale->saleItems->count() }} item(s)
                                    </div>

                                </td>

                                <td class="amount">
                                    ₱{{ number_format($sale->total_amount, 2) }}
                                </td>

                                <td class="payment-method">
                                    {{ ucfirst($sale->payment_method) }}
                                </td>

                                <td class="date-time">
                                    <div class="date">
                                        {{ $sale->sale_date->format('M d, Y') }}
                                    </div>

                                    <div class="time">
                                        {{ $sale->sale_date->format('h:i A') }}
                                    </div>
                                </td>

                                <td>
                                    <span class="status-badge">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>

                                <td>

                                    <a
                                        href="{{ route('sales.show', $sale->id) }}"
                                        class="btn-action">
                                        View Receipt
                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="8" style="text-align:center;">
                                    No transactions found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                <div class="table-footer">
                    <div class="pagination-info">
                        Showing
                        <span>{{ $sales->firstItem() ?? 0 }}</span>
                        to
                        <span>{{ $sales->lastItem() ?? 0 }}</span>
                        of
                        <span>{{ $sales->total() }}</span>
                        entries
                    </div>

                    <div class="pagination">

                        {{-- Previous Button --}}
                        @if($sales->onFirstPage())

                        <button
                            class="page-btn prev disabled"
                            type="button"
                            disabled>
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        @else

                        <a
                            href="{{ $sales->previousPageUrl() }}"
                            class="page-btn prev">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>

                        @endif

                        {{-- Page Numbers --}}
                        <div class="page-numbers">

                            @for($i = 1; $i <= $sales->lastPage(); $i++)

                                <a
                                    href="{{ $sales->url($i) }}"
                                    class="page-btn page-number {{ $sales->currentPage() == $i ? 'active' : '' }}">
                                    {{ $i }}
                                </a>

                                @endfor

                        </div>

                        {{-- Next Button --}}
                        @if($sales->hasMorePages())

                        <a
                            href="{{ $sales->nextPageUrl() }}"
                            class="page-btn next">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                        @else

                        <button
                            class="page-btn next disabled"
                            type="button"
                            disabled>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                        @endif

                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        function toggleCustomDateInputs(value) {
            const customContainer = document.getElementById('customDateInputs');

            if (value === 'custom') {
                customContainer.style.display = 'flex';
            } else {
                customContainer.style.display = 'none';
                // Automatically submit form for preset options
                document.getElementById('dateFilterSelect').form.submit();
            }
        }
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