<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shift Logs Report</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 20px; }
        h1 { font-size: 20px; margin-bottom: 2px; }
        .subtitle { color: #64748b; font-size: 11px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #f1f5f9; text-transform: uppercase; letter-spacing: 1px; font-size: 9px; padding: 10px 8px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 6px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-open { background: #fef3c7; color: #d97706; }
        .badge-closed { background: #d1fae5; color: #059669; }
        .positive { color: #059669; font-weight: bold; }
        .negative { color: #ef4444; font-weight: bold; }
        .zero { color: #94a3b8; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <h1>SwiftBill — Shift Logs Report</h1>
    <p class="subtitle">Period: {{ $from }} to {{ $to }}</p>

    <table>
        <thead>
            <tr>
                <th>Cashier</th>
                <th>Shift Opened</th>
                <th>Shift Closed</th>
                <th class="text-right">Starting Cash</th>
                <th class="text-right">Expected Cash</th>
                <th class="text-right">Actual Cash</th>
                <th class="text-right">Variance</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shifts as $shift)
                @php
                    $variance = ($shift->status === 'closed' && $shift->actual_ending_cash !== null)
                        ? $shift->actual_ending_cash - $shift->expected_ending_cash
                        : null;
                @endphp
                <tr>
                    <td>{{ $shift->user->name ?? '-' }}</td>
                    <td>{{ $shift->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $shift->closed_at ? $shift->closed_at->format('d M Y H:i') : '—' }}</td>
                    <td class="text-right">Rp {{ number_format($shift->starting_cash, 0) }}</td>
                    <td class="text-right">{{ $shift->expected_ending_cash !== null ? 'Rp '.number_format($shift->expected_ending_cash, 0) : '—' }}</td>
                    <td class="text-right">{{ $shift->actual_ending_cash !== null ? 'Rp '.number_format($shift->actual_ending_cash, 0) : '—' }}</td>
                    <td class="text-right">
                        @if($variance !== null)
                            <span class="{{ $variance > 0 ? 'positive' : ($variance < 0 ? 'negative' : 'zero') }}">
                                {{ $variance > 0 ? '+' : '' }}Rp {{ number_format($variance, 0) }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $shift->status === 'open' ? 'badge-open' : 'badge-closed' }}">{{ strtoupper($shift->status) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
