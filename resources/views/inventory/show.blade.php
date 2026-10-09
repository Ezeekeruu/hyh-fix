<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->product_name }} - HYH FIX</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topbar-user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/master-data.css') }}">
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

                <a href="{{ url('/transaction-history') }}" class="nav-link">
                    <i class="fa-regular fa-clipboard"></i>
                    Transaction History
                </a>

                <a href="{{ url('/inventory') }}" class="nav-link active">
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

            <!-- Back-arrow Header -->
            <div class="form-page-header">
                <a href="{{ route('products.index') }}" class="btn-back" title="Back to Inventory">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2>{{ $product->product_name }}</h2>
                <div class="profile-dropdown" style="margin-left:auto;">
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

            <!-- Detail Card -->
            <div class="form-page-wrapper">
                <section class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">
                            @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->product_name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 10px;">
                            @else
                            <i class="fa-solid fa-box"></i>
                            @endif
                        </div>
                        <div>
                            <h3>{{ $product->product_name }}</h3>
                            <p>SKU: {{ $product->sku }}</p>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <tbody>
                                <tr>
                                    <td style="font-weight: 700; width: 220px;">Category</td>
                                    <td>{{ $product->category?->category_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Supplier</td>
                                    <td>{{ $product->supplier?->supplier_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Cost Price</td>
                                    <td>₱{{ number_format($product->cost_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Selling Price</td>
                                    <td>₱{{ number_format($product->sell_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Stock Quantity</td>
                                    <td>{{ $product->stock_quantity }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Low Stock Threshold</td>
                                    <td>{{ $product->low_stock_threshold }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('products.index') }}" class="btn-cancel">Back to Inventory</a>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn-add-product" style="text-decoration: none;">
                            <i class="fa-solid fa-pen"></i>
                            Edit Product
                        </a>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>

</html>
