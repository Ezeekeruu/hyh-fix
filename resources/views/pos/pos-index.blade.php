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

                <a href="{{ route('pos.index') }}" class="nav-link active">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>

                <a href="{{ route('transaction.index') }}" class="nav-link">
                    <i class="fa-regular fa-clipboard"></i>
                    Transaction History
                </a>

                <a href="{{ route('products.index') }}" class="nav-link">
                    <i class="fa-solid fa-box"></i>
                    Inventory
                </a>

                <a href="{{ url('/reports') }}" class="nav-link">
                    <i class="fa-solid fa-chart-column"></i>
                    Reports
                </a>

                <a href="{{ route('users.index') }}" class="nav-link">
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

            <!-- POS LAYOUT -->
            <div class="pos-layout">

                <!-- PRODUCTS PANEL -->
                <section class="products-panel">

                    <!-- SEARCH -->
                    <div class="search-bar">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="pos-search" placeholder="Search products by name or SKU...">
                    </div>

                    <!-- CATEGORY TABS -->
                    <div class="category-tabs" id="category-tabs">
                        <button class="cat-tab active" data-category="all">All Items</button>
                        <!-- Additional categories can be generated dynamically -->
                    </div>

                    <!-- PRODUCT GRID -->
                    <div class="product-grid" id="product-grid">
                        @if($products->count() > 0)
                        @foreach($products as $product)
                        <div class="product-card"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->product_name }}"
                            data-price="{{ $product->sell_price }}"
                            data-stock="{{ $product->stock_quantity }}"
                            data-sku="{{ $product->sku }}">
                            <div class="product-top">
                                <span class="product-tag">{{ $product->category->category_name ?? 'Item' }}</span>
                                <span class="stock-tag {{ $product->stock_quantity <= 5 ? 'warning' : '' }}">
                                    Stock: {{ $product->stock_quantity }}
                                </span>
                            </div>
                            <div class="product-image"></div>
                            <div class="product-info">
                                <h4>{{ $product->product_name }}</h4>
                                <p>SKU: {{ $product->sku }}</p>
                            </div>
                            <div class="product-bottom">
                                <div class="product-price">
                                    <span>PHP</span>
                                    <strong>₱{{ number_format($product->sell_price, 2) }}</strong>
                                </div>
                                <button type="button" class="add-btn" onclick="addToCartFromCard(this)">
                                    <i class="fa-solid fa-plus"></i>
                                    Add
                                </button>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p style="grid-column: 1 / -1; text-align: center; padding: 20px;">No products available for sale.</p>
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
                                    <button type="button" class="icon-btn" title="Clear Cart" onclick="clearCart()"><i class="fa-regular fa-trash-can"></i></button>
                                </div>
                            </div>
                            <p class="order-meta">Register — Cashier</p>
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
                                <input type="number" step="0.01" id="cash-received-input" value="0.00" oninput="calculateChange()">
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

    <script>
        let cart = [];

        function addToCart(id, name, price, stock) {
            let item = cart.find(i => i.id === id);
            if (item) {
                if (item.quantity + 1 > stock) {
                    alert(`Cannot add more. Only ${stock} items in stock.`);
                    return;
                }
                item.quantity += 1;
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    stock,
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

            addToCart(id, name, price, stock);
        }

        // Add Product to Cart
        function addToCartFromCard(button) {
            let card = button.closest('.product-card');
            let id = parseInt(card.getAttribute('data-id'));
            let name = card.getAttribute('data-name');
            let price = parseFloat(card.getAttribute('data-price'));
            let stock = parseInt(card.getAttribute('data-stock'));

            addToCart(id, name, price, stock);
        }

        // Update Quantity
        function updateQty(id, delta) {
            let item = cart.find(i => i.id === id);
            if (!item) return;

            if (item.quantity + delta > item.stock) {
                alert(`Cannot exceed available stock of ${item.stock}.`);
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

                // Render visible item
                let itemHtml = `
                <div class="cart-item">
                    <div class="cart-item-image"></div>
                    <div class="cart-item-info">
                        <h5>${item.name}</h5>
                        <span>₱${item.price.toFixed(2)} each</span>
                    </div>
                    <div class="qty-control">
                        <button type="button" onclick="updateQty(${item.id}, -1)"><i class="fa-solid fa-minus"></i></button>
                        <span>${item.quantity}</span>
                        <button type="button" onclick="updateQty(${item.id}, 1)"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    <button type="button" class="cart-remove" onclick="removeFromCart(${item.id})"><i class="fa-solid fa-xmark"></i></button>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', itemHtml);

                // Render hidden input fields for backend submission
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

        // Quick Cash Buttons
        function setQuickCash(amount) {
            let total = getCartTotal();
            let cashInput = document.getElementById('cash-received-input');

            if (amount === 'exact') {
                cashInput.value = total.toFixed(2);
            } else {
                cashInput.value = parseFloat(amount).toFixed(2);
            }
            calculateChange();
        }

        // Calculate Change Due
        function calculateChange() {
            let total = getCartTotal();
            let cashReceived = parseFloat(document.getElementById('cash-received-input').value) || 0;
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

        // Live Product Search Filter
        document.getElementById('pos-search').addEventListener('input', function(e) {
            let term = e.target.value.toLowerCase();
            let cards = document.querySelectorAll('#product-grid .product-card');

            cards.forEach(card => {
                let name = card.getAttribute('data-name').toLowerCase();
                let sku = card.getAttribute('data-sku').toLowerCase();
                if (name.includes(term) || sku.includes(term)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Form submit validation
        document.getElementById('pos-form').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Please add at least one product to the order.');
                return;
            }

            let total = getCartTotal();
            let cashReceived = parseFloat(document.getElementById('cash-received-input').value) || 0;
            let paymentMethod = document.getElementById('payment-method-input').value;

            if (paymentMethod === 'Cash' && cashReceived < total) {
                e.preventDefault();
                alert('Cash received is less than the total due.');
            }
        });
    </script>

</body>

</html>