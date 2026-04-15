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

        // Recalculate summary (Only for closed shifts in this date range)
        $summary = CashShift::whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->where('status', 'closed')
            ->get();

        $this->totalOverage = 0;
        $this->totalShortage = 0;
        $this->totalSystemExpected = 0;

        foreach ($summary as $shift) {
            $variance = $shift->actual_ending_cash - $shift->expected_ending_cash;
            if ($variance > 0) {
                $this->totalOverage += $variance;
            } elseif ($variance < 0) {
                $this->totalShortage += abs($variance);
            }
            $this->totalSystemExpected += $shift->expected_ending_cash;
        }

        return view('livewire.reports.shift-log', [
            'shifts' => $shifts
        ])->layout('layouts.app');
    }
}
