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
    public $snapToken = null; // Store pending Midtrans token

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

        // Load cart and token from session persistence
        $this->cart = session()->get('pos_cart_' . auth()->id(), []);
        $this->snapToken = session()->get('pos_snap_token_' . auth()->id(), null);
        $this->customerName = session()->get('pos_customer_' . auth()->id(), 'Guest');
        
        $this->calculateTotal();
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

        $cashIn = $this->currentShift->cashTransactions()->where('type', 'in')->sum('amount');
        $cashOut = $this->currentShift->cashTransactions()->where('type', 'out')->sum('amount');

        return $this->currentShift->starting_cash + $cashSales + $cashIn - $cashOut;
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
        
        // Clear session on shift close
        session()->forget('pos_cart_' . auth()->id());
        session()->forget('pos_snap_token_' . auth()->id());

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
                'stock' => $product->stock,
                'discount' => 0
            ];
        }

        $this->snapToken = null;
        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        if ($productId === 'all') {
            $this->cart = [];
        } else {
            unset($this->cart[$productId]);
        }
        $this->snapToken = null;
        $this->calculateTotal();
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            unset($this->cart[$productId]);
        } else {
            if ($quantity <= ($this->cart[$productId]['stock'] ?? PHP_INT_MAX)) {
                $this->cart[$productId]['quantity'] = $quantity;
            } else {
                session()->flash('error', 'Requested quantity exceeds stock.');
            }
        }
        $this->snapToken = null;
        $this->calculateTotal();
    }

    public function updateDiscount($productId, $discount)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['discount'] = (float) $discount;
            $this->snapToken = null;
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

        // Sync with session for persistence
        session()->put('pos_cart_' . auth()->id(), $this->cart);
        session()->put('pos_snap_token_' . auth()->id(), $this->snapToken);
        session()->put('pos_customer_' . auth()->id(), $this->customerName);
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

        if ($this->paymentMethod === 'qris' || $this->paymentMethod === 'transfer') {
            if ($this->snapToken) {
                $this->dispatch('open-midtrans', snapToken: $this->snapToken);
                return;
            }
            $this->initiateMidtransPayment();
            return;
        }

        $this->processFinalCheckout($transactionService);
    }

    protected function initiateMidtransPayment()
    {
        // Set Midtrans Config
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $orderId = 'SB-' . time() . '-' . auth()->id();

        $items = collect($this->cart)->map(fn($item) => [
            'id' => $item['product_id'],
            'price' => (int) $item['sell_price'],
            'quantity' => (int) $item['quantity'],
            'name' => substr($item['name'], 0, 50),
        ])->values()->toArray();

        // Add discount as a negative item if applicable
        if ($this->totalDiscount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => -(int) $this->totalDiscount,
                'quantity' => 1,
                'name' => 'Discount/Promo',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $this->total,
            ],
            'customer_details' => [
                'first_name' => $this->customerName,
                'email' => auth()->user()->email,
            ],
            'item_details' => $items,
        ];

        try {
            $this->snapToken = \Midtrans\Snap::getSnapToken($params);
            $this->dispatch('open-midtrans', snapToken: $this->snapToken);
        } catch (\Exception $e) {
            session()->flash('error', 'Midtrans Error: ' . $e->getMessage());
        }
    }

    public function finalizeTransaction(TransactionService $transactionService, $midtransResult = null)
    {
        $this->processFinalCheckout($transactionService);
    }

    protected function processFinalCheckout(TransactionService $transactionService)
    {
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
        $this->snapToken = null; // Clear token on success

        // Final Session Cleanup
        session()->forget('pos_cart_' . auth()->id());
        session()->forget('pos_snap_token_' . auth()->id());
        session()->forget('pos_customer_' . auth()->id());

        $this->redirect(route('pos.receipt', $invoiceNumber), navigate: false);
    }
}
