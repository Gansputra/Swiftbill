<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;

class SalesReport extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    
    public $totalRevenue = 0;
    public $totalDiscount = 0;
    public $totalProfit = 0;
    public $totalTransactions = 0;

    public function mount()
    {
        $this->dateFrom = today()->format('Y-m-d');
        $this->dateTo = today()->format('Y-m-d');
    }

    public function updated($property)
    {
        if (in_array($property, ['dateFrom', 'dateTo'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Transaction::with('items.product', 'user')
            ->whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->latest();

        // Calculate Totals using query (to avoid putting thousands of models in memory)
        $this->totalRevenue = (float) $query->sum('total_price');
        $this->totalDiscount = (float) $query->sum('total_discount');
        $this->totalTransactions = $query->count();

        // Calculate Total Profit (Requires some raw DB heavy lifting or simplified logic)
        // For efficiency, we calculate summary without loading full relations into memory
        $totalCogs = \DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.created_at', '>=', $this->dateFrom)
            ->whereDate('transactions.created_at', '<=', $this->dateTo)
            ->sum(\DB::raw('quantity * cogs'));

        $this->totalProfit = $this->totalRevenue - $totalCogs;

        return view('livewire.reports.sales-report', [
            'transactions' => $query->paginate(15)
        ])->layout('layouts.app');
    }
}
