<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Transaction;

class SalesReport extends Component
{
    public $dateFrom;
    public $dateTo;
    public $transactions = [];
    public $totalRevenue = 0;
    public $totalDiscount = 0;
    public $totalTransactions = 0;

    public function mount()
    {
        $this->dateFrom = today()->format('Y-m-d');
        $this->dateTo = today()->format('Y-m-d');
        $this->loadReport();
    }

    public function loadReport()
    {
        $query = Transaction::with('items.product', 'user')
            ->whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->latest();

        $this->transactions = $query->get()->toArray();
        $this->totalRevenue = $query->sum('total_price');
        $this->totalDiscount = $query->sum('total_discount');
        $this->totalTransactions = $query->count();
    }

    public function updated($property)
    {
        if (in_array($property, ['dateFrom', 'dateTo'])) {
            $this->loadReport();
        }
    }

    public function render()
    {
        return view('livewire.reports.sales-report')->layout('layouts.app');
    }
}
