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
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search by name, username, staff ID, or email...">
                    </div>

                    <div class="filter-dropdown">
                        <select>
                            <option>All Roles</option>
                        </select>
                    </div>

                    <div class="filter-dropdown small-select">
                        <select>
                            <option >Status</option>
                        </select>
                    </div>

                    <button class="btn-filter-icon" type="button" title="Clear filters">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </button>

                    <button class="btn-add-product" type="button">
                        <i class="fa-solid fa-user-plus"></i>
                        Add New User
                    </button>
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
                            <!-- User Row Structure (loop your users here) -->
                            <tr>
                                <td class="user-id-cell"></td>
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar">
                                            <!-- <img src="" alt=""> or initials -->
                                            <span class="presence-dot"></span>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">dsad</div>
                                            <div class="user-email">@</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <!-- Add class: manager | technician | sales-clerk | secretary -->
                                    <span class="role-badge">
                                        Manager
                                    </span>
                                </td>
                                <td>
                                    <!-- Add class: active | inactive -->
                                    <span class="status-badge">
                                        <span class="status-dot"></span>
                                    </span>
                                </td>
                                <td class="joined-cell"></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" type="button" title="View"><i class="fa-regular fa-eye"></i></button>
                                        <button class="btn-icon" type="button" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                        <label class="toggle-switch" title="Activate / Deactivate">
                                            <input type="checkbox">
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <button class="btn-icon danger" type="button" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
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