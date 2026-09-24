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

                <a href="{{ url('/reports') }}" class="nav-link active">
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
                    <h2>REPORTS</h2>
                </div>

                <div class="topbar-right">
                    <button class="notification-btn">
                        <i class="fa-regular fa-bell"></i>
                    </button>

                    <div class="profile">
                        <div class="avatar"></div>
                        <div class="profile-info">
                            <h4></h4>
                            <span></span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <!-- Date Range + Export -->
            <section class="page-actions">
                <div class="range-tabs">
                    <button>Today</button>
                    <button>This Week</button>
                    <button class="active">This Month</button>
                    <button class="custom-date">
                        <i class="fa-regular fa-calendar"></i>
                        Custom Date
                    </button>
                </div>

                <div class="export-actions">
                    <button class="export-btn pdf">
                        <span class="export-icon"><i class="fa-regular fa-file-pdf"></i></span>
                        Export PDF
                    </button>

                    <button class="export-btn excel">
                        <span class="export-icon"><i class="fa-regular fa-file-excel"></i></span>
                        Export Excel
                    </button>
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
                        <h5>NET REVENUE & PROFIT</h5>
                        <div class="icon-box success">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                    </div>
                    <h2 class="stat-value"></h2>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <h5>REPAIRS COMPLETED</h5>
                        <div class="icon-box">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>
                    </div>
                    <h2 class="stat-value"></h2>
                </div>
            </section>

            <!-- Sales Overview -->
            <section class="analytics-card">

                <div class="analytics-header">
                    <div>
                        <div class="title-row">
                            <h2>Sales Overview</h2>
                        </div>

                        <p>Sales comparison of repairs, and accessories over the last 8 weeks.</p>
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
                        Retail & Parts
                    </div>

                    <div class="legend-item">
                        <span class="dash"></span>
                        Total Revenue Trajectory
                    </div>
                </div>

                <!-- CHART: render with your chart library of choice (Chart.js, ApexCharts, etc.) -->
                <div class="chart-placeholder" id="salesOverviewChart"></div>
            </section>

            <!-- Insight Row -->
            <section class="insight-row">
                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-regular fa-calendar"></i></div>
                    <div>
                        <span>Peak Operational Day</span>
                        <h4></h4>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                    <div>
                        <span>Top Grossing Category</span>
                        <h4></h4>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                    <div>
                        <span>Retail Conversion Rate</span>
                        <h4></h4>
                    </div>
                </div>
            </section>

            <!-- Best-Selling / Slow-Moving -->
            <section class="panel-grid">

                <div class="panel-card">
                    <div class="panel-header">
                        <div class="panel-header-title">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <h2>Best-Selling Retail Products</h2>
                        </div>
                        <button class="panel-link">Full Catalog <i class="fa-solid fa-arrow-right"></i></button>
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
                          
                                <tr>
                                    <td>
                                        <div class="product-cell">
                                            <div class="product-thumb"><i class="fa-solid fa-box"></i></div>
                                           
                                        </div>
                                    </td>
                                    <td class="num"></td>
                                    <td class="num"></td>
                                </tr>
                      
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
                            <div class="panel-header-sub">Stagnant capital exceeding 30+ shelf days</div>
                        </div>
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
                       
                                <tr>
                                    <td>
                                        <div class="product-cell">
                                            <div class="product-thumb alert"><i class="fa-solid fa-box"></i></div>
                                           
                                        </div>
                                    </td>
                                    <td class="num"></td>
                                    <td class="num"></td>
                                </tr>
                         
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Most Requested Repairs / Inventory Status -->
            <section class="panel-grid">

                <div class="panel-card">
                    <div class="panel-header">
                        <div class="panel-header-title">
                            <i class="fa-solid fa-briefcase"></i>
                            <h2>Most Requested Repair Services</h2>
                        </div>
                        <span class="panel-meta"></span>
                    </div>

                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th class="num">Completed</th>
                                <th class="num">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                                <tr>
                                    <td></td>
                                    <td class="num"></td>
                                    <td class="num"></td>
                                </tr>
                           
                        </tbody>
                    </table>
                </div>

                <div class="panel-card">
                    <div class="panel-header">
                        <div class="panel-header-title">
                            <i class="fa-solid fa-box"></i>
                            <h2>Inventory Status</h2>
                        </div>
                    </div>

                    <div class="stock-progress">
                        <span class="in-stock"></span>
                        <span class="low-stock"></span>
                        <span class="out-stock"></span>
                    </div>

                    <div class="stock-summary">
                        <div class="stock-summary-item">
                            <div class="dot-label"><span class="dot green"></span> In Stock</div>
                            <h3></h3>
                        </div>

                        <div class="stock-summary-item low">
                            <div class="dot-label"><span class="dot blue"></span> Low Stock</div>
                            <h3></h3>
                            <small>Needs reorder</small>
                        </div>

                        <div class="stock-summary-item out">
                            <div class="dot-label"><span class="dot red"></span> Out of Stock</div>
                            <h3></h3>
                            <small>Urgent restock</small>
                        </div>
                    </div>

                    <button class="panel-btn">
                        <i class="fa-solid fa-warehouse"></i>
                        Open Inventory Manager
                    </button>
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
                        <span class="panel-meta"></span>
                        <button class="filter-chip">
                            <i class="fa-solid fa-sliders"></i>
                            Filter
                        </button>
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
                            
                                <tr>
                                    <td>
                                        <div class="staff-name">
                                            <div class="staff-avatar"></div>
                                            
                                        </div>
                                    </td>
                                    <td><span class="role-badge"></span></td>
                                    <td class="num"></td>
                                    <td class="num"></td>
                                    <td class="num revenue"></td>
                                </tr>
            
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

</body>
</html>