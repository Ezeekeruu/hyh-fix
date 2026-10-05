<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Supplier - HYH FIX</title>

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

                <a href="{{ url('/suppliers') }}" class="nav-link active">
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
                <a href="{{ route('suppliers.index') }}" class="btn-back" title="Back to Suppliers">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2>Add New Supplier</h2>
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

            <!-- Centered Form Card -->
            <div class="form-page-wrapper">
                <section class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">
                            <i class="fa-solid fa-truck-field"></i>
                        </div>
                        <div>
                            <h3>Supplier Details</h3>
                            <p>Fill in the information below to register a new supplier.</p>
                        </div>
                    </div>

                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf

                        <div class="form-row">
                            <div class="form-group">
                                <label for="supplier_name">Supplier Name</label>
                                <input type="text" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" placeholder="e.g. Apex Tech Supplies" required maxlength="100">
                            </div>
                        </div>

                        <div class="form-row two-col">
                            <div class="form-group">
                                <label for="contact_info">Contact Info</label>
                                <input type="text" id="contact_info" name="contact_info" value="{{ old('contact_info') }}" placeholder="e.g. 0917-123-4567 / sales@apex.com" maxlength="100">
                            </div>
                            <div class="form-group">
                                <label for="location">Location / Address</label>
                                <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="e.g. Davao City, Philippines" maxlength="255">
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('suppliers.index') }}" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-add-product">
                                <i class="fa-solid fa-plus"></i>
                                Create Supplier
                            </button>
                        </div>

                        @if ($errors->any())
                        <div style="background-color: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; margin-top: 20px; font-size: 14px;">
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin-top: 6px; margin-left: 20px;">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </form>
                </section>
            </div>
        </main>
    </div>
</body>

</html>
