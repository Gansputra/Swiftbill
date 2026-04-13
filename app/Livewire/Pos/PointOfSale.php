<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Product;
use App\Services\TransactionService;

class PointOfSale extends Component
{
    public $searchTerm = '';
    public $cart = [];
    public $total = 0;
    public $totalPaid = 0;
    public $change = 0;
    public $paymentMethod = 'cash';
    public $customerName = 'Guest';

    public function render()
    {
        $products = Product::where('stock', '>', 0)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
            })
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.pos.point-of-sale', [
            'products' => $products
        ]);
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] < $product->stock) {
                $this->cart[$productId]['quantity']++;
            } else {
                session()->flash('error', 'Not enough stock available.');
            }
        } else {
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sell_price' => $product->sell_price,
                'quantity' => 1,
                'sku' => $product->sku
            ];
        }

        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            unset($this->cart[$productId]);
        } else {
            $product = Product::find($productId);
            if ($quantity <= $product->stock) {
                $this->cart[$productId]['quantity'] = $quantity;
            } else {
                session()->flash('error', 'Requested quantity exceeds stock.');
            }
        }
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['sell_price'] * $item['quantity'];
        });
        $this->change = $this->totalPaid - $this->total;
    }

    public function updatedTotalPaid()
    {
        $this->calculateTotal();
    }

    public function checkout(TransactionService $transactionService)
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty.');
            return;
        }

        if ($this->totalPaid < $this->total && $this->paymentMethod === 'cash') {
            session()->flash('error', 'Paid amount must be greater than or equal to total.');
            return;
        }

        $transaction = $transactionService->createTransaction([
            'total_price' => $this->total,
            'total_paid' => $this->totalPaid,
            'payment_method' => $this->paymentMethod,
            'customer_name' => $this->customerName
        ], array_values($this->cart));

        session()->flash('success', 'Transaction completed successfully.');
        $this->cart = [];
        $this->total = 0;
        $this->totalPaid = 0;
        $this->change = 0;
        $this->customerName = 'Guest';

        $this->redirect(route('pos.index'), navigate: true);
    }
}
