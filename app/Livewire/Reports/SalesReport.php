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

        // Calculate Totals in single query
        $totals = Transaction::selectRaw('
            COUNT(*) as total_transactions,
            SUM(total_price) as total_revenue,
            SUM(total_discount) as total_discount
        ')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->selectRaw('SUM(transaction_items.quantity * transaction_items.cogs) as total_cogs')
            ->whereDate('transactions.created_at', '>=', $this->dateFrom)
            ->whereDate('transactions.created_at', '<=', $this->dateTo)
            ->first();

        $this->totalRevenue = (float) ($totals->total_revenue ?? 0);
        $this->totalDiscount = (float) ($totals->total_discount ?? 0);
        $this->totalTransactions = (int) ($totals->total_transactions ?? 0);
        $this->totalProfit = $this->totalRevenue - ($totals->total_cogs ?? 0);

        return view('livewire.reports.sales-report', [
            'transactions' => $query->paginate(15)
        ])->layout('layouts.app');
    }
}
