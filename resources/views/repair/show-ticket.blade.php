<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Ticket #{{ $repairTicket->id }} - HYH FIX</title>

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
                <a href="{{ route('repair-tickets.index') }}" class="btn-back" title="Back to Repair Management">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2>Repair Ticket #{{ $repairTicket->id }}</h2>
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

            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <!-- Ticket summary -->
            <section class="inventory-card">
                <div class="inventory-actions-bar">
                    <div class="master-count">
                        <strong>{{ $repairTicket->service_type }}</strong>
                        &nbsp;·&nbsp; {{ ucfirst(str_replace('_', ' ', $repairTicket->status)) }}
                    </div>

                    <div style="margin-left: auto; display: flex; gap: 10px;">
                        <a href="{{ route('repair-tickets.edit', $repairTicket->id) }}" class="btn-add-product" style="text-decoration: none;">
                            <i class="fa-solid fa-pen"></i>
                            Edit Ticket
                        </a>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <tbody>
                            <tr>
                                <td style="font-weight: 700; width: 220px;">Customer</td>
                                <td>{{ $repairTicket->device?->customer?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Phone</td>
                                <td>{{ $repairTicket->device?->customer?->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Address</td>
                                <td>{{ $repairTicket->device?->customer?->address ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Device</td>
                                <td>{{ trim(($repairTicket->device?->brand ?? '') . ' ' . ($repairTicket->device?->model ?? '')) ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Serial / IMEI</td>
                                <td>{{ $repairTicket->device?->serial_or_imei ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Problem</td>
                                <td>{{ $repairTicket->problem_description ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Assigned To</td>
                                <td>{{ $repairTicket->assignedUser?->name ?? 'Unassigned' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Quotation / Final Price</td>
                                <td>₱{{ number_format($repairTicket->quotation_price ?? 0, 2) }} / ₱{{ number_format($repairTicket->final_price ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Date Received</td>
                                <td>{{ $repairTicket->date_received ? \Carbon\Carbon::parse($repairTicket->date_received)->format('M d, Y h:i A') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700;">Date Completed</td>
                                <td>{{ $repairTicket->date_completed ? \Carbon\Carbon::parse($repairTicket->date_completed)->format('M d, Y h:i A') : '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Status history -->
            <section class="inventory-card">
                <div class="inventory-actions-bar">
                    <div class="master-count"><strong>Status History</strong></div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>STATUS</th>
                                <th>CHANGED BY</th>
                                <th>CHANGED AT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repairTicket->statusHistory as $history)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $history->status)) }}</td>
                                <td>{{ $history->changedBy?->name ?? 'System' }}</td>
                                <td>{{ $history->changed_at ? \Carbon\Carbon::parse($history->changed_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 20px;">No status history.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Intake photos -->
            @if($repairTicket->photos->isNotEmpty())
            <section class="inventory-card">
                <div class="inventory-actions-bar">
                    <div class="master-count"><strong>Intake Photos</strong></div>
                </div>

                <div style="display: flex; gap: 12px; flex-wrap: wrap; padding: 4px 2px;">
                    @foreach($repairTicket->photos as $photo)
                    <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" title="Uploaded by {{ $photo->uploadedBy?->name ?? 'staff' }}">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Repair photo" style="width: 140px; height: 140px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border-color);">
                    </a>
                    @endforeach
                </div>
            </section>
            @endif
        </main>
    </div>
</body>

</html>
