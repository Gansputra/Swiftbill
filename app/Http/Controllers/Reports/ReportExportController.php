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

    /**
     * Export shift logs as CSV/Excel
     */
    public function shiftExcel(Request $request)
    {
        $from = $request->query('from', today()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', today()->format('Y-m-d'));

        $shifts = \App\Models\CashShift::with('user')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderBy('id', 'desc')
            ->get();

        $filename = "shift_logs_{$from}_to_{$to}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($shifts) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Cashier', 'Shift Opened', 'Shift Closed', 'Starting Cash', 'Expected Cash', 'Actual Cash', 'Variance', 'Status']);

            foreach ($shifts as $shift) {
                $variance = ($shift->status === 'closed' && $shift->actual_ending_cash !== null)
                    ? $shift->actual_ending_cash - $shift->expected_ending_cash
                    : 0;

                fputcsv($file, [
                    $shift->user->name ?? '-',
                    $shift->created_at->format('Y-m-d H:i'),
                    $shift->closed_at ? $shift->closed_at->format('Y-m-d H:i') : '-',
                    $shift->starting_cash,
                    $shift->expected_ending_cash ?? '-',
                    $shift->actual_ending_cash ?? '-',
                    $variance,
                    strtoupper($shift->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export shift logs as printable PDF (HTML)
     */
    public function shiftPdf(Request $request)
    {
        $from = $request->query('from', today()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', today()->format('Y-m-d'));

        $shifts = \App\Models\CashShift::with('user')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderBy('id', 'desc')
            ->get();

        return view('reports.shifts-pdf', [
            'shifts' => $shifts,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
