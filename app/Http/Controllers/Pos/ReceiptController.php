<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transaction;

class ReceiptController extends Controller
{
    public function show($invoiceNumber)
    {
        $transaction = Transaction::where('invoice_number', $invoiceNumber)
                        ->with('items.product')
                        ->firstOrFail();

        return view('pos.receipt', compact('transaction'));
    }
}
