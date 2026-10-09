<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - HYH FIX</title>

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

                <a href="{{ route('products.index') }}" class="nav-link active">
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
                <h2>Add New Product</h2>
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

            <!-- Wide Form Page Wrapper -->
            <div class="form-page-wrapper">

                <!-- CARD 1: ADD PRODUCT -->
                <section class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <div>
                            <h3>Product Details</h3>
                            <p>Fill in the information below to create a new product item in your inventory.</p>
                        </div>
                    </div>

                    <form action="{{ route('products.store') }}" method="POST" class="product-form" enctype="multipart/form-data">
                        @csrf

                        <!-- Split Grid: Left (Inputs) | Right (Image Select & Preview) -->
                        <div class="form-grid-layout">

                            <!-- LEFT PANEL: INPUT FIELDS -->
                            <div class="form-left-panel">

                                <!-- Product Name -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="product_name">Product Name</label>
                                        <input type="text" id="product_name" name="product_name" value="{{ old('product_name') }}" placeholder="e.g. iPhone 13 Screen Replacement" required maxlength="150">
                                    </div>
                                </div>

                                <!-- SKU & Category -->
                                <div class="form-row two-col">
                                    <div class="form-group">
                                        <label for="sku">SKU Code</label>
                                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}" placeholder="e.g. SCR-IP13-001" required maxlength="50">
                                    </div>

                                    <div class="form-group">
                                        <label for="category_id">Category</label>
                                        <select id="category_id" name="category_id" required>
                                            <option value="" disabled selected>Select Category</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Supplier & Stock Quantity -->
                                <div class="form-row two-col">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier</label>
                                        <select id="supplier_id" name="supplier_id" required>
                                            <option value="" disabled selected>Select Supplier</option>
                                            @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->supplier_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="stock_quantity">Stock Quantity</label>
                                        <input type="number" min="0" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" placeholder="0" required>
                                    </div>
                                </div>

                                <!-- Low Stock Threshold -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="low_stock_threshold">Low Stock Threshold</label>
                                        <input type="number" min="0" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" placeholder="10" required>
                                        <small style="color:#6b7280; margin-top: 2px;">Status turns Low Stock when stock reaches this number.</small>
                                    </div>
                                </div>

                                <!-- Cost Price & Selling Price -->
                                <div class="form-row two-col">
                                    <div class="form-group">
                                        <label for="cost_price">Cost Price (₱)</label>
                                        <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price') }}" placeholder="0.00" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="sell_price">Selling Price (₱)</label>
                                        <input type="number" step="0.01" min="0" id="sell_price" name="sell_price" value="{{ old('sell_price') }}" placeholder="0.00" required>
                                        <small style="color:#6b7280; margin-top: 4px; display: block;">Recommended (cost + 30%): <strong id="suggest-value">—</strong> <button type="button" id="apply-suggest" style="background:none; border:none; color:#2563eb; font-weight:700; cursor:pointer; font-size:12px;">Apply</button></small>
                                    </div>
                                </div>

                            </div>

                            <!-- RIGHT PANEL: CHOOSE IMAGE & PREVIEW BELOW IT -->
                            <div class="form-right-panel">
                                <div class="form-group">
                                    <label for="image">Choose Product Image</label>
                                    <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp" onchange="previewProductImage(event)">
                                    <small style="color:#6b7280; margin-top: 2px;">Formats: JPG, PNG, WEBP (Max 2MB)</small>
                                </div>

                                <div class="form-group">
                                    <label>Image Preview</label>
                                    <div class="image-preview-container">
                                        <div class="image-preview-placeholder" id="preview-placeholder">
                                            <i class="fa-regular fa-image"></i>
                                            <span>No image selected</span>
                                        </div>
                                        <img id="image-preview" src="" alt="Image preview" style="display:none;">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Validation Errors -->
                        @if ($errors->any())
                        <div style="background-color: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-top: 20px; font-size: 14px;">
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin-top: 6px; margin-left: 20px;">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="form-actions">
                            <a href="{{ route('products.index') }}" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-add-product">
                                <i class="fa-solid fa-plus"></i>
                                Save Product
                            </button>
                        </div>

                    </form>
                </section>

            </div>
        </main>
    </div>

    <script>
        function previewProductImage(event) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('preview-placeholder');
            const file = event.target.files[0];

            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            } else {
                preview.style.display = 'none';
                if (placeholder) placeholder.style.display = 'flex';
            }
        }

        // Recommended selling price: cost + 30%. Hint only — Apply fills it in.
        const PRICE_MARKUP = 1.3;
        function updateSuggestedPrice() {
            const cost = parseFloat(document.getElementById('cost_price').value);
            const label = document.getElementById('suggest-value');
            if (isNaN(cost) || cost < 0) {
                label.textContent = '—';
                return;
            }
            label.textContent = '₱' + (cost * PRICE_MARKUP).toFixed(2);
        }
        document.getElementById('cost_price').addEventListener('input', updateSuggestedPrice);
        document.getElementById('apply-suggest').addEventListener('click', function () {
            const cost = parseFloat(document.getElementById('cost_price').value);
            if (!isNaN(cost) && cost >= 0) {
                document.getElementById('sell_price').value = (cost * PRICE_MARKUP).toFixed(2);
            }
        });
        updateSuggestedPrice();
    </script>
</body>

</html>