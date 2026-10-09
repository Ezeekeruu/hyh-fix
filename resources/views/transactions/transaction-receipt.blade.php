<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - HYH FIX</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topbar-user.css') }}">
</head>

<body>

    <div class="dashboard">

        <!-- SIDEBAR -->
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

                <a href="{{ route('transaction.history') }}"
                    class="nav-link active">
                    <i class="fa-regular fa-clipboard"></i>
                    Transaction History
                </a>

                <a href="{{ url('/inventory') }}" class="nav-link">
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

        <main class="main-content">

            <!-- BACK BUTTON -->

            <div class="receipt-header">

                <a href="{{ route('transaction.history') }}"
                    class="btn-back-receipt">

                    <i class="fa-solid fa-arrow-left"></i>
                    Back

                </a>

                <button onclick="window.print()"
                    class="btn-print">

                    <i class="fa-solid fa-print"></i>
                    Print Receipt

                </button>

            </div>

            <!-- RECEIPT PAPER -->

            <div class="receipt-paper">

                <div class="receipt-brand">

                    <h2>HYH FIX</h2>

                    <p>Cellphone Repair & Accessories</p>

                </div>

                <div class="receipt-divider"></div>

                <div class="receipt-meta">

                    <div>
                        <strong>Transaction #</strong>
                        <span>{{ $sale->id }}</span>
                    </div>

                    <div>
                        <strong>Date</strong>
                        <span>{{ $sale->sale_date->format('M d, Y h:i A') }}</span>
                    </div>

                    <div>
                        <strong>Customer</strong>
                        <span>{{ $sale->customer->name ?? 'Walk-in Customer' }}</span>
                    </div>

                    <div>
                        <strong>Payment</strong>
                        <span>{{ $sale->payment_method }}</span>
                    </div>

                    <div>
                        <strong>Served By</strong>
                        <span>{{ $sale->user->name ?? 'Staff' }}</span>
                    </div>

                </div>

                <div class="receipt-divider"></div>

                <table class="receipt-table">

                    <thead>

                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($sale->saleItems as $item)

                        <tr>

                            <td>

                                <div class="receipt-product-name">
                                    {{ $item->product?->product_name ?? '(Removed product)' }}
                                </div>

                                <div class="receipt-product-sku">
                                    SKU: {{ $item->product?->sku ?? 'N/A' }}
                                </div>

                            </td>

                            <td>
                                {{ $item->quantity }}
                            </td>

                            <td>
                                ₱{{ number_format($item->unit_price,2) }}
                            </td>

                            <td>
                                ₱{{ number_format($item->subtotal,2) }}
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

                <div class="receipt-divider"></div>

                <div class="receipt-total">

                    <h3>
                        TOTAL
                    </h3>

                    <h2>
                        ₱{{ number_format($sale->total_amount,2) }}
                    </h2>

                </div>

                <div class="receipt-footer">

                    Thank you for your purchase!

                </div>

            </div>

        </main>

    </div>

    @if(request('print'))
    <script>
        window.addEventListener('load', function () { window.print(); });
    </script>
    @endif

</body>

</html>