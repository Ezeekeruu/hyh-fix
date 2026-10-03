<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Management - HYH FIX</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/repair.css') }}">
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
                <a href="{{ url('/dashboard') }}" class="nav-link">
                    <i class="fa-solid fa-table-cells-large"></i>
                    Dashboard
                </a>

                <a href="{{ url('/pos') }}" class="nav-link">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>

                <a href="{{ url('/repair-management') }}" class="nav-link active">
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

        <!-- Main Content Area -->
        <main class="main-content">

            <!-- Top Navigation Bar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-btn" type="button">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>REPAIR MANAGEMENT</h2>
                </div>

                <div class="topbar-right">
                    <button class="notification-btn" type="button">
                        <i class="fa-regular fa-bell"></i>
                    </button>

                    <div class="profile">
                        <div class="avatar"></div>
                        <div class="profile-info">
                            <h4>Sonayah Faisal</h4>
                            <span>Manager</span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <!-- Table Container Card -->
            <section class="inventory-card">
                <!-- Search, Filter & Action Bar -->
                <div class="inventory-actions-bar">

                    <!-- Combined Filter Form -->
                    <form action="{{ url('/repair-management') }}" method="GET" style="display: flex; gap: 10px; align-items: center; width: 100%;">

                        <!-- 1. Search Box -->
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ticket #, customer, or device...">
                            <button type="submit" class="search-btn">
                                Search
                            </button>
                        </div>

                        <!-- 2. Status Dropdown -->
                        <div class="filter-dropdown">
                            <select name="status" onchange="this.form.submit()">
                                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <!-- 3. Technician Dropdown -->
                        <div class="filter-dropdown small-select">
                            <select name="technician" onchange="this.form.submit()">
                                <option value="all" {{ request('technician') == 'all' || !request('technician') ? 'selected' : '' }}>All Technicians</option>
                                @foreach($technicians ?? [] as $tech)
                                <option value="{{ $tech->id }}" {{ request('technician') == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Clear Filters Button -->
                        <a href="{{ url('/repair-management') }}" class="btn-filter-icon" title="Clear filters">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                        </a>

                        <!-- 5. Create Repair Ticket Button -->
                        <a href="{{ url('/repair-management/create') }}" class="btn-add-product" style="margin-left: auto; text-decoration: none;">
                            <i class="fa-solid fa-circle-plus"></i>
                            New Ticket
                        </a>

                    </form>

                </div>

                <!-- Repairs Table -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>TICKET #</th>
                                <th>CUSTOMER</th>
                                <th>DEVICE</th>
                                <th>SERVICE TYPE</th>
                                <th>EST. PRICE</th>
                                <th>TECHNICIAN</th>
                                <th>STATUS</th>
                                <th>RECEIVED DATE</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repairs as $repair)
                            <tr>
                                <td class="user-id-cell">#{{ $repair->id }}</td>
                                <td>
                                    <div class="user-details">
                                        <div class="user-name">{{ $repair->device?->customer?->name ?? 'N/A' }}</div>
                                        <div class="user-email">{{ $repair->device?->customer?->phone ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-details">
                                        <div class="user-name">{{ ($repair->device?->brand ?? '') . ' ' . ($repair->device?->model ?? 'N/A') }}</div>
                                        <div class="user-email">{{ $repair->device?->serial_or_imei ?? 'S/N' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge">
                                        {{ $repair->service_type }}
                                    </span>
                                </td>
                                <td>
                                    ₱{{ number_format($repair->quotation_price ?? 0, 2) }}
                                </td>
                                <td>
                                    <span class="role-badge technician">
                                        {{ $repair->assignedUser?->name ?? 'Unassigned' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ str_replace('_', '-', $repair->status) }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst(str_replace('_', ' ', $repair->status)) }}
                                    </span>
                                </td>
                                <td class="joined-cell">
                                    {{ $repair->date_received? \Carbon\Carbon::parse($repair->date_received)->format('M d, Y'): 'N/A'}}
                                </td>
                                <td>
                                    <div class="action-buttons" style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                                        <a href="{{ url('/repair-management/' . $repair->id . '/edit') }}" class="btn-icon" title="Edit Ticket">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ url('/repair-management/' . $repair->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this repair ticket? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-icon danger" type="submit" title="Delete">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px; color: #6b7280;">
                                    No repair tickets found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer / Pagination -->
                <div class="table-footer">
                    <div class="pagination-info">
                        Showing <span>{{ $repairs->firstItem() ?? 0 }}</span>-<span>{{ $repairs->lastItem() ?? 0 }}</span> of <span>{{ $repairs->total() }}</span> tickets
                    </div>

                    <div class="pagination">
                        {{-- Previous Page Button --}}
                        @if ($repairs->onFirstPage())
                        <button class="page-btn prev" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        @else
                        <a href="{{ $repairs->previousPageUrl() }}" class="page-btn prev">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                        @endif

                        {{-- Page Numbers --}}
                        <div class="page-numbers">
                            @foreach ($repairs->getUrlRange(1, $repairs->lastPage()) as $page => $url)
                            @if ($page == $repairs->currentPage())
                            <span class="page-btn active">{{ $page }}</span>
                            @else
                            <a href="{{ $url }}" class="page-btn" style="text-decoration: none;">{{ $page }}</a>
                            @endif
                            @endforeach
                        </div>

                        {{-- Next Page Button --}}
                        @if ($repairs->hasMorePages())
                        <a href="{{ $repairs->nextPageUrl() }}" class="page-btn next">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        @else
                        <button class="page-btn next" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>