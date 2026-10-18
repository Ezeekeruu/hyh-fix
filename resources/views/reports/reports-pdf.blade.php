<!DOCTYPE html>
<html>
<head>
    <title>HYH Fix Report - {{ $rangeLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 20px; margin: 0 0 4px 0; }
        h2 { font-size: 14px; margin: 16px 0 6px; border-bottom: 1px solid #999; padding-bottom: 4px; }
        p.meta { color: #555; margin: 2px 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #bbb; padding: 5px 7px; text-align: left; font-size: 9.5px; }
        th { background: #e5e7eb; }
        td.num, th.num { text-align: right; }
        .cards td { text-align: center; font-size: 13px; }
        .cards small { color: #555; }
        .filter-note { color: #4b5563; font-size: 9.5px; margin: 4px 0 10px 0; }
        .section-title { font-size: 12px; font-weight: 700; margin: 14px 0 4px 0; color: #1f2937; }
        .total-row td { font-weight: 700; border-top: 2px solid #333; }
    </style>
</head>
<body>
    <h1>HYH Fix — Business Report</h1>
    <p class="meta">Period: {{ $rangeLabel }} &nbsp;|&nbsp; Generated: {{ now()->format('M d, Y h:i A') }} &nbsp;|&nbsp; Exported by: {{ $generatedBy ?? 'System' }}</p>

    @php
        $filterParts = [];
        if ($selectedProduct) { $filterParts[] = 'Product: ' . $selectedProduct->product_name; }
        if ($selectedServiceType) { $filterParts[] = 'Service: ' . $selectedServiceType; }
        $filterText = $filterParts ? ' | Filters: ' . implode(' · ', $filterParts) : '';
    @endphp
    <p class="filter-note">Sales comparison of repairs and accessories{{ $filterText }}</p>

    <table class="cards">
        <tr>
            <td><small>Total Sales</small><br><strong>P{{ number_format($totalSales, 2) }}</strong></td>
            <td><small>Total Transactions</small><br><strong>{{ number_format($totalTransactions) }}</strong></td>
            <td><small>Net Revenue</small><br><strong>P{{ number_format($netRevenue, 2) }}</strong></td>
            <td><small>Profit</small><br><strong>P{{ number_format($profit, 2) }}</strong></td>
            <td><small>Repairs Completed</small><br><strong>{{ number_format($repairsCompleted) }}</strong></td>
        </tr>
    </table>

    @if($selectedProduct)
    <h2 class="section-title">Product Sales — {{ $selectedProduct->product_name }}</h2>
    <p class="meta">SKU: {{ $selectedProduct->sku }} &nbsp;|&nbsp; Sold {{ $productQtySold }} unit{{ $productQtySold != 1 ? 's' : '' }} &nbsp;|&nbsp; Revenue: P{{ number_format($productSalesAmount, 2) }}</p>
    <table>
        <thead><tr><th>Date</th><th>Sale #</th><th>Customer</th><th class="num">Qty</th><th class="num">Unit Price</th><th class="num">Subtotal</th></tr></thead>
        <tbody>
        @forelse($productSales as $item)
        <tr>
            <td>{{ \Carbon\Carbon::parse($item->sale_date)->format('M d, Y h:i A') }}</td>
            <td>#{{ $item->sale_id }}</td>
            <td>{{ $item->customer_name ?? 'Walk-in' }}</td>
            <td class="num">{{ number_format($item->quantity) }}</td>
            <td class="num">P{{ number_format($item->unit_price, 2) }}</td>
            <td class="num">P{{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6">No completed retail sales for this product in the selected period.</td></tr>
        @endforelse
        <tr class="total-row"><td colspan="3">TOTAL</td><td class="num">{{ number_format($productQtySold) }}</td><td class="num">—</td><td class="num">P{{ number_format($productSalesAmount, 2) }}</td></tr>
        </tbody>
    </table>
    @endif

    @if($selectedServiceType)
    <h2 class="section-title">Repair Requests — {{ $selectedServiceType }}</h2>
    <p class="meta">{{ $repairCount }} request{{ $repairCount != 1 ? 's' : '' }} &nbsp;|&nbsp; Revenue: P{{ number_format($repairAmount, 2) }}</p>
    <table>
        <thead><tr><th>Date Received</th><th>Customer</th><th>Device</th><th>Status</th><th class="num">Final Price</th></tr></thead>
        <tbody>
        @forelse($repairRecords as $ticket)
        <tr>
            <td>{{ $ticket->date_received? \Carbon\Carbon::parse($ticket->date_received)->format('M d, Y') : 'N/A' }}</td>
            <td>{{ $ticket->device?->customer?->name ?? 'Walk-in' }}</td>
            <td>{{ $ticket->device?->brand }} {{ $ticket->device?->model }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</td>
            <td class="num">P{{ number_format($ticket->final_price ?? 0, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5">No repair requests for this service type in the selected period.</td></tr>
        @endforelse
        <tr class="total-row"><td colspan="4">TOTAL</td><td class="num">P{{ number_format($repairAmount, 2) }}</td></tr>
        </tbody>
    </table>
    @endif

    @if(!$selectedProduct && !$selectedServiceType)
    <p class="meta"><em>No product or service-type filter applied. Use the report page to pick a product or service type for a detailed export.</em></p>
    @endif

    <h2 class="section-title">Best-Selling Retail Products</h2>
    <table>
        <thead><tr><th>Product</th><th class="num">Sold</th><th class="num">Sales (P)</th></tr></thead>
        <tbody>
            @forelse ($bestSellingProducts as $product)
            <tr><td>{{ $product->product_name }}</td><td class="num">{{ number_format($product->qty_sold) }}</td><td class="num">{{ number_format($product->sales_amount, 2) }}</td></tr>
            @empty
            <tr><td colspan="3">No retail sales in the selected period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="section-title">Slow-Moving &amp; Dead Stock</h2>
    <table>
        <thead><tr><th>Product</th><th>Status</th><th class="num">In Stock</th><th class="num">Sold</th></tr></thead>
        <tbody>
            @forelse ($slowMovingProducts as $product)
            <tr><td>{{ $product->product_name }}</td><td>{{ (int) $product->qty_sold === 0 ? 'Dead Stock' : 'Slow Moving' }}</td><td class="num">{{ number_format($product->stock_quantity) }}</td><td class="num">{{ number_format($product->qty_sold) }}</td></tr>
            @empty
            <tr><td colspan="4">No slow-moving or dead stock for the selected period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="section-title">Most Requested Repair Services</h2>
    <table>
        <thead><tr><th>Service</th><th class="num">Requests</th><th class="num">Revenue (P)</th></tr></thead>
        <tbody>
            @forelse ($topRepairServices as $service)
            <tr><td>{{ $service->service_type }}</td><td class="num">{{ number_format($service->total_requests) }}</td><td class="num">{{ number_format($service->revenue, 2) }}</td></tr>
            @empty
            <tr><td colspan="3">No repair requests in the selected period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="section-title">Inventory Status ({{ number_format($totalProducts) }} total products)</h2>
    <table>
        <tbody>
            <tr><td>In Stock</td><td class="num">{{ number_format($inStock) }}</td></tr>
            <tr><td>Low Stock (needs reorder)</td><td class="num">{{ number_format($lowStock) }}</td></tr>
            <tr><td>Out of Stock (urgent restock)</td><td class="num">{{ number_format($outOfStock) }}</td></tr>
        </tbody>
    </table>

    <h2 class="section-title">Staff Performance</h2>
    <table>
        <thead><tr><th>Name</th><th>Role</th><th class="num">Repairs</th><th class="num">Sales</th><th class="num">Revenue (P)</th></tr></thead>
        <tbody>
            @forelse ($staffPerformance as $staff)
            <tr><td>{{ $staff->name }}</td><td>{{ ucfirst($staff->role ?? '') }}</td><td class="num">{{ number_format($staff->completed_repairs_count) }}</td><td class="num">{{ number_format($staff->completed_sales_count) }}</td><td class="num">{{ number_format($staff->revenue_generated, 2) }}</td></tr>
            @empty
            <tr><td colspan="5">No staff activity for the selected period.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
