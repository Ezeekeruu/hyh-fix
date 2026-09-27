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
                    </div>
                    <div class="inv-stat-body">
                        <h5>TOTAL PRODUCTS</h5>
                        <div class="inv-stat-value-group">
                            <span class="inv-stat-number">{{ $products->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Items -->
                <div class="inv-stat-card">
                    <div class="inv-stat-top">
                        <div class="inv-icon-box warning">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                    <div class="inv-stat-body">
                        <h5>LOW STOCK ITEMS</h5>
                        <div class="inv-stat-value-group">
                            <span class="inv-stat-number">{{ $products->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5)->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Out of Stock -->
                <div class="inv-stat-card">
                    <div class="inv-stat-top">
                        <div class="inv-icon-box danger">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>
                    </div>
                    <div class="inv-stat-body">
                        <h5>OUT OF STOCK</h5>
                        <div class="inv-stat-value-group">
                            <span class="inv-stat-number">{{ $products->where('stock_quantity', '<=', 0)->count() }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Table Container Card -->
            <section class="inventory-card">
                <!-- Search, Filter & Action Bar -->
                <form action="{{ route('products.index') }}" method="GET" id="filter-form" class="inventory-actions-bar">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by SKU, product name, category, supplier...">
                    </div>

                    <div class="filter-dropdown">
                        <select name="category_id" onchange="document.getElementById('filter-form').submit();">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-dropdown small-select">
                        <select name="status" onchange="document.getElementById('filter-form').submit();">
                            <option value="">All Status</option>
                            <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    <a href="{{ url('/inventory') }}" class="btn-filter-icon" title="Clear filters">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </a>

                    <a href="{{ route('products.create') }}" class="btn-add-product" style="text-decoration: none;">
                        <i class="fa-solid fa-plus"></i>
                        Add Product
                    </a>
                </form>

                <!-- Inventory Table -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>PRODUCT NAME & SUPPLIER</th>
                                <th>CATEGORY</th>
                                <th>IN STOCK</th>
                                <th>SELLING PRICE</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td class="sku-cell">{{ $product->sku }}</td>
                                <td class="product-info-cell">
                                    <div class="product-details">
                                        <div class="product-name">{{ $product->product_name }}</div>
                                        <div class="product-spec">Supplier: {{ $product->supplier->supplier_name ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge">{{ $product->category->category_name ?? 'N/A' }}</span>
                                </td>
                                <td class="stock-cell">{{ $product->stock_quantity }}</td>
                                <td class="price-cell">₱{{ number_format($product->sell_price, 2) }}</td>
                                <td>
                                    @if($product->stock_quantity <= 0)
                                        <span class="status-badge out-of-stock">Out of Stock</span>
                                        @elseif($product->stock_quantity <= 5)
                                            <span class="status-badge low-stock">Low Stock</span>
                                            @else
                                            <span class="status-badge in-stock">In Stock</span>
                                            @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('products.show', $product->id) }}" class="btn-icon" title="View"><i class="fa-regular fa-eye"></i></a>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-icon danger" type="submit" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">No products found in the inventory.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer / Pagination -->
                <div class="table-footer">
                    <div class="pagination-info">
                        Showing <span>{{ $products->count() > 0 ? 1 : 0 }}</span>-<span>{{ $products->count() }}</span> of <span>{{ $products->count() }}</span> items
                    </div>

                    <div class="pagination">
                        <button class="page-btn prev" type="button"><i class="fa-solid fa-chevron-left"></i></button>
                        <div class="page-numbers">
                            <!-- Page number buttons -->
                        </div>
                        <button class="page-btn next" type="button"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>