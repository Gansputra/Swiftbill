<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CashTransaction;
use App\Models\CashShift;
use Livewire\WithPagination;

class CashManagement extends Component
{
    use WithPagination;

    public $type = '';
    public $amount = '';
    public $notes = '';
    
    public $showForm = false;
    public $searchTerm = '';
    
    public $currentShift;

    protected $rules = [
        'type' => 'required|in:in,out',
        'amount' => 'required|numeric|min:1',
        'notes' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->loadCurrentShift();
    }

    public function loadCurrentShift()
    {
        $this->currentShift = CashShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
    }

    public function render()
    {
        $this->loadCurrentShift(); // Re-check in case shift state changed

        $transactions = CashTransaction::with(['user', 'shift'])
            ->whereDate('created_at', today())
            ->where('notes', 'like', '%' . $this->searchTerm . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.cash-management', [
            'transactions' => $transactions,
        ])->layout('layouts.app');
    }

    public function resetFields()
    {
        $this->type = '';
        $this->amount = '';
        $this->notes = '';
        $this->showForm = false;
    }

    public function store()
    {
        $this->validate();

        $this->loadCurrentShift();

        if (!$this->currentShift) {
            session()->flash('error', 'You must open a shift in Point of Sale before adding cash transactions!');
            return;
        }

        CashTransaction::create([
            'shift_id' => $this->currentShift->id,
            'user_id' => auth()->id(),
            'type' => $this->type,
            'amount' => $this->amount,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Cash transaction recorded successfully.');
        $this->resetFields();
    }
}
