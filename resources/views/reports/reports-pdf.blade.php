<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HYH Fix Report - {{ $rangeLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 20px; margin: 0; }
        h2 { font-size: 14px; margin: 18px 0 6px; border-bottom: 1px solid #999; padding-bottom: 4px; }
        p.meta { color: #555; margin: 2px 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #bbb; padding: 5px 7px; text-align: left; }
        th { background: #e5e7eb; }
        td.num, th.num { text-align: right; }
        .cards td { text-align: center; font-size: 13px; }
        .cards small { color: #555; }
    </style>
</head>
<body>
    <h1>HYH Fix — Business Report</h1>
    <p class="meta">Period: {{ $rangeLabel }} &nbsp;|&nbsp; Generated: {{ now()->format('M d, Y h:i A') }}</p>

    <table class="cards">
        <tr>
            <td><small>Total Sales</small><br><strong>P{{ number_format($totalSales, 2) }}</strong></td>
            <td><small>Total Transactions</small><br><strong>{{ number_format($totalTransactions) }}</strong></td>
            <td><small>Net Revenue</small><br><strong>P{{ number_format($netRevenue, 2) }}</strong><br><small>Profit: P{{ number_format($profit, 2) }}</small></td>
            <td><small>Repairs Completed</small><br><strong>{{ number_format($repairsCompleted) }}</strong></td>
        </tr>
    </table>

    <h2>Best-Selling Retail Products</h2>
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

    <h2>Slow-Moving &amp; Dead Stock</h2>
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

    <h2>Most Requested Repair Services</h2>
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

    <h2>Inventory Status ({{ number_format($totalProducts) }} total products)</h2>
    <table>
        <tbody>
            <tr><td>In Stock</td><td class="num">{{ number_format($inStock) }}</td></tr>
            <tr><td>Low Stock (needs reorder)</td><td class="num">{{ number_format($lowStock) }}</td></tr>
            <tr><td>Out of Stock (urgent restock)</td><td class="num">{{ number_format($outOfStock) }}</td></tr>
        </tbody>
    </table>

    <h2>Staff Performance</h2>
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
