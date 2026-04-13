<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report {{ $from }} to {{ $to }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e293b; padding-bottom: 15px; }
        .header h1 { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { font-size: 11px; color: #64748b; margin-top: 4px; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 20px; gap: 15px; }
        .summary-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center; }
        .summary-box .label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .summary-box .value { font-size: 18px; font-weight: 800; margin-top: 4px; }
        .value-green { color: #059669; }
        .value-rose { color: #e11d48; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f1f5f9; padding: 8px 10px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        tr:hover { background: #f8fafc; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .total-row { background: #f1f5f9; font-weight: 800; font-size: 12px; }
        .total-row td { border-top: 2px solid #1e293b; padding: 10px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-cash { background: #ecfdf5; color: #059669; }
        .badge-digital { background: #eef2ff; color: #4f46e5; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
        .btn-print { display: block; width: 200px; margin: 20px auto; padding: 10px; background: #4f46e5; color: white; text-align: center; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; }
        @media print {
            .btn-print { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="header">
        <h1>Sales Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        <p>Generated: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Transactions</div>
            <div class="value">{{ $transactions->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Discount</div>
            <div class="value value-rose">Rp {{ number_format($totalDiscount, 0) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Revenue</div>
            <div class="value value-green">Rp {{ number_format($totalRevenue, 0) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Date</th>
                <th>Cashier</th>
                <th>Items</th>
                <th>Method</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $trx)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="font-bold">{{ $trx->invoice_number }}</td>
                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $trx->user->name ?? '-' }}</td>
                <td>
                    @foreach($trx->items as $item)
                        {{ $item->product->name ?? 'Unknown' }} ×{{ $item->quantity }}<br>
                    @endforeach
                </td>
                <td><span class="badge {{ $trx->payment_method === 'cash' ? 'badge-cash' : 'badge-digital' }}">{{ $trx->payment_method }}</span></td>
                <td class="text-right">Rp {{ number_format($trx->total_discount, 0) }}</td>
                <td class="text-right font-bold">Rp {{ number_format($trx->total_price, 0) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right">GRAND TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalDiscount, 0) }}</td>
                <td class="text-right">Rp {{ number_format($totalRevenue, 0) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Swiftbill POS — Automated Sales Report
    </div>

    <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
</body>
</html>
