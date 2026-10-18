<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock In - HYH FIX</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
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

@if(auth()->check() && auth()->user()->canAccessPos())
                <a href="{{ url('/pos') }}" class="nav-link">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>
@endif

                <p class="nav-group-label">MANAGEMENT</p>
                <a href="{{ url('/repair-management') }}" class="nav-link ">
                    <i class="fa-solid fa-wrench"></i>
                    Repair Management
                </a>

@if(auth()->check() && auth()->user()->canViewTransactions())
                <a href="{{ url('/transaction-history') }}" class="nav-link">
                    <i class="fa-regular fa-clipboard"></i>
                    Transaction History
                </a>
@endif

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
                <h2>Stock In</h2>
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

            <!-- Form Container Wrapper -->
            <div class="form-page-wrapper">
                <section class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3>Record Incoming Stock</h3>
                            <p>Add received quantity. The purchase price blends into the cost as a moving average, and the movement is logged for the product detail report.</p>
                        </div>
                    </div>

                    <form action="{{ route('products.stockIn') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select id="product_id" name="product_id" required onchange="showSelectedStock()">
                                <option value="" disabled {{ old('product_id', $selected?->id) ? '' : 'selected' }}>Select Product</option>
                                @foreach($products as $product)
                                <option
                                    value="{{ $product->id }}"
                                    data-stock="{{ $product->stock_quantity }}"
                                    data-cost="{{ $product->cost_price }}"
                                    {{ (string) old('product_id', $selected?->id) === (string) $product->id ? 'selected' : '' }}>
                                    {{ $product->product_name }} ({{ $product->sku }})
                                </option>
                                @endforeach
                            </select>
                            <small id="stock-hint" style="color:#6b7280; margin-top: 4px; display: block;"></small>
                        </div>

                        <div class="form-row two-col">
                            <div class="form-group">
                                <label for="quantity">Quantity Received</label>
                                <input
                                    type="number"
                                    min="1"
                                    id="quantity"
                                    name="quantity"
                                    value="{{ old('quantity') }}"
                                    placeholder="0"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="unit_cost">Unit Cost (₱)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    id="unit_cost"
                                    name="unit_cost"
                                    value="{{ old('unit_cost') }}"
                                    placeholder="0.00"
                                    required>
                                <small style="color:#6b7280; margin-top: 4px; display: block;">Same product at a different price? Enter this batch's price — the average updates itself.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <input
                                type="text"
                                id="notes"
                                name="notes"
                                value="{{ old('notes') }}"
                                placeholder="e.g. PO-1024 from Apex Tech Supplies"
                                maxlength="255">
                        </div>

                        <!-- Validation Errors -->
                        @if ($errors->any())
                        <div class="form-error-box" style="margin-top: 20px; padding: 14px; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; color: #dc2626; font-size: 13px;">
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin-top: 8px; margin-left: 18px;">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="form-actions">
                            <a href="{{ route('products.index') }}" class="btn-cancel">
                                <i class="fa-solid fa-xmark"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn-add-product">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                Add Stock
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <script>
        function showSelectedStock() {
            var select = document.getElementById('product_id');
            var option = select.options[select.selectedIndex];
            var hint = document.getElementById('stock-hint');
            if (option && option.value) {
                hint.textContent = 'On hand: ' + option.getAttribute('data-stock') + ' pcs · Current avg cost: ₱' + Number(option.getAttribute('data-cost')).toFixed(2);
            } else {
                hint.textContent = '';
            }
        }
        showSelectedStock();
    </script>

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
    </script>
</body>

</html>
