<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HYH FIX POS</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">
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

                <a href="{{ url('/pos') }}" class="nav-link active">
                    <i class="fa-solid fa-cash-register"></i>
                    POS
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-regular fa-clipboard"></i>
                    Transaction History
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-box"></i>
                    Inventory
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-chart-column"></i>
                    Reports
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-users"></i>
                    User Management
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="#" class="nav-link logout">
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

                <button class="menu-btn">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <h2>POS</h2>

            </div>

            <div class="topbar-right">

                <button class="notification-btn">
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

            <!--  PRODUCTS PANEL  -->
            <section class="products-panel">

                <!-- SEARCH -->
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search products or services...">
                </div>

                <!-- CATEGORY TABS -->
                <div class="category-tabs">
                    <button class="active">All Items</button>
                    <button>Accessories</button>
                    <button>Phone Cases</button>
                    <button>Computer Accessories</button>
                    <button>Repair Services</button>
                </div>

                <!-- PRODUCT GRID -->
                <div class="product-grid">

                    <!-- Product Card 1 -->
                    <div class="product-card">
                        <div class="product-top">
                            <span class="product-tag"></span>
                            <span class="stock-tag"></span>
                        </div>
                        <div class="product-image"></div>
                        <div class="product-info">
                            <h4></h4>
                            <p></p>
                        </div>
                        <div class="product-bottom">
                            <div class="product-price">
                                <span></span>
                                <strong></strong>
                            </div>
                            <button class="add-btn">
                                <i class="fa-solid fa-plus"></i>
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 2 -->
                    <div class="product-card">
                        <div class="product-top">
                            <span class="product-tag"></span>
                            <span class="stock-tag"></span>
                        </div>
                        <div class="product-image"></div>
                        <div class="product-info">
                            <h4></h4>
                            <p></p>
                        </div>
                        <div class="product-bottom">
                            <div class="product-price">
                                <span></span>
                                <strong></strong>
                            </div>
                            <button class="add-btn">
                                <i class="fa-solid fa-plus"></i>
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 3 -->
                    <div class="product-card">
                        <div class="product-top">
                            <span class="product-tag"></span>
                            <span class="stock-tag warning"></span>
                        </div>
                        <div class="product-image"></div>
                        <div class="product-info">
                            <h4></h4>
                            <p></p>
                        </div>
                        <div class="product-bottom">
                            <div class="product-price">
                                <span></span>
                                <strong></strong>
                            </div>
                            <button class="add-btn">
                                <i class="fa-solid fa-plus"></i>
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 4 -->
                    <div class="product-card">
                        <div class="product-top">
                            <span class="product-tag"></span>
                            <span class="stock-tag"></span>
                        </div>
                        <div class="product-image"></div>
                        <div class="product-info">
                            <h4></h4>
                            <p></p>
                        </div>
                        <div class="product-bottom">
                            <div class="product-price">
                                <span></span>
                                <strong></strong>
                            </div>
                            <button class="add-btn">
                                <i class="fa-solid fa-plus"></i>
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 5 -->
                    <div class="product-card">
                        <div class="product-top">
                            <span class="product-tag"></span>
                            <span class="stock-tag"></span>
                        </div>
                        <div class="product-image"></div>
                        <div class="product-info">
                            <h4></h4>
                            <p></p>
                        </div>
                        <div class="product-bottom">
                            <div class="product-price">
                                <span></span>
                                <strong></strong>
                            </div>
                            <button class="add-btn">
                                <i class="fa-solid fa-plus"></i>
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 6 -->
                    <div class="product-card">
                        <div class="product-top">
                            <span class="product-tag"></span>
                            <span class="stock-tag"></span>
                        </div>
                        <div class="product-image"></div>
                        <div class="product-info">
                            <h4></h4>
                            <p></p>
                        </div>
                        <div class="product-bottom">
                            <div class="product-price">
                                <span></span>
                                <strong></strong>
                            </div>
                            <button class="add-btn">
                                <i class="fa-solid fa-plus"></i>
                                Add
                            </button>
                        </div>
                    </div>

                </div>

            </section>

            <!-- ORDER PANEL  -->
            <aside class="order-panel">

                <!-- ORDER HEADER -->
                <div>
                    <div class="order-header">
                        <div class="order-title">
                            <h3>Order #</h3>
                            <span class="order-badge">Active</span>
                        </div>
                        <div class="order-actions">
                            <button class="icon-btn"><i class="fa-regular fa-clock"></i></button>
                            <button class="icon-btn"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>
                    <p class="order-meta">Register — Cashier</p>
                </div>

                <!-- CUSTOMER BOX -->
                <div class="customer-box">
                    <div class="customer-box-header">
                        <span>CUSTOMER ATTACHED</span>
                        <a href="#">Change</a>
                    </div>
                    <div class="customer-info">
                        <div class="customer-avatar"></div>
                        <div class="customer-details">
                            <h4></h4>
                            <span></span>
                        </div>
                    </div>
                </div>

                <!-- CART ITEMS-->
                <div class="cart-items">

                    <div class="cart-item">
                        <div class="cart-item-image"></div>
                        <div class="cart-item-info">
                            <h5>TEST PRODUCT</h5>
                            <span>Test Description</span>
                        </div>
                        <div class="qty-control">
                            <button><i class="fa-solid fa-minus"></i></button>
                            <span>0</span>
                            <button><i class="fa-solid fa-plus"></i></button>
                        </div>
                        <button class="cart-remove"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <div class="cart-item">
                        <div class="cart-item-image"></div>
                        <div class="cart-item-info">
                            <h5>TEST PRODUCT</h5>
                            <span>Test Description</span>
                        </div>
                        <div class="qty-control">
                            <button><i class="fa-solid fa-minus"></i></button>
                            <span>0</span>
                            <button><i class="fa-solid fa-plus"></i></button>
                        </div>
                        <button class="cart-remove"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <div class="cart-item">
                        <div class="cart-item-image"></div>
                        <div class="cart-item-info">
                            <h5>TEST PRODUCT</h5>
                            <span>Test Description</span>
                        </div>
                        <div class="qty-control">
                            <button><i class="fa-solid fa-minus"></i></button>
                            <span>0</span>
                            <button><i class="fa-solid fa-plus"></i></button>
                        </div>
                        <button class="cart-remove"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                </div>

                <!-- TOTALS -->
                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span></span>
                    </div>
                    <div class="total-row grand">
                        <span>Total Due</span>
                        <strong></strong>
                    </div>
                    <div class="currency-note">PHP Currency</div>
                </div>

                <!-- TENDER METHOD -->
                <div>
                    <p class="section-label">TENDER METHOD</p>
                    <div class="tender-tabs">
                        <button class="active">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            Cash
                        </button>
                        <button>
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
                        <input type="text" value="0.00">
                    </div>
                    <div class="quick-cash">
                        <button>Exact</button>
                        <button>₱100</button>
                        <button>₱200</button>
                        <button>₱500</button>
                    </div>
                    <div class="change-row">
                        <span>Change to Return:</span>
                        <strong>₱0.00</strong>
                    </div>
                </div>

                <!-- CHECKOUT -->
                <button class="checkout-btn">
                    <i class="fa-regular fa-circle-check"></i>
                    Complete Sale &amp; Print Slip (F12)
                </button>

                <div class="secondary-actions">
                    <button>
                        <i class="fa-regular fa-envelope"></i>
                        Email Receipt
                    </button>
                    <button>
                        <i class="fa-regular fa-file-lines"></i>
                        Draft Invoice
                    </button>
                </div>

            </aside>

        </div>

    </main>

</div>

</body>
</html>