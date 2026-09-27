<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - HYH FIX</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
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

                <a href="{{ url('/user-management') }}" class="nav-link active">
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
                    <h2>USER MANAGEMENT</h2>
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
                    <form action="{{ url('/user-management') }}" method="GET" style="display: flex; gap: 10px; align-items: center; width: 100%;">

                        <!-- 1. Search Box -->
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search by name, User ID, or email...">
                        </div>

                        <!-- 2. Role Dropdown -->
                        <div class="filter-dropdown">
                            <select name="role" onchange="this.form.submit()">
                                <option value="all" {{ request('role') == 'all' || !request('role') ? 'selected' : '' }}>All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Manager</option>
                                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>

                        <!-- 3. Status Dropdown -->
                        <div class="filter-dropdown small-select">
                            <select name="status" onchange="this.form.submit()">
                                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>

                        <!-- 4. Clear Filters Button -->
                        <a href="{{ url('/user-management') }}" class="btn-filter-icon" title="Clear filters">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                        </a>

                        <!-- 5. Add New User Button -->
                        <a href="{{ url('/user-management/add') }}" class="btn-add-product" style="margin-left: auto;">
                            <i class="fa-solid fa-user-plus"></i>
                            Add New User
                        </a>

                    </form>

                </div>

                <!-- Users Table -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>USER ID</th>
                                <th>FULL NAME</th>
                                <th>ROLE</th>
                                <th>ACCOUNT STATUS</th>
                                <th>JOINED</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="user-id-cell">{{ $user->id }}</td>
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                            <span class="presence-dot {{ $user->status == 'active' ? 'active' : '' }}"></span>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $user->name }}</div>
                                            <div class="user-email">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge {{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $user->status }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="joined-cell">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <div class="action-buttons" style="display: flex; justify-content: center; align-items: center;">
                                        <button class="btn-icon" type="button" title="View"><i class="fa-regular fa-eye"></i></button>
                                        <button class="btn-icon" type="button" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                        <label class="toggle-switch" title="Activate / Deactivate">
                                            <input type="checkbox" {{ $user->status == 'active' ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <button class="btn-icon danger" type="button" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px; color: #6b7280;">
                                    No staff accounts found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer / Pagination -->
                <div class="table-footer">
                    <div class="pagination-info">
                        Showing <span></span>-<span></span> of <span></span> staff accounts
                    </div>

                    <div class="pagination">
                        <button class="page-btn prev" type="button"><i class="fa-solid fa-chevron-left"></i></button>
                        <div class="page-numbers">
                            <!-- Page number buttons will be populated here -->
                        </div>
                        <button class="page-btn next" type="button"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>