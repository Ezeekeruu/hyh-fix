<!DOCTYPE html>
<html>
<head>
    <title>HYH Fix — Detailed Report - {{ $rangeLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; margin: 0; }
        h1 { font-size: 15px; margin: 0 0 4px 0; }
        p.meta { color: #555; margin: 2px 0 8px; font-size: 8.5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #bbb; padding: 4px 6px; text-align: left; font-size: 9px; }
        th { background: #e5e7eb; font-weight: 700; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.3px; }
        td.num, th.num { text-align: right; }
        .filter-note { color: #4b5563; font-size: 8.5px; margin: 4px 0 8px 0; }
        .total-row td { font-weight: 700; border-top: 2px solid #333; background: #f3f4f6; }
        .empty-row { text-align: center; color: #6b7280; padding: 16px 8px; }
    </style>
</head>
<body>
    <h1>HYH Fix — Detailed Transaction Report</h1>
    <p class="meta">Period: {{ $rangeLabel }} &nbsp;|&nbsp; Generated: {{ now()->format('M d, Y h:i A') }} &nbsp;|&nbsp; Exported by: {{ $generatedBy ?? 'System' }}</p>

    @php
        $filterParts = [];
        if ($selectedProduct) { $filterParts[] = 'Product: ' . $selectedProduct->product_name; }
        if ($selectedServiceType) { $filterParts[] = 'Service: ' . $selectedServiceType; }
        $filterText = $filterParts ? ' | Filters: ' . implode(' · ', $filterParts) : '';
    @endphp
    <p class="filter-note">Complete row-by-row transaction dump{{ $filterText }}</p>

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
            <td class="num">{{ '₱' . number_format($txn->amount, 2) }}</td>
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
                <td class="num">{{ '₱' . number_format($allTransactions->sum('amount'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
