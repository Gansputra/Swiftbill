<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\CashShift;
use Livewire\WithPagination;
use Carbon\Carbon;

class ShiftLog extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;

    public $totalOverage = 0;
    public $totalShortage = 0;
    public $totalSystemExpected = 0;

    public function mount()
    {
        $this->dateFrom = today()->startOfMonth()->format('Y-m-d');
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
        $query = CashShift::with('user')
            ->whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->orderBy('id', 'desc');

        $shifts = $query->paginate(10);

        // Calculate summary in single query
        $summary = CashShift::selectRaw('
            SUM(CASE WHEN (actual_ending_cash - expected_ending_cash) > 0 
                THEN (actual_ending_cash - expected_ending_cash) ELSE 0 END) as totalOverage,
            SUM(CASE WHEN (actual_ending_cash - expected_ending_cash) < 0 
                THEN ABS(actual_ending_cash - expected_ending_cash) ELSE 0 END) as totalShortage,
            SUM(expected_ending_cash) as totalSystemExpected
        ')
            ->whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->where('status', 'closed')
            ->first();

        $this->totalOverage = $summary->totalOverage ?? 0;
        $this->totalShortage = $summary->totalShortage ?? 0;
        $this->totalSystemExpected = $summary->totalSystemExpected ?? 0;

        return view('livewire.reports.shift-log', [
            'shifts' => $shifts
        ])->layout('layouts.app');
    }
}
