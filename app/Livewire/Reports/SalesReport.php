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

        // Calculate Transaction totals without duplication from join
        $transactionTotals = Transaction::selectRaw('
            COUNT(*) as total_transactions,
            SUM(total_price) as total_revenue,
            SUM(total_discount) as total_discount
        ')
            ->whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->first();

        // Calculate COGS separately using join
        $totalCogs = \DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.created_at', '>=', $this->dateFrom)
            ->whereDate('transactions.created_at', '<=', $this->dateTo)
            ->sum(\DB::raw('transaction_items.quantity * transaction_items.cogs'));

        $this->totalRevenue = (float) ($transactionTotals->total_revenue ?? 0);
        $this->totalDiscount = (float) ($transactionTotals->total_discount ?? 0);
        $this->totalTransactions = (int) ($transactionTotals->total_transactions ?? 0);
        $this->totalProfit = $this->totalRevenue - $totalCogs;

        return view('livewire.reports.sales-report', [
            'transactions' => $query->paginate(15)
        ])->layout('layouts.app');
    }
}
