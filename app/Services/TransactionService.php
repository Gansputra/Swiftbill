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
            
            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => auth()->id(),
                'total_price' => $data['total_price'],
                'total_paid' => $data['total_paid'],
                'total_change' => $data['total_paid'] - $data['total_price'],
                'payment_method' => $data['payment_method'],
                'customer_name' => $data['customer_name'] ?? 'Guest',
            ]);

            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                
                // Reduce Stock
                $product->decrement('stock', $item['quantity']);

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['sell_price'],
                    'subtotal' => $item['quantity'] * $item['sell_price'],
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
