<!DOCTYPE html>
<html>
<head>
    <title>HYH FIX — Business Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #111827; margin: 0; }
        h1 { font-size: 17px; margin: 0 0 4px 0; }
        p.meta { color: #555; margin: 2px 0 8px; font-size: 8.5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #bbb; padding: 5px 7px; text-align: left; font-size: 9px; }
        th { background: #e5e7eb; font-weight: 700; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.3px; }
        td.num, th.num { text-align: right; }
        .filter-note { color: #4b5563; font-size: 8.5px; margin: 4px 0 8px 0; }
        .total-row td { font-weight: 700; border-top: 2px solid #333; background: #f3f4f6; }
        .empty-row { text-align: center; color: #6b7280; padding: 14px 8px; }
        .section-title { font-size: 12px; font-weight: 700; margin: 14px 0 5px 0; color: #1f2937; border-bottom: 1px solid #d1d5db; padding-bottom: 3px; }
        .section-sub { font-size: 8.5px; color: #6b7280; margin-bottom: 6px; }
    </style>
</head>
<body>
    <h1>HYH FIX — Business Report</h1>
    <p class="meta">Period: {{ $rangeLabel }} &nbsp;|&nbsp; Generated: {{ now()->format('M d, Y h:i A') }} &nbsp;|&nbsp; Exported by: {{ $generatedBy ?? 'System' }}</p>

    @php
        $filterParts = [];
        if ($selectedProduct) { $filterParts[] = 'Product: ' . $selectedProduct->product_name; }
        if ($selectedServiceType) { $filterParts[] = 'Service: ' . $selectedServiceType; }
        $filterText = $filterParts ? ' | Filters: ' . implode(' · ', $filterParts) : '';
    @endphp
    <p class="filter-note">Sales comparison of repairs and accessories{{ $filterText }}</p>

    <!-- Summary Cards -->
    <table class="summary-table">
        <tr>
            <td><small>Total Sales</small><br><strong>P{{ number_format($totalSales, 2) }}</strong></td>
            <td><small>Total Transactions</small><br><strong>{{ number_format($totalTransactions) }}</strong></td>
            <td><small>Net Revenue</small><br><strong>P{{ number_format($netRevenue, 2) }}</strong></td>
            <td><small>Profit</small><br><strong>P{{ number_format($profit, 2) }}</strong></td>
            <td><small>Repairs Completed</small><br><strong>{{ number_format($repairsCompleted) }}</strong></td>
        </tr>
    </table>

    <!-- Best-Selling Retail Products -->
    <h2 class="section-title">Best-Selling Retail Products</h2>
    <p class="section-sub">Top retail products based on units sold during the selected period.</p>
    <table>
        <thead><tr><th>Product</th><th class="num">Sold</th><th class="num">Sales (P)</th></tr></thead>
        <tbody>
        @forelse ($bestSellingProducts as $product)
        <tr>
            <td>{{ $product->product_name }}</td>
            <td class="num">{{ number_format($product->qty_sold) }}</td>
            <td class="num">P{{ number_format($product->sales_amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="3" class="empty-row">No retail sales in the selected period.</td></tr>
        @endforelse
        </tbody>
    </table>

    <!-- Slow-Moving & Dead Stock -->
    <h2 class="section-title">Slow-Moving & Dead Stock</h2>
    <p class="section-sub">Products with low or no sales movement during the selected period.</p>
    <table>
        <thead><tr><th>Product</th><th>Status</th><th class="num">In Stock</th><th class="num">Sold</th></tr></thead>
        <tbody>
        @forelse ($slowMovingProducts as $product)
        @php $isDead = (int) $product->qty_sold === 0; @endphp
        <tr>
            <td>{{ $product->product_name }}</td>
            <td>{{ $isDead ? 'Dead Stock' : 'Slow Moving' }}</td>
            <td class="num">{{ number_format($product->stock_quantity) }}</td>
            <td class="num">{{ number_format($product->qty_sold) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="empty-row">No slow-moving or dead stock for the selected period.</td></tr>
        @endforelse
        </tbody>
    </table>

    <!-- Most Requested Repair Services -->
    <h2 class="section-title">Most Requested Repair Services</h2>
    <p class="section-sub">Most frequently requested repair services during the selected period.</p>
    <table>
        <thead><tr><th>Service</th><th class="num">Requests</th><th class="num">Revenue (P)</th></tr></thead>
        <tbody>
        @forelse ($topRepairServices as $service)
        <tr>
            <td>{{ $service->service_type }}</td>
            <td class="num">{{ number_format($service->total_requests) }}</td>
            <td class="num">P{{ number_format($service->revenue, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="3" class="empty-row">No repair requests in the selected period.</td></tr>
        @endforelse
        </tbody>
    </table>

    <!-- Inventory Status -->
    <h2 class="section-title">Inventory Status ({{ number_format($totalProducts) }} total products)</h2>
    <table>
        <tbody>
            <tr><td>In Stock</td><td class="num">{{ number_format($inStock) }}</td></tr>
            <tr><td>Low Stock (needs reorder)</td><td class="num">{{ number_format($lowStock) }}</td></tr>
            <tr><td>Out of Stock (urgent restock)</td><td class="num">{{ number_format($outOfStock) }}</td></tr>
        </tbody>
    </table>

    <!-- Staff Performance -->
    <h2 class="section-title">Staff Performance</h2>
    <p class="section-sub">Individual productivity and total revenue contribution.</p>
    <table>
        <thead><tr><th>Name</th><th>Role</th><th class="num">Repairs</th><th class="num">Sales</th><th class="num">Revenue (P)</th></tr></thead>
        <tbody>
        @forelse ($staffPerformance as $staff)
        <tr>
            <td>{{ $staff->name }}</td>
            <td>{{ ucfirst($staff->role ?? '') }}</td>
            <td class="num">{{ number_format($staff->completed_repairs_count) }}</td>
            <td class="num">{{ number_format($staff->completed_sales_count) }}</td>
            <td class="num">P{{ number_format($staff->revenue_generated, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-row">No staff activity for the selected period.</td></tr>
        @endforelse
        </tbody>
    </table>

    <!-- Detailed Transaction Report -->
    <h2 class="section-title">Detailed Transaction Report</h2>
    <p class="section-sub">Complete row-by-row transaction dump for the selected period.</p>
    <table>
        <thead>
            <tr>
                <th>DATE &amp; TIME</th>
                <th>TYPE</th>
                <th class="num">REF #</th>
                <th>CUSTOMER</th>
                <th>ITEM / DEVICE</th>
                <th class="num">QTY</th>
                <th class="num">AMOUNT</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
        @forelse($allTransactions as $txn)
        <tr>
            <td>{{ $txn->date_label }}</td>
            <td>{{ ucfirst($txn->type) }}</td>
            <td class="num">{{ $txn->ref }}</td>
            <td>{{ $txn->customer }}</td>
            <td>{{ $txn->item }}</td>
            <td class="num">{{ number_format($txn->qty) }}</td>
            <td class="num">P{{ number_format($txn->amount, 2) }}</td>
            <td>{{ $txn->status }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="empty-row">No transactions found in the selected period.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5">TOTAL</td>
                <td class="num">{{ number_format($allTransactions->sum('qty')) }}</td>
                <td class="num">P{{ number_format($allTransactions->sum('amount'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
