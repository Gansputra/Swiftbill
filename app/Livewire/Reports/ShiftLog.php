<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\CashShift;
use Carbon\Carbon;

class ShiftLog extends Component
{
    public $dateFrom;
    public $dateTo;
    
    public $shifts = [];
    public $totalOverage = 0;
    public $totalShortage = 0;
    public $totalSystemExpected = 0;

    public function mount()
    {
        $this->dateFrom = today()->startOfMonth()->format('Y-m-d');
        $this->dateTo = today()->format('Y-m-d');
        $this->loadReport();
    }

    public function loadReport()
    {
        $query = CashShift::with('user')
            ->whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->orderBy('id', 'desc');

        $shiftsList = $query->get();
        $this->shifts = $shiftsList->toArray();

        $this->totalOverage = 0;
        $this->totalShortage = 0;
        $this->totalSystemExpected = 0;

        foreach ($shiftsList as $shift) {
            if ($shift->status === 'closed') {
                $variance = $shift->actual_ending_cash - $shift->expected_ending_cash;
                if ($variance > 0) {
                    $this->totalOverage += $variance;
                } elseif ($variance < 0) {
                    $this->totalShortage += abs($variance);
                }
                $this->totalSystemExpected += $shift->expected_ending_cash;
            }
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['dateFrom', 'dateTo'])) {
            $this->loadReport();
        }
    }

    public function render()
    {
        return view('livewire.reports.shift-log')->layout('layouts.app');
    }
}
