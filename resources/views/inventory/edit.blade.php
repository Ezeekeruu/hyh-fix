<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - HYH FIX</title>
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
                <h2>Edit Product</h2>
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
                <!-- SINGLE COMBINED CARD -->
                <section class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon edit-icon">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h3>Update Product Details</h3>
                            <p>Modify inventory information, category, supplier, and stock levels for <strong>{{ $product->product_name }}</strong>.</p>
                        </div>
                    </div>

                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- 1. TOP SECTION: PRODUCT DETAILS & IMAGE -->
                        <div class="form-grid-layout">
                            <!-- Left Panel: Product Information -->
                            <div class="form-left-panel">
                                <!-- Row 1: Product Name & SKU -->
                                <div class="form-row two-col">
                                    <div class="form-group">
                                        <label for="product_name">Product Name</label>
                                        <input
                                            type="text"
                                            id="product_name"
                                            name="product_name"
                                            value="{{ old('product_name', $product->product_name) }}"
                                            placeholder="e.g. iPhone 13 OLED Screen Replacement"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="sku">SKU Code</label>
                                        <input
                                            type="text"
                                            id="sku"
                                            name="sku"
                                            value="{{ old('sku', $product->sku) }}"
                                            placeholder="e.g. SCR-IP13-001"
                                            required>
                                    </div>
                                </div>

                                <!-- Row 2: Cost Price & Selling Price -->
                                <div class="form-row two-col">
                                    <div class="form-group">
                                        <label for="cost_price">Cost Price (₱)</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            id="cost_price"
                                            name="cost_price"
                                            value="{{ old('cost_price', $product->cost_price) }}"
                                            placeholder="0.00"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="sell_price">Selling Price (₱)</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            id="sell_price"
                                            name="sell_price"
                                            value="{{ old('sell_price', $product->sell_price) }}"
                                            placeholder="0.00"
                                            required>
                                        <small style="color:#6b7280; margin-top: 4px; display: block;">Recommended (cost + 30%): <strong id="suggest-value">—</strong> <button type="button" id="apply-suggest" style="background:none; border:none; color:#2563eb; font-weight:700; cursor:pointer; font-size:12px;">Apply</button></small>
                                    </div>
                                </div>

                                <!-- Row 3: Stock Quantity & Low Stock Threshold -->
                                <div class="form-row two-col">
                                    <div class="form-group">
                                        <label for="stock_quantity">Stock Quantity</label>
                                        <input
                                            type="number"
                                            min="0"
                                            id="stock_quantity"
                                            name="stock_quantity"
                                            value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                            placeholder="0"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="low_stock_threshold">Low Stock Threshold</label>
                                        <input
                                            type="number"
                                            min="0"
                                            id="low_stock_threshold"
                                            name="low_stock_threshold"
                                            value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}"
                                            placeholder="10"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Panel: Image Upload & Preview Container -->
                            <div class="form-right-panel">
                                <label>Product Image</label>
                                <div class="image-preview-container">
                                    @if($product->image_path)
                                    <img id="image-preview" src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->product_name }}">
                                    @else
                                    <img id="image-preview" src="" alt="Image Preview" style="display: none;">
                                    <div id="image-placeholder" class="image-preview-placeholder">
                                        <i class="fa-solid fa-image"></i>
                                        <span>No image available</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="image">Change Image (Optional)</label>
                                    <input type="file" id="image" name="image" accept="image/*" onchange="previewSelectedImage(event)">
                                </div>
                            </div>
                        </div>

                        <!-- Divider Line -->
                        <div style="margin: 32px 0 24px;">
                            <hr style="border: 0; border-top: 1px solid var(--border-color);">
                        </div>

                        <!-- 2. BOTTOM SECTION: CATEGORY & SUPPLIER SUB-CARDS -->
                        <div class="form-row two-col">
                            <!-- LEFT SUB-CARD: CATEGORY -->
                            <div class="sub-form-card">
                                <div class="sub-form-header">
                                    <i class="fa-solid fa-tags"></i>
                                    <h4>Category</h4>
                                </div>
                                <div class="form-group">
                                    <label for="category_id">Category Name</label>
                                    <select id="category_id" name="category_id" required>
                                        <option value="" disabled {{ old('category_id', $product->category_id) ? '' : 'selected' }}>Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string) old('category_id', $product->category_id) === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- RIGHT SUB-CARD: SUPPLIER & DETAILS -->
                            <div class="sub-form-card">
                                <div class="sub-form-header">
                                    <i class="fa-solid fa-truck-field"></i>
                                    <h4>Supplier</h4>
                                </div>
                                <div class="form-group">
                                    <label for="supplier_id">Supplier Name</label>
                                    <select id="supplier_id" name="supplier_id" onchange="updateSupplierDetails()" required>
                                        <option value="" disabled {{ old('supplier_id', $product->supplier_id) ? '' : 'selected' }} data-contact="" data-location="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option
                                            value="{{ $supplier->id }}"
                                            data-contact="{{ $supplier->contact_info }}"
                                            data-location="{{ $supplier->location }}"
                                            {{ (string) old('supplier_id', $product->supplier_id) === (string) $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->supplier_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="contact_info">Contact Info</label>
                                    <input
                                        type="text"
                                        id="contact_info"
                                        class="form-control-disabled"
                                        value="{{ $product->supplier ? $product->supplier->contact_info : '' }}"
                                        placeholder="Contact info will appear here"
                                        disabled
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="location">Location / Address</label>
                                    <input
                                        type="text"
                                        id="location"
                                        class="form-control-disabled"
                                        value="{{ $product->supplier ? $product->supplier->location : '' }}"
                                        placeholder="Location will appear here"
                                        disabled
                                        readonly>
                                </div>
                            </div>
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

                        <!-- 3. FORM ACTIONS AT VERY BOTTOM -->
                        <div class="form-actions">
                            <a href="{{ route('products.index') }}" class="btn-cancel">
                                <i class="fa-solid fa-xmark"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn-add-product">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Image preview logic
        function previewSelectedImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('image-preview');
                const placeholder = document.getElementById('image-placeholder');
                output.src = reader.result;
                output.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        // Supplier details updater logic
        function updateSupplierDetails() {
            const select = document.getElementById('supplier_id');
            const selectedOption = select.options[select.selectedIndex];

            const contactInput = document.getElementById('contact_info');
            const locationInput = document.getElementById('location');

            contactInput.value = selectedOption.getAttribute('data-contact') || '';
            locationInput.value = selectedOption.getAttribute('data-location') || '';
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