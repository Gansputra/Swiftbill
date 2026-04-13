<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    public function createTransaction(array $data, array $cartItems)
    {
        return DB::transaction(function () use ($data, $cartItems) {
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            
            $activeShift = \App\Models\CashShift::where('user_id', auth()->id())
                ->where('status', 'open')
                ->first();

            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => auth()->id(),
                'shift_id' => $activeShift ? $activeShift->id : null,
                'total_price' => $data['total_price'],
                'total_discount' => $data['total_discount'] ?? 0,
                'total_paid' => $data['total_paid'],
                'total_change' => $data['total_paid'] - $data['total_price'],
                'payment_method' => $data['payment_method'],
                'customer_name' => $data['customer_name'] ?? 'Guest',
            ]);

            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                
                // Reduce Stock
                $product->decrement('stock', $item['quantity']);

                // Create Stock Movement History
                \App\Models\StockMovement::create([
                    'product_id' => $item['product_id'],
                    'type' => 'sale',
                    'quantity' => $item['quantity'],
                    'reference_id' => $invoiceNumber,
                    'notes' => 'Sold via POS',
                    'user_id' => auth()->id()
                ]);

                $discount = $item['discount'] ?? 0;
                $subtotal = ($item['quantity'] * $item['sell_price']) - $discount;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['sell_price'],
                    'discount' => $discount,
                    'subtotal' => max(0, $subtotal),
                ]);
            }

            return $transaction;
        });
    }

    public function getDailySales()
    {
        return Transaction::whereDate('created_at', today())->get();
    }
}
