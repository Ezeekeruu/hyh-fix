<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HYH FIX POS</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">
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

                <a href="{{ route('pos.index') }}" class="nav-link active">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>

                <p class="nav-group-label">MANAGEMENT</p>
                <a href="{{ url('/repair-management') }}" class="nav-link ">
                    <i class="fa-solid fa-wrench"></i>
                    Repair Management
                </a>

                <a href="{{ route('transaction.history') }}" class="nav-link">
                    <i class="fa-regular fa-clipboard"></i>
                    Transaction History
                </a>

                <a href="{{ route('products.index') }}" class="nav-link">
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

                <a href="{{ route('users.index') }}" class="nav-link">
                    <i class="fa-solid fa-users"></i>
                    User Management
                </a>
@endif
            </nav>

        </aside>

        <!-- MAIN -->
        <main class="main-content">

            <!-- TOPBAR -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-btn" type="button">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>POS</h2>
                </div>

                <div class="topbar-right">

                    <div class="profile-dropdown">
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
            </header>

            <!-- POS LAYOUT -->
            <div class="pos-layout">

                <!-- PRODUCTS PANEL -->
                <section class="products-panel">

                    <!-- FILTER & SEARCH BAR (Matching User Management Layout & Styling) -->
                    <div class="inventory-actions-bar">
                        <form id="pos-filter-form" onsubmit="event.preventDefault(); filterAndSortProducts();" style="display: flex; gap: 10px; align-items: center; width: 100%;">

                            <!-- 1. Search Box with Search Button -->
                            <div class="search-box">
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text" id="pos-search" placeholder="Search products by name or SKU...">
                                <button type="submit" class="search-btn">
                                    Search
                                </button>
                            </div>

                            <!-- 2. Category Dropdown Filter -->
                            <div class="filter-dropdown">
                                <select id="category-filter" name="category">
                                    <option value="all">All Categories</option>
                                    @if (isset($categories))
                                    @foreach($categories as $category)
                                    <option value="{{ strtolower($category->category_name) }}">{{ $category->category_name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- 3. Price Sorting Dropdown -->
                            <div class="filter-dropdown small-select">
                                <select id="price-sort" name="price_sort">
                                    <option value="default" disabled selected>Sort by Price</option>
                                    <option value="low-high">Price: Low to High</option>
                                    <option value="high-low">Price: High to Low</option>
                                </select>
                            </div>

                            <!-- 4. Filter Clear Button -->
                            <button type="button" class="btn-filter-icon" id="clear-filters-btn" title="Clear filters">
                                <i class="fa-solid fa-filter-circle-xmark"></i>
                            </button>

                        </form>
                    </div>

                    <!-- PRODUCT GRID -->
                    <div class="product-grid" id="product-grid">
                        @if ($products->count() > 0)
                        @foreach($products as $product)
                        <div class="product-card"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->product_name }}"
                            data-price="{{ $product->sell_price }}"
                            data-stock="{{ $product->stock_quantity }}"
                            data-sku="{{ $product->sku }}"
                            data-image="{{ $product->image_url }}"
                            data-category="{{ strtolower($product->category->category_name ?? '') }}">

                            <div class="product-top">
                                <span class="product-tag">
                                    {{ $product->category->category_name ?? 'Item' }}
                                </span>

                                <span class="stock-tag {{ $product->stock_quantity <= 5 ? 'warning' : '' }}">
                                    Stock: {{ $product->stock_quantity }}
                                </span>
                            </div>

                            <div class="product-image">
                                @if($product->image_path)
                                <img src="{{ $product->image_url }}"
                                    alt="{{ $product->product_name }}">
                                @else
                                <div class="no-image">
                                    No Image
                                </div>
                                @endif
                            </div>

                            <div class="product-info">
                                <h4>{{ $product->product_name }}</h4>
                                <p>SKU: {{ $product->sku }}</p>
                            </div>

                            <div class="product-bottom">
                                <div class="product-price">
                                    <span>PHP</span>
                                    <strong>₱{{ number_format($product->sell_price, 2) }}</strong>
                                </div>

                                <button type="button"
                                    class="add-btn"
                                    onclick="addToCartFromCard(this)"> Add
                                   
                                </button>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p style="grid-column: 1 / -1; text-align: center; padding: 20px;">
                            No products available for sale.
                        </p>
                        @endif
                    </div>

                </section>

                <!-- ORDER PANEL -->
                <aside class="order-panel">
                    <form action="{{ route('sales.store') }}" method="POST" id="pos-form">
                        @csrf

                        <!-- ORDER HEADER -->
                        <div>
                            <div class="order-header">
                                <div class="order-title">
                                    <h3>Order</h3>
                                    <span class="order-badge">Active</span>
                                </div>
                                <div class="order-actions">
                                    <button type="button" class="icon-btn" title="Clear Cart" onclick="confirmClearCart()"><i class="fa-regular fa-trash-can"></i></button>
                                </div>
                            </div>
                            <p class="order-meta" style="margin-bottom: 5px;">
                                Register —
                                <span>{{ auth()->check() ? auth()->user()->name : 'Cashier' }}</span>
                            </p>
                        </div>

                        <!-- CUSTOMER BOX -->
                        <div class="customer-box">
                            <div class="customer-box-header">
                                <span>CUSTOMER ATTACHED</span>
                            </div>
                            <div class="customer-info" style="margin-top: 5px;">
                                <select name="customer_id" class="customer-select" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                                    <option value="">Walk-in Customer</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- CART ITEMS CONTAINER -->
                        <div class="cart-items" id="cart-items-container">
                            <p id="empty-cart-msg" style="text-align: center; color: #888; margin-top: 20px;">No items in order.</p>
                        </div>

                        <!-- Hidden inputs for array payload -->
                        <div id="hidden-cart-inputs"></div>

                        <!-- TOTALS -->
                        <div class="totals">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span id="summary-subtotal">₱0.00</span>
                            </div>
                            <div class="total-row grand">
                                <span>Total Due</span>
                                <strong id="summary-total">₱0.00</strong>
                            </div>
                            <div class="currency-note">PHP Currency</div>
                        </div>

                        <!-- TENDER METHOD -->
                        <div>
                            <p class="section-label">TENDER METHOD</p>
                            <input type="hidden" name="payment_method" id="payment-method-input" value="Cash">
                            <div class="tender-tabs">
                                <button type="button" class="tender-btn active" data-method="Cash" onclick="selectTender('Cash', this)">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                    Cash
                                </button>
                                <button type="button" class="tender-btn" data-method="GCash" onclick="selectTender('GCash', this)">
                                    <i class="fa-solid fa-mobile-screen"></i>
                                    GCash
                                </button>
                            </div>
                        </div>

                        <!-- CASH RECEIVED -->
                        <div class="cash-received">
                            <p class="section-label">CASH RECEIVED</p>
                            <div class="cash-input">
                                <span>₱</span>
                                <input type="text" inputmode="decimal" autocomplete="off" id="cash-received-input" value="0.00" oninput="calculateChange()" onfocus="this.select()">
                            </div>
                            <div class="quick-cash">
                                <button type="button" onclick="setQuickCash('exact')">Exact</button>
                                <button type="button" onclick="setQuickCash(100)">₱100</button>
                                <button type="button" onclick="setQuickCash(200)">₱200</button>
                                <button type="button" onclick="setQuickCash(500)">₱500</button>
                            </div>
                            <div class="change-row">
                                <span>Change to Return:</span>
                                <strong id="change-due-display">₱0.00</strong>
                            </div>
                        </div>

                        <!-- CHECKOUT -->
                        <button type="submit" class="checkout-btn" id="submit-sale-btn">
                            <i class="fa-regular fa-circle-check"></i>
                            Complete Sale &amp; Print Slip
                        </button>
                    </form>
                </aside>

            </div>

        </main>

    </div>

    <!-- Notice modal (styled replacement for native alert()) -->
    <div class="notice-overlay" id="notice-overlay">
        <div class="notice-card" role="alertdialog" aria-modal="true">
            <div class="notice-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h3 id="notice-title">Notice</h3>
            <p id="notice-message"></p>
            <button type="button" class="notice-ok" id="notice-ok">OK</button>
        </div>
    </div>


    <!-- Clear-order confirm modal -->
    <div class="notice-overlay" id="clear-overlay">
        <div class="notice-card" role="alertdialog" aria-modal="true">
            <div class="notice-icon"><i class="fa-regular fa-trash-can"></i></div>
            <h3>Clear order?</h3>
            <p>This removes all items from the current order. This cannot be undone.</p>
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 18px;">
                <button type="button" class="notice-ok" id="clear-cancel" style="background: #e5e7eb; color: #111827;">Cancel</button>
                <button type="button" class="notice-ok" id="clear-confirm" style="background: #dc2626;">Clear All</button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        // Styled notice modal (replaces native alert())
        function showNotice(message, title) {
            document.getElementById('notice-title').textContent = title || 'Notice';
            document.getElementById('notice-message').textContent = message;
            document.getElementById('notice-overlay').classList.add('show');
        }

        function closeNotice() {
            document.getElementById('notice-overlay').classList.remove('show');
        }

        document.getElementById('notice-ok').addEventListener('click', closeNotice);
        function confirmClearCart() {
            if (cart.length === 0) {
                showNotice('The order is already empty.', 'Empty Order');
                return;
            }
            document.getElementById('clear-overlay').classList.add('show');
        }
        document.getElementById('clear-cancel').addEventListener('click', function () {
            document.getElementById('clear-overlay').classList.remove('show');
        });
        document.getElementById('clear-confirm').addEventListener('click', function () {
            document.getElementById('clear-overlay').classList.remove('show');
            clearCart();
        });
        document.getElementById('notice-overlay').addEventListener('click', function (e) {
            if (e.target === this) closeNotice();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeNotice();
        });

        function addToCart(id, name, price, stock, image) {
            let item = cart.find(i => i.id === id);
            if (item) {
                if (item.quantity + 1 > stock) {
                    showNotice(`Cannot add more. Only ${stock} items in stock.`, 'Out of Stock');
                    return;
                }
                item.quantity += 1;
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    stock,
                    image,
                    quantity: 1
                });
            }
            renderCart();
        }

        function addToCartFromCard(button) {
            let card = button.closest('.product-card');
            let id = parseInt(card.getAttribute('data-id'));
            let name = card.getAttribute('data-name');
            let price = parseFloat(card.getAttribute('data-price'));
            let stock = parseInt(card.getAttribute('data-stock'));
            let image = card.getAttribute('data-image');

            addToCart(id, name, price, stock, image);
        }

        // Update Quantity
        function updateQty(id, delta) {
            let item = cart.find(i => i.id === id);
            if (!item) return;

            if (item.quantity + delta > item.stock) {
                showNotice(`Cannot exceed available stock of ${item.stock}.`, 'Out of Stock');
                return;
            }

            item.quantity += delta;
            if (item.quantity <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
            renderCart();
        }

        // Remove Item
        function removeFromCart(id) {
            cart = cart.filter(i => i.id !== id);
            renderCart();
        }

        // Set Quantity From Direct Input
        function setQtyFromInput(id, value) {
            let item = cart.find(i => i.id === id);
            if (!item) return;

            let qty = parseInt(value, 10);
            if (isNaN(qty) || qty < 1) qty = 1;

            if (qty > item.stock) {
                showNotice(`Only ${item.stock} in stock. Quantity set to the maximum available.`, 'Out of Stock');
                qty = item.stock;
            }

            item.quantity = qty;
            renderCart();
        }
        // Clear Entire Cart
        function clearCart() {
            cart = [];
            renderCart();
        }

        // Render Cart HTML & Totals
        function renderCart() {
            const container = document.getElementById('cart-items-container');
            const hiddenInputs = document.getElementById('hidden-cart-inputs');

            container.innerHTML = '';
            hiddenInputs.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = '<p id="empty-cart-msg" style="text-align: center; color: #888; margin-top: 20px;">No items in order.</p>';
                document.getElementById('summary-subtotal').innerText = '₱0.00';
                document.getElementById('summary-total').innerText = '₱0.00';
                calculateChange();
                return;
            }

            let total = 0;

            cart.forEach((item, index) => {
                let itemSubtotal = item.price * item.quantity;
                total += itemSubtotal;

                let itemHtml = `
                <div class="cart-item">
                    <div class="cart-item-image">
    ${item.image
        ? `<img src="${item.image}" alt="${item.name}">`
        : ''
    }
</div>
                    <div class="cart-item-info">
                        <h5>${item.name}</h5>
                        <span>₱${item.price.toFixed(2)} each</span>
                    </div>
                    <div class="qty-control">
                        <button type="button" onclick="updateQty(${item.id}, -1)"><i class="fa-solid fa-minus"></i></button>
                        <input type="number" class="qty-input" min="1" max="${item.stock}" value="${item.quantity}" onchange="setQtyFromInput(${item.id}, this.value)" title="Edit quantity">
                        <button type="button" onclick="updateQty(${item.id}, 1)"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    <button type="button" class="cart-remove" onclick="removeFromCart(${item.id})"><i class="fa-solid fa-xmark"></i></button>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', itemHtml);

                let hiddenHtml = `
                <input type="hidden" name="products[${index}][id]" value="${item.id}">
                <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
            `;
                hiddenInputs.insertAdjacentHTML('beforeend', hiddenHtml);
            });

            document.getElementById('summary-subtotal').innerText = `₱${total.toFixed(2)}`;
            document.getElementById('summary-total').innerText = `₱${total.toFixed(2)}`;

            calculateChange();
        }

        // Select Tender Method
        function selectTender(method, element) {
            document.getElementById('payment-method-input').value = method;
            document.querySelectorAll('.tender-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
        }

        // Quick Cash Buttons (accumulate: each tap adds to what's entered)
        function setQuickCash(amount) {
            let cashInput = document.getElementById('cash-received-input');

            if (amount === 'exact') {
                cashInput.value = getCartTotal().toFixed(2);
            } else {
                let raw = cashInput.value.replace(/[^0-9.]/g, '');
                let current = parseFloat(raw) || 0;
                cashInput.value = (current + parseFloat(amount)).toFixed(2);
            }
            calculateChange();
        }

        // Calculate Change Due
        function calculateChange() {
            let total = getCartTotal();
            let raw = document.getElementById('cash-received-input').value.replace(/[^0-9.]/g, '');
            let cashReceived = parseFloat(raw) || 0;
            let change = cashReceived - total;

            const changeDisplay = document.getElementById('change-due-display');
            if (change < 0) {
                changeDisplay.innerText = '₱0.00';
            } else {
                changeDisplay.innerText = `₱${change.toFixed(2)}`;
            }
        }

        // Helper: Compute Grand Total from cart
        function getCartTotal() {
            return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        }

        // Combined Product Search, Category Filter, and Price Sorting
        const searchInput = document.getElementById('pos-search');
        const categoryFilter = document.getElementById('category-filter');
        const priceSort = document.getElementById('price-sort');
        const clearBtn = document.getElementById('clear-filters-btn');
        const productGrid = document.getElementById('product-grid');

        // 1. Capture original product order on page load
        const originalCards = Array.from(document.querySelectorAll('#product-grid .product-card'));

        function filterAndSortProducts() {
            let term = searchInput.value.toLowerCase().trim();
            let selectedCategory = categoryFilter.value.toLowerCase();
            let sortValue = priceSort.value;

            // 2. Work with a fresh copy of the original order
            let cards = [...originalCards];

            cards.forEach(card => {
                let name = (card.getAttribute('data-name') || '').toLowerCase();
                let sku = (card.getAttribute('data-sku') || '').toLowerCase();
                let category = (card.getAttribute('data-category') || '').toLowerCase();

                let matchesSearch = name.includes(term) || sku.includes(term);
                let matchesCategory = (selectedCategory === 'all' || category === selectedCategory);

                if (matchesSearch && matchesCategory) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });

            // 3. Apply sorting or keep default initial order
            if (sortValue === 'low-high') {
                cards.sort((a, b) => parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price')));
            } else if (sortValue === 'high-low') {
                cards.sort((a, b) => parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price')));
            }

            // 4. Re-append in correct order
            cards.forEach(card => productGrid.appendChild(card));
        }

        if (searchInput) searchInput.addEventListener('input', filterAndSortProducts);
        if (categoryFilter) categoryFilter.addEventListener('change', filterAndSortProducts);
        if (priceSort) priceSort.addEventListener('change', filterAndSortProducts);

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                if (categoryFilter) categoryFilter.value = 'all';
                if (priceSort) priceSort.value = 'default';
                filterAndSortProducts();
            });
        }

        // Form submit validation
        document.getElementById('pos-form').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                showNotice('Please add at least one product to the order.', 'Empty Order');
                return;
            }

            let total = getCartTotal();
            let rawCash = document.getElementById('cash-received-input').value.replace(/[^0-9.]/g, '');
            let cashReceived = parseFloat(rawCash) || 0;
            let paymentMethod = document.getElementById('payment-method-input').value;

            if (paymentMethod === 'Cash' && cashReceived < total) {
                e.preventDefault();
                showNotice('Cash received is less than the total due.', 'Insufficient Cash');
            }
        });
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

        document.querySelectorAll('form').forEach(function (form) {
            if (form.id === 'pos-filter-form') return;
            var searchInput = form.querySelector('input[name="search"]');
            if (!searchInput) return;
            var liveSearchTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(liveSearchTimer);
                liveSearchTimer = setTimeout(function () { form.requestSubmit(); }, 450);
            });
        });
    </script>
</body>

</html>