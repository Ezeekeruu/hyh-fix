<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - HYH FIX</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
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

                <a href="{{ url('/inventory') }}" class="nav-link active">
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
                    <h2>INVENTORY</h2>
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

            <!-- Stat Summary Cards -->
            <section class="inventory-stats-grid">
                <!-- Total Products -->
                <div class="inv-stat-card">
                    <div class="inv-stat-top">
                        <div class="inv-icon-box blue">
                            <i class="fa-solid fa-cubes"></i>
                        </div>
                        <span class="inv-pill positive"></span>
                    </div>
                    <div class="inv-stat-body">
                        <h5>TOTAL PRODUCTS</h5>
                        <div class="inv-stat-value-group">
                            <span class="inv-stat-number"></span>
                            <span class="inv-stat-sub"></span>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Items -->
                <div class="inv-stat-card">
                    <div class="inv-stat-top">
                        <div class="inv-icon-box warning">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <span class="inv-pill warning"></span>
                    </div>
                    <div class="inv-stat-body">
                        <h5>LOW STOCK ITEMS</h5>
                        <div class="inv-stat-value-group">
                            <span class="inv-stat-number"></span>
                            <span class="inv-stat-sub danger-text"></span>
                        </div>
                    </div>
                </div>

                <!-- Out of Stock -->
                <div class="inv-stat-card">
                    <div class="inv-stat-top">
                        <div class="inv-icon-box danger">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>
                        <span class="inv-pill danger"></span>
                    </div>
                    <div class="inv-stat-body">
                        <h5>OUT OF STOCK</h5>
                        <div class="inv-stat-value-group">
                            <span class="inv-stat-number"></span>
                            <span class="inv-stat-sub"></span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Table Container Card -->
            <section class="inventory-card">
                <!-- Search, Filter & Action Bar -->
                <div class="inventory-actions-bar">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search by SKU, product name, category, supplier...">
                    </div>

                    <div class="filter-dropdown">
                        <select>
                            <option>All Categories (All)</option>
                        </select>
                    </div>

                    <div class="filter-dropdown small-select">
                        <select>
                            <option>Status</option>
                        </select>
                    </div>

                    <button class="btn-filter-icon" type="button">
                        <i class="fa-solid fa-sliders"></i>
                    </button>

                    <button class="btn-add-product" type="button">
                        <i class="fa-solid fa-plus"></i>
                        Add Product
                    </button>
                </div>

                <!-- Inventory Table -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>PRODUCT NAME & COMPATIBILITY</th>
                                <th>CATEGORY</th>
                                <th>IN STOCK</th>
                                <th>SELLING PRICE</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Item Row Structure -->
                            <tr>
                                <td class="sku-cell"></td>
                                <td class="product-info-cell">
                                    <div class="product-thumb"></div>
                                    <div class="product-details">
                                        <div class="product-name"></div>
                                        <div class="product-spec"></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge"></span>
                                </td>
                                <td class="stock-cell"></td>
                                <td class="price-cell"></td>
                                <td>
                                    <span class="status-badge"></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" type="button" title="View"><i class="fa-regular fa-eye"></i></button>
                                        <button class="btn-icon" type="button" title="Edit"><i class="fa-solid fa-pen"></i></button>
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
                        Showing <span></span>-<span></span> of <span></span> items
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