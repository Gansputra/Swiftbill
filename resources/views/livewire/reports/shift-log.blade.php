<div>
    <x-slot name="header">
        Shift Logs
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Shift Logs</h2>
                <p class="text-xs text-slate-500 uppercase tracking-widest font-bold mt-1">Cash Register Audit Trail</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.shifts.excel', ['from' => $dateFrom, 'to' => $dateTo]) }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition shadow-lg shadow-emerald-100 dark:shadow-none flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Excel
                </a>
                <a href="{{ route('reports.shifts.pdf', ['from' => $dateFrom, 'to' => $dateTo]) }}" target="_blank" class="px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition shadow-lg shadow-rose-100 dark:shadow-none flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    PDF
                </a>
            </div>
        </div>

        <!-- Date Filter & Summary -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">From</span>
                    <input type="date" wire:model.live="dateFrom" class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-white">
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">To</span>
                    <input type="date" wire:model.live="dateTo" class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500 text-slate-700 dark:text-white">
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest">Total Expected</span>
                    <span class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($totalSystemExpected, 0) }}</span>
                </div>
                <div class="text-right">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest">Overage</span>
                    <span class="text-lg font-extrabold text-emerald-600">+Rp {{ number_format($totalOverage, 0) }}</span>
                </div>
                <div class="text-right">
                    <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest">Shortage</span>
                    <span class="text-lg font-extrabold text-rose-500">-Rp {{ number_format($totalShortage, 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Shifts Table -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cashier</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Shift Opened</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Shift Closed</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Starting Cash</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Expected Cash</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actual Cash</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Variance</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($shifts as $shift)
                            @php
                                $variance = ($shift['status'] === 'closed' && $shift['actual_ending_cash'] !== null)
                                    ? $shift['actual_ending_cash'] - $shift['expected_ending_cash']
                                    : null;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase">{{ substr($shift['user']['name'] ?? '?', 0, 1) }}</div>
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $shift['user']['name'] ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-500">{{ \Carbon\Carbon::parse($shift['created_at'])->setTimezone(config('app.timezone'))->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-500">
                                    {{ $shift['closed_at'] ? \Carbon\Carbon::parse($shift['closed_at'])->setTimezone(config('app.timezone'))->format('d M Y H:i') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-bold text-slate-900 dark:text-white">Rp {{ number_format($shift['starting_cash'], 0) }}</td>
                                <td class="px-6 py-4 text-right text-xs font-bold text-slate-900 dark:text-white">
                                    {{ $shift['expected_ending_cash'] !== null ? 'Rp '.number_format($shift['expected_ending_cash'], 0) : '—' }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-bold text-slate-900 dark:text-white">
                                    {{ $shift['actual_ending_cash'] !== null ? 'Rp '.number_format($shift['actual_ending_cash'], 0) : '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($variance !== null)
                                        <span class="text-xs font-extrabold {{ $variance > 0 ? 'text-emerald-600' : ($variance < 0 ? 'text-rose-500' : 'text-slate-400') }}">
                                            {{ $variance > 0 ? '+' : '' }}Rp {{ number_format($variance, 0) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest
                                        {{ $shift['status'] === 'open' ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' }}">
                                        {{ $shift['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No shift records found for the selected date range</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
