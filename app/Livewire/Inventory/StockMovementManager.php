<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockMovement;
use Livewire\WithPagination;

class StockMovementManager extends Component
{
    use WithPagination;

    public $product_id, $type, $quantity, $notes;
    public $showForm = false;
    public $searchTerm = '';

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'type' => 'required|in:purchase,opname_add,opname_deduct',
        'quantity' => 'required|integer|min:1',
        'notes' => 'nullable|string|max:255',
    ];

    public function render()
    {
        $movements = StockMovement::with(['product', 'user'])
            ->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.inventory.stock-movement-manager', [
            'movements' => $movements,
            'products' => Product::all(),
        ]);
    }

    public function resetFields()
    {
        $this->product_id = '';
        $this->type = '';
        $this->quantity = '';
        $this->notes = '';
        $this->showForm = false;
    }

    public function store()
    {
        $this->validate();

        $product = Product::findOrFail($this->product_id);

        // Adjust actual product stock safely
        if ($this->type === 'opname_deduct') {
            if ($product->stock < $this->quantity) {
                session()->flash('error', 'Quantity to deduct exceeds current stock.');
                return;
            }
            $product->decrement('stock', $this->quantity);
        } else {
            $product->increment('stock', $this->quantity);
        }

        // Record movement
        StockMovement::create([
            'product_id' => $this->product_id,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'user_id' => auth()->id(),
        ]);

        session()->flash('success', 'Stock movement recorded successfully.');
        $this->resetFields();
    }
}
