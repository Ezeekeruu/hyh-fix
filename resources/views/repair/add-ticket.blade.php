<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Repair Ticket - HYH FIX</title>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Repair Management Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/repair.css') }}">
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

                <a href="{{ route('repair-tickets.index') }}" class="nav-link active">
                    <i class="fa-solid fa-wrench"></i>
                    Repair Management
                </a>

                <a href="{{ url('/transaction-history') }}" class="nav-link">
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

            <!-- Back Header -->
            <div class="form-page-header" style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                <a href="{{ route('repair-tickets.index') }}" class="btn-filter-icon" title="Back to Repair Management">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 style="font-size: 20px; font-weight: 700; color: var(--text-main);">Create New Repair Ticket</h2>
            </div>

            <!-- Form Wrapper -->
            <div class="inventory-card">

                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa-solid fa-wrench"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main);">Repair Registration Details</h3>
                        <p style="font-size: 12px; color: var(--text-muted);">Fill in customer, device, and repair information to create a ticket.</p>
                    </div>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                <div style="background-color: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin-top: 6px; margin-left: 20px;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('repair-tickets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- SECTION 1: CUSTOMER INFORMATION -->
                    <div style="margin-bottom: 28px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-user" style="margin-right: 6px;"></i> Customer Information
                        </h4>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Customer Name <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="e.g. Juan Dela Cruz" required style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none;">
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Phone Number <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="e.g. 09171234567" required style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none;">
                            </div>

                            <div style="grid-column: 1 / -1;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Address</label>
                                <input type="text" name="address" value="{{ old('address') }}" placeholder="e.g. Poblacion, Davao City" style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none;">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: DEVICE INFORMATION -->
                    <div style="margin-bottom: 28px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-mobile-screen-button" style="margin-right: 6px;"></i> Device Information
                        </h4>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Brand <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="brand" value="{{ old('brand') }}" placeholder="e.g. Apple, Samsung, Xiaomi" required style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none;">
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Model <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g. iPhone 13 Pro Max" required style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none;">
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Serial Number / IMEI</label>
                                <input type="text" name="serial_or_imei" value="{{ old('serial_or_imei') }}" placeholder="e.g. 358201091234567" style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none;">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: REPAIR INFORMATION -->
                    <div style="margin-bottom: 28px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-list-check" style="margin-right: 6px;"></i> Repair Information
                        </h4>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Service Type <span style="color: #ef4444;">*</span></label>
                                <select name="service_type" required style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; background: #fff;">
                                    <option value="">Select Service Type</option>

                                    @foreach($serviceTypes as $service)
                                    <option value="{{ $service->name }}"
                                        {{ old('service_type') == $service->name ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Assigned Technician <span style="color: #ef4444;">*</span></label>
                                <select name="assigned_to" required style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; background: #fff;">
                                    <option value="" disabled selected>Select Staff / Technician</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Quotation Price (₱)</label>
                                <input type="number" step="0.01" min="0" name="quotation_price" value="{{ old('quotation_price') }}" placeholder="0.00" style="width: 100%; height: 42px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none;">
                            </div>

                            <div style="grid-column: 1 / -1;">
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Problem Description</label>
                                <textarea name="problem_description" rows="3" placeholder="Describe the device issue in detail..." style="width: 100%; border: 1px solid var(--border-color); border-radius: 8px; padding: 10px 12px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; resize: vertical;">{{ old('problem_description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: DEVICE INTAKE PHOTOS -->
                    <div style="margin-bottom: 28px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-camera" style="margin-right: 6px;"></i> Device Intake Photos
                        </h4>

                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; align-items: start;">
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Upload Device Photos</label>
                                <input type="file" name="photos[]" multiple accept="image/png, image/jpeg, image/webp" onchange="previewPhotos(event)" style="font-size: 13px; color: var(--text-muted);">
                                <small style="display: block; color: var(--text-muted); margin-top: 4px; font-size: 11px;">Select multiple photos. Formats: JPG, PNG, WEBP (Max 5MB each)</small>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">Photos Preview</label>
                                <div id="photo-preview-container" style="display: flex; gap: 10px; flex-wrap: wrap; background: #f8fafc; border: 1px dashed var(--border-color); border-radius: 8px; padding: 12px; min-height: 80px; align-items: center; justify-content: center;">
                                    <span id="preview-placeholder" style="color: var(--text-muted); font-size: 12px;"><i class="fa-regular fa-images" style="margin-right: 6px;"></i> No photos selected yet</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                        <a href="{{ route('repair-tickets.index') }}" style="height: 40px; padding: 0 20px; border-radius: 8px; border: 1px solid var(--border-color); background: #fff; color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; transition: background 0.2s;">
                            Cancel
                        </a>
                        <button type="submit" class="btn-add-product">
                            <i class="fa-solid fa-wrench"></i> Register Repair
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <script>
        function previewPhotos(event) {
            const container = document.getElementById('photo-preview-container');
            const placeholder = document.getElementById('preview-placeholder');
            const files = event.target.files;

            container.innerHTML = '';

            if (files && files.length > 0) {
                Array.from(files).forEach(file => {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.width = '70px';
                    img.style.height = '70px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '6px';
                    img.style.border = '1px solid #e5e7eb';
                    container.appendChild(img);
                });
            } else {
                container.appendChild(placeholder);
            }
        }
    </script>
</body>

</html>