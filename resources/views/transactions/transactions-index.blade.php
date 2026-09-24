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

                <a href="{{ url('/transaction-history') }}" class="nav-link active">
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
                    <h2>TRANSACTION HISTORY</h2>
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

            <!-- Search and Filter Section -->
            <section class="filter-section">
                <div class="filter-row">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search by Transaction ID (#TX-), customer, phone, IMEI...">
                    </div>

                    <div class="filter-dropdown date-picker">
                        <i class="fa-regular fa-calendar"></i>
                        <select>
                            <option></option>
                        </select>
                    </div>

                    <div class="filter-dropdown">
                        <select>
                            <option>All Payment Methods</option>
                        </select>
                    </div>

                    <div class="filter-dropdown">
                        <select>
                            <option>All Statuses</option>
                        </select>
                    </div>
                </div>

                <div class="active-filters">
                    <span class="filter-label">Showing:</span>
                    <div class="filter-pill">
                        Period: <span></span>
                        <button class="remove-pill" type="button"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <button class="clear-all-btn" type="button">Clear All Filters</button>
                </div>
            </section>

            <!-- Recent Logs Table Card -->
            <section class="logs-card">
                <div class="logs-header">
                    <h3>Recent Logs <span class="badge-count"></span></h3>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>TRANSACTION ID</th>
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
                            <!-- Data Row Template -->
                            <tr>
                                <td class="tx-id"></td>
                                <td class="customer-name"></td>
                                <td class="item-service">
                                    <div class="item-title"></div>
                                    <div class="item-sub"></div>
                                </td>
                                <td class="amount"></td>
                                <td class="payment-method">
                                    <span class="payment-tag">
                                        <i class="fa-regular fa-credit-card"></i>
                                        <span></span>
                                    </span>
                                </td>
                                <td class="date-time">
                                    <div class="date"></div>
                                    <div class="time"></div>
                                </td>
                                <td>
                                    <span class="status-badge"></span>
                                </td>
                                <td>
                                    <button class="btn-action" type="button">
                                        <i class="fa-regular fa-file-lines"></i>
                                        View Receipt
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                <div class="table-footer">
                    <div class="pagination-info">
                        Showing <span></span> to <span></span> of <span></span> entries
                    </div>

                    <div class="pagination">
                        <button class="page-btn prev" type="button"><i class="fa-solid fa-chevron-left"></i></button>
                        <div class="page-numbers">
                            <!-- Add dynamic page buttons here -->
                        </div>
                        <button class="page-btn next" type="button"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>