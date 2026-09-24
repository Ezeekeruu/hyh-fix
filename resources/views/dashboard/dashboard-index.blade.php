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
                            <span>Manager</span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <section class="welcome-section">
                <div>
                    <h1>Welcome back, Sonayah!</h1>
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
                    <h2 class="stat-value"></h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>TOTAL TRANSACTIONS</h5>
                        <div class="icon-box">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                    </div>
                    <h2 class="stat-value"></h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>LOW STOCK ITEMS</h5>
                        <div class="icon-box warning">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                    <h2 class="stat-value"></h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>PENDING REPAIRS</h5>
                        <div class="icon-box">
                            <i class="fa-solid fa-toolbox"></i>
                        </div>
                    </div>
                    <h2 class="stat-value"></h2>
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
                            <button>Daily</button>
                            <button class="active">Weekly</button>
                            <button>Monthly</button>
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

                    <!-- CHART PLACEHOLDER -->
                    <div class="chart-placeholder"></div>

                    <!-- BOTTOM METRICS -->
                    <div class="analytics-bottom">
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fa-solid fa-mobile-screen"></i>
                            </div>

                            <div>
                                <span>Top Repair Service</span>
                                <h4></h4>
                            </div>
                        </div>

                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="fa-solid fa-plug-circle-bolt"></i>
                            </div>

                            <div>
                                <span>Top Retail Accessory</span>
                                <h4></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LOW STOCK -->
                <div class="low-stock-card">
                    <div class="low-stock-header">
                        <h2>Low Stock Products</h2>
                        <button>Manage</button>
                    </div>

                    <div class="stock-list">
                        <div class="stock-item">
                            <div class="stock-image"></div>

                            <div class="stock-info">
                                <h4></h4>
                                <span></span>
                            </div>

                            <div class="stock-badge low"></div>
                        </div>

                        <div class="stock-item">
                            <div class="stock-image"></div>

                            <div class="stock-info">
                                <h4></h4>
                                <span></span>
                            </div>

                            <div class="stock-badge critical"></div>
                        </div>

                        <div class="stock-item">
                            <div class="stock-image"></div>

                            <div class="stock-info">
                                <h4></h4>
                                <span></span>
                            </div>
                            <div class="stock-badge critical"></div>
                        </div>

                        <div class="stock-item">
                            <div class="stock-image"></div>

                            <div class="stock-info">
                                <h4></h4>
                                <span></span>
                            </div>

                            <div class="stock-badge low"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Transactions -->
            <section class="transactions-card">
                <div class="transactions-header">
                    <div>
                        <h2>Recent Transactions</h2>

                        <p>
                            Combined Point of Sale checkouts and completed repair work orders
                        </p>
                    </div>

                    <!-- Filters -->
                    <div class="filters">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" placeholder="Filter customer, ticket...">
                        </div>

                        <select>
                            <option>Status: All</option>
                        </select>

                        <select>
                            <option>Payment: All</option>
                        </select>

                        <button class="filter-btn">
                            <i class="fa-solid fa-filter"></i>
                            Filter
                        </button>
                    </div>

                </div>

                <!-- Table -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Customer & Device</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Timestamp</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td><span class="tag"></span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><span class="status"></span></td>
                                <td>
                                    <div class="actions">
                                        <i class="fa-regular fa-file-lines"></i>
                                        <i class="fa-solid fa-print"></i>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td></td>
                                <td><span class="tag"></span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><span class="status"></span></td>
                                <td>
                                    <div class="actions">
                                        <i class="fa-regular fa-file-lines"></i>
                                        <i class="fa-solid fa-print"></i>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td></td>
                                <td><span class="tag"></span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><span class="status"></span></td>
                                <td>
                                    <div class="actions">
                                        <i class="fa-regular fa-file-lines"></i>
                                        <i class="fa-solid fa-print"></i>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <button>Prev</button>

                    <div class="pages">
                        <button class="active">1</button>
                        <button>2</button>
                        <button>3</button>
                    </div>
                    <button>Next</button>
                </div>

                <div class="pagination-info">
                    Showing <strong>1–3</strong> out of <strong>9</strong> transactions
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

</body>
</html>