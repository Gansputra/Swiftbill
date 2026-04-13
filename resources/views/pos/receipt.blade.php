<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&family=Inconsolata:wght@200..900&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inconsolata', monospace;
            font-size: 14px;
            color: #000;
            background-color: #f1f5f9;
        }

        .ticket {
            width: 80mm; /* Standard thermal printer width */
            max-width: 80mm;
            padding: 15px;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        h1.store-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .store-info {
            font-size: 12px;
            line-height: 1.2;
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .receipt-info {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .receipt-info table {
            width: 100%;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        table.items th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            text-align: left;
        }

        table.items td {
            padding: 5px 0;
            vertical-align: top;
        }

        .item-name {
            font-weight: bold;
            display: block;
        }
        
        .item-discount {
            font-size: 11px;
            color: #666;
            font-style: italic;
        }

        .totals {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .totals table {
            width: 100%;
            font-size: 14px;
        }

        .grand-total {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            border-top: 1px dashed #000;
            padding-top: 15px;
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #4f46e5;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            font-family: inherit;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-back {
            display: block;
            width: 100%;
            padding: 10px;
            background: #e2e8f0;
            color: #333;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-family: inherit;
            margin-top: 10px;
            border-radius: 5px;
        }

        .no-print {
            max-width: 80mm;
            margin: 0 auto 40px auto;
        }

        @media print {
            body { background-color: #fff; }
            .ticket {
                margin: 0;
                padding: 0;
                box-shadow: none;
                width: 100%;
                max-width: 100%;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="ticket">
        
        <div class="text-center">
            <h1 class="store-name">TokoKu</h1>
            <div class="store-info">
                Jl. Raya Contoh No. 123<br>
                Telp: 08123456789
            </div>
        </div>

        <div class="receipt-info">
            <table>
                <tr>
                    <td>Inv:</td>
                    <td class="text-right">{{ $transaction->invoice_number }}</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Cashier:</td>
                    <td class="text-right">{{ auth()->user()->name ?? 'System' }}</td>
                </tr>
                <tr>
                    <td>Customer:</td>
                    <td class="text-right">{{ $transaction->customer_name ?? 'Guest' }}</td>
                </tr>
            </table>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td>
                        <span class="item-name">{{ $item->product->name ?? 'Unknown Item' }}</span>
                        <div style="font-size: 11px;">@ Rp {{ number_format($item->unit_price, 0) }}</div>
                        @if($item->discount > 0)
                            <div class="item-discount">Disc: -Rp {{ number_format($item->discount, 0) }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_price + $transaction->total_discount, 0) }}</td>
                </tr>
                @if($transaction->total_discount > 0)
                <tr>
                    <td>Discount</td>
                    <td class="text-right">- Rp {{ number_format($transaction->total_discount, 0) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td style="padding: 10px 0;">TOTAL</td>
                    <td class="text-right" style="padding: 10px 0;">Rp {{ number_format($transaction->total_price, 0) }}</td>
                </tr>
                <tr>
                    <td style="padding-top: 10px;">Pay ({{ strtoupper($transaction->payment_method) }})</td>
                    <td class="text-right" style="padding-top: 10px;">Rp {{ number_format($transaction->total_paid, 0) }}</td>
                </tr>
                <tr>
                    <td>Change</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_change, 0) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Thank you for shopping with us!<br>
            Please come again.
        </div>
    </div>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Print Again</button>
        <a href="{{ route('pos.index') }}" class="btn-back">Back to POS</a>
    </div>

</body>
</html>
