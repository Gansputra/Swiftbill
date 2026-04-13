<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    /**
     * Export sales report as CSV/Excel
     */
    public function excel(Request $request)
    {
        $from = $request->query('from', today()->format('Y-m-d'));
        $to = $request->query('to', today()->format('Y-m-d'));

        $transactions = Transaction::with('items.product', 'user')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()
            ->get();

        $filename = "sales_report_{$from}_to_{$to}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['Invoice', 'Date', 'Cashier', 'Customer', 'Items', 'Payment Method', 'Discount', 'Total']);

            foreach ($transactions as $trx) {
                $items = $trx->items->map(fn($i) => ($i->product->name ?? 'Unknown') . ' x' . $i->quantity)->implode(', ');

                fputcsv($file, [
                    $trx->invoice_number,
                    $trx->created_at->format('Y-m-d H:i'),
                    $trx->user->name ?? '-',
                    $trx->customer_name ?? 'Guest',
                    $items,
                    strtoupper($trx->payment_method),
                    $trx->total_discount,
                    $trx->total_price,
                ]);
            }

            // Summary row
            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', 'TOTAL', $transactions->sum('total_discount'), $transactions->sum('total_price')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export sales report as printable PDF (HTML)
     */
    public function pdf(Request $request)
    {
        $from = $request->query('from', today()->format('Y-m-d'));
        $to = $request->query('to', today()->format('Y-m-d'));

        $transactions = Transaction::with('items.product', 'user')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->latest()
            ->get();

        return view('reports.sales-pdf', [
            'transactions' => $transactions,
            'from' => $from,
            'to' => $to,
            'totalRevenue' => $transactions->sum('total_price'),
            'totalDiscount' => $transactions->sum('total_discount'),
        ]);
    }
}
