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
    public $totalDiscount = 0;
    public $change = 0;
    public $paymentMethod = 'cash';
    public $customerName = 'Guest';

    public $hasShiftError = '';

    // Shift properties
    public $currentShift;
    public $startingCash = '';
    public $actualCash = '';
    public $closingNotes = '';
    public $isClosingShift = false;

    public function mount()
    {
        $this->currentShift = \App\Models\CashShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
    }

    public function openShift()
    {
        if ($this->startingCash === '' || $this->startingCash < 0) {
            $this->hasShiftError = 'Please enter a valid starting cash amount.';
            return;
        }

        $this->currentShift = \App\Models\CashShift::create([
            'user_id' => auth()->id(),
            'status' => 'open',
            'starting_cash' => (float) $this->startingCash,
        ]);
        
        $this->hasShiftError = '';
    }

    public function calculateExpectedCash()
    {
        if (!$this->currentShift) return 0;
        
        // Net cash added to drawer = total_price (for cash payments)
        $cashSales = $this->currentShift->transactions()
            ->where('payment_method', 'cash')
            ->sum('total_price');

        return $this->currentShift->starting_cash + $cashSales;
    }

    public function initiateCloseShift()
    {
        $this->isClosingShift = true;
        $this->actualCash = $this->calculateExpectedCash();
        $this->closingNotes = '';
    }

    public function confirmCloseShift()
    {
        if ($this->actualCash === '' || $this->actualCash < 0) {
            $this->hasShiftError = 'Please enter a valid actual cash amount.';
            return;
        }

        $this->currentShift->update([
            'expected_ending_cash' => $this->calculateExpectedCash(),
            'actual_ending_cash' => (float) $this->actualCash,
            'notes' => $this->closingNotes,
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        $this->currentShift = null;
        $this->isClosingShift = false;
        $this->startingCash = '';
        $this->cart = [];
        $this->total = 0;
        $this->totalPaid = 0;
        $this->change = 0;
        
        session()->flash('success', 'Shift has been successfully closed.');
        $this->redirect(route('pos.index'), navigate: false);
    }

    public function render()
    {
        $products = collect();
        
        if ($this->currentShift) {
            $products = Product::where('stock', '>', 0)
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
                })
                ->latest()
                ->take(8)
                ->get();
        }

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
                'sku' => $product->sku,
                'discount' => 0
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

    public function updateDiscount($productId, $discount)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['discount'] = (float) $discount;
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $this->totalDiscount = collect($this->cart)->sum(function ($item) {
            return (float) ($item['discount'] ?? 0);
        });

        $this->total = (float) collect($this->cart)->sum(function ($item) {
            $subtotal = ((float) $item['sell_price'] * (int) $item['quantity']) - (float) ($item['discount'] ?? 0);
            return max(0, $subtotal);
        });

        if ($this->paymentMethod !== 'cash') {
            $this->totalPaid = $this->total;
        }

        $this->change = (float) $this->totalPaid - (float) $this->total;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['totalPaid', 'paymentMethod'])) {
            $this->calculateTotal();
        }
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

        if (!$this->currentShift) {
            session()->flash('error', 'No active shift.');
            return;
        }

        $transaction = $transactionService->createTransaction([
            'total_price' => $this->total,
            'total_discount' => $this->totalDiscount,
            'total_paid' => $this->totalPaid,
            'payment_method' => $this->paymentMethod,
            'customer_name' => $this->customerName
        ], array_values($this->cart));

        session()->flash('success', 'Transaction completed successfully.');
        $invoiceNumber = $transaction->invoice_number;
        
        $this->cart = [];
        $this->total = 0;
        $this->totalPaid = 0;
        $this->change = 0;
        $this->totalDiscount = 0;
        $this->customerName = 'Guest';

        $this->redirect(route('pos.receipt', $invoiceNumber), navigate: false);
    }
}
