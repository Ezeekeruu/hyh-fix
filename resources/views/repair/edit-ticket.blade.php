<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Repair Ticket #{{ $repairTicket->id }} - HYH FIX</title>

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
                <a href="{{ url('/repair-management') }}" class="nav-link active">
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
                <a href="{{ route('repair-tickets.show', $repairTicket->id) }}" class="btn-back" title="Back to Ticket">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2>Edit Repair Ticket #{{ $repairTicket->id }}</h2>
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
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h3>Update Repair Ticket</h3>
                            <p>Service: <strong>{{ $repairTicket->service_type }}</strong> (service type cannot be changed here).</p>
                        </div>
                    </div>

                    <form action="{{ route('repair-tickets.update', $repairTicket->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-row two-col">
                            <div class="form-group">
                                <label for="device_id">Device</label>
                                <select id="device_id" name="device_id" required>
                                    @foreach($devices as $device)
                                    <option value="{{ $device->id }}" {{ old('device_id', $repairTicket->device_id) == $device->id ? 'selected' : '' }}>
                                        {{ $device->brand }} {{ $device->model }} ({{ $device->customer?->name ?? 'Walk-in' }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="assigned_to">Assigned Technician</label>
                                <select id="assigned_to" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $repairTicket->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="problem_description">Problem Description</label>
                                <input type="text" id="problem_description" name="problem_description" value="{{ old('problem_description', $repairTicket->problem_description) }}" placeholder="Describe the issue" required>
                            </div>
                        </div>

                        <div class="form-row two-col">
                            <div class="form-group">
                                <label for="quotation_price">Quotation Price (₱)</label>
                                <input type="number" step="0.01" min="0" id="quotation_price" name="quotation_price" value="{{ old('quotation_price', $repairTicket->quotation_price) }}" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label for="final_price">Final Price (₱)</label>
                                <input type="number" step="0.01" min="0" id="final_price" name="final_price" value="{{ old('final_price', $repairTicket->final_price) }}" placeholder="0.00">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" required>
                                    @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $repairTicket->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row two-col">
                            <div class="form-group">
                                <label for="date_received">Date Received</label>
                                <input type="datetime-local" id="date_received" name="date_received" value="{{ old('date_received', $repairTicket->date_received ? \Carbon\Carbon::parse($repairTicket->date_received)->format('Y-m-d\TH:i') : '') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="date_completed">Date Completed</label>
                                <input type="datetime-local" id="date_completed" name="date_completed" value="{{ old('date_completed', $repairTicket->date_completed ? \Carbon\Carbon::parse($repairTicket->date_completed)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('repair-tickets.show', $repairTicket->id) }}" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-add-product">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Save Changes
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
