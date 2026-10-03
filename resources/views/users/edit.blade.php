<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - HYH FIX</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Stylesheet (shared with User Management & Add Page) -->
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
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

                <a href="{{ url('/repair-management') }}" class="nav-link">
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

                <a href="{{ url('/reports') }}" class="nav-link">
                    <i class="fa-solid fa-chart-column"></i>
                    Reports
                </a>

                <a href="{{ url('/user-management') }}" class="nav-link active">
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

            <!-- Back-arrow header -->
            <div class="form-page-header">
                <a href="{{ url('/user-management') }}" class="btn-back" title="Back to User Management">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2>Edit User</h2>
            </div>

            <!-- Centered Form Card -->
            <div class="form-page-wrapper">
                <section class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon edit-icon">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <div>
                            <h3>Update User Details</h3>
                            <p>Modify account information and permissions for <strong>{{ $user->name }}</strong>.</p>
                        </div>
                    </div>

                    <form action="{{ url('/user-management/' . $user->id) }}" method="POST" class="user-form">
                        @csrf
                        @method('PUT')

                        <!-- Row 1: Full Name & Email Address -->
                        <div class="form-row two-col">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input
                                    type="text"
                                    id="full_name"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    placeholder="e.g. Sonayah Faisal"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    placeholder="name@gmail.com"
                                    required>
                            </div>
                        </div>

                        <!-- Row 2: Role & Account Status -->
                        <div class="form-row two-col">
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select id="role" name="role" required>
                                    <option value="" disabled>Select a role</option>
                                    <option value="manager" {{ old('role', $user->role) == 'manager' || old('role', $user->role) == 'admin' ? 'selected' : '' }}>Manager</option>
                                    <option value="secretary" {{ old('role', $user->role) == 'secretary' ? 'selected' : '' }}>Secretary</option>
                                    <option value="sales-clerk" {{ old('role', $user->role) == 'sales-clerk' ? 'selected' : '' }}>Sales Clerk</option>
                                    <option value="technician" {{ old('role', $user->role) == 'technician' ? 'selected' : '' }}>Technician</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="status">Account Status</label>
                                <select id="status" name="status" required>
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="disabled" {{ old('status', $user->status) == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ url('/user-management') }}" class="btn-cancel">
                                <i class="fa-solid fa-xmark"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn-add-product">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Save Changes
                            </button>
                        </div>

                        @if ($errors->any())
                        <div class="form-error-box">
                            <strong>Please fix the following errors:</strong>
                            <ul>
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