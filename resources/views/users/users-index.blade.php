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
                <a href="{{ url('/repair-management') }}" class="nav-link">
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

                <a href="{{ url('/user-management') }}" class="nav-link active">
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
                    <h2>USER MANAGEMENT</h2>
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

            <!-- Table Container Card -->
            <section class="inventory-card">
                <!-- Search, Filter & Action Bar -->
                <div class="inventory-actions-bar">

                    <!-- Combined Filter Form -->
                    <form action="{{ url('/user-management') }}" method="GET" style="display: flex; gap: 10px; align-items: center; width: 100%;">

                        <!-- 1. Search Box -->
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, User ID, or email...">
                            <button type="submit" class="search-btn">
                                Search
                            </button>
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
                        <a href="{{ url('/user-management/add') }}" class="btn-add-product" style="margin-left: auto; text-decoration: none; ">
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
                                        <a href="{{ url('/user-management/' . $user->id . '/edit') }}" class="btn-icon" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ url('/user-management/' . $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this user permanently? This cannot be undone.');">
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
                        Showing <span>{{ $users->firstItem() ?? 0 }}</span>-<span>{{ $users->lastItem() ?? 0 }}</span> of <span>{{ $users->total() }}</span> staff accounts
                    </div>

                    <div class="pagination">
                        {{-- Previous Page Button --}}
                        @if ($users->onFirstPage())
                        <button class="page-btn prev" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        @else
                        <a href="{{ $users->previousPageUrl() }}" class="page-btn prev">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                        @endif

                        {{-- Page Numbers --}}
                        <div class="page-numbers">
                            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if ($page == $users->currentPage())
                            <span class="page-btn active">{{ $page }}</span>
                            @else
                            <a href="{{ $url }}" class="page-btn" style="text-decoration: none;">{{ $page }}</a>
                            @endif
                            @endforeach
                        </div>

                        {{-- Next Page Button --}}
                        @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="page-btn next">
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