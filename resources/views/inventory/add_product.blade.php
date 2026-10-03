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

            <!-- Back-arrow Header -->
            <div class="form-page-header">
                <a href="{{ route('products.index') }}" class="btn-back" title="Back to Inventory">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2>Add New Product</h2>
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

                                <!-- Cost Price & Selling Price -->
                                <div class="form-row two-col">
                                    <div class="form-group">
                                        <label for="cost_price">Cost Price (₱)</label>
                                        <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price') }}" placeholder="0.00" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="sell_price">Selling Price (₱)</label>
                                        <input type="number" step="0.01" min="0" id="sell_price" name="sell_price" value="{{ old('sell_price') }}" placeholder="0.00" required>
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

                <!-- CARD 2: ADD CATEGORY, SERVICE TYPE & SUPPLIER -->
                <section class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div>
                            <h3>Add Category, Service Type & Supplier</h3>
                            <p>Quickly register new categories, service types, or suppliers to make them immediately available for selection above.</p>
                        </div>
                    </div>

                    <!-- 3-Column Grid for Quick Add Sub-forms -->
                    <div class="form-row three-col">

                        <!-- 1. ADD CATEGORY -->
                        <form action="{{ route('categories.store') }}" method="POST" class="sub-form-card">
                            @csrf
                            <div class="sub-form-header">
                                <i class="fa-solid fa-tags"></i>
                                <h4>New Category</h4>
                            </div>

                            <div class="form-group">
                                <label for="category_name">Category Name</label>
                                <input type="text" id="category_name" name="category_name" placeholder="e.g. Battery Replacement" required maxlength="100">
                            </div>

                            <div class="sub-form-actions">
                                <button type="submit" class="btn-add-product">
                                    <i class="fa-solid fa-plus"></i>
                                    Save Category
                                </button>
                            </div>
                        </form>

                        <!-- 2. ADD SERVICE TYPE (NEW) -->
                        <form action="{{ route('service-types.store') }}"
                            method="POST"
                            class="sub-form-card">
                            @csrf

                            <div class="sub-form-header">
                                <i class="fa-solid fa-wrench"></i>
                                <h4>New Service Type</h4>
                            </div>

                            <div class="form-group">
                                <label>Service Type Name</label>
                                <input type="text"
                                    name="name"
                                    required
                                    maxlength="100"
                                    placeholder="Enter Service Type">
                            </div>

                            <div class="sub-form-actions">
                                <button type="submit" class="btn-add-product">
                                    <i class="fa-solid fa-plus"></i>
                                    Save Service
                                </button>
                            </div>
                        </form>

                        <!-- 3. ADD SUPPLIER -->
                        <form action="{{ route('suppliers.store') }}" method="POST" class="sub-form-card">
                            @csrf
                            <div class="sub-form-header">
                                <i class="fa-solid fa-truck-field"></i>
                                <h4>New Supplier</h4>
                            </div>

                            <div class="form-group">
                                <label for="supplier_name">Supplier Name</label>
                                <input type="text" id="supplier_name" name="supplier_name" placeholder="e.g. Apex Tech Supplies" required maxlength="100">
                            </div>

                            <div class="form-group">
                                <label for="contact_info">Contact Info</label>
                                <input type="text" id="contact_info" name="contact_info" placeholder="e.g. 0917-123-4567 / sales@apex.com" maxlength="100">
                            </div>

                            <div class="form-group">
                                <label for="location">Location / Address</label>
                                <input type="text" id="location" name="location" placeholder="e.g. Davao City, Philippines" maxlength="255">
                            </div>

                            <div class="sub-form-actions">
                                <button type="submit" class="btn-add-product">
                                    <i class="fa-solid fa-plus"></i>
                                    Save Supplier
                                </button>
                            </div>
                        </form>

                    </div>
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
    </script>
</body>

</html>