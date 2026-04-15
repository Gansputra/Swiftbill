<div class="space-y-6">
    <!-- Header & Action -->
    <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Shift Logs</h2>
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">Operational Audits</p>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-3">
                <input type="date" wire:model.live="dateFrom"
                    class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-[10px] font-black uppercase tracking-widest py-3 px-4 focus:ring-4 focus:ring-indigo-500/10">
                <span class="text-slate-300">/</span>
                <input type="date" wire:model.live="dateTo"
                    class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-[10px] font-black uppercase tracking-widest py-3 px-4 focus:ring-4 focus:ring-indigo-500/10">
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.shifts.excel', ['from' => $dateFrom, 'to' => $dateTo]) }}"
                    class="p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                    <x-heroicon-o-document-chart-bar class="w-5 h-5" />
                </a>
                <a href="{{ route('reports.shifts.pdf', ['from' => $dateFrom, 'to' => $dateTo]) }}" target="_blank"
                    class="p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                    <x-heroicon-o-printer class="w-5 h-5" />
                </a>
            </div>
        </div>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div
            class="bg-indigo-50 dark:bg-indigo-900/20 p-8 rounded-[2rem] border border-indigo-100 dark:border-indigo-900/30">
            <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2 block">Duty Rounds</span>
            <div class="flex items-baseline gap-2">
                <span
                    class="text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tighter">{{ $shifts->total() }}</span>
                <span class="text-[10px] font-bold text-indigo-400 uppercase">Records</span>
            </div>
        </div>
        <div
            class="md:col-span-3 bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="max-w-xl">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Audit
                    Protocol</span>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-400 italic">Financial reconciliation of
                    shifts within the selected range. Ensuring zero variance across all duty rounds.</p>
            </div>
            <x-heroicon-o-clock class="w-12 h-12 text-slate-200" />
        </div>
    </div>

    <div
        class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
        <div class="overflow-x-auto p-4 pb-0">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                    <tr>
                        <th
                            class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-2xl">
                            Personnel</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Time Span
                        </th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cash
                            Metrics</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Variance
                        </th>
                        <th
                            class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right rounded-r-2xl">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                    @forelse($shifts as $shift)
                        <tr class="group">
                            <td
                                class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter">
                                    {{ $shift->user->name ?? 'System' }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">Cashier
                                    Station</p>
                            </td>
                            <td
                                class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span
                                            class="text-[10px] font-bold text-slate-600 dark:text-slate-400">{{ $shift->created_at->format('H:i') }}</span>
                                    </div>
                                    @if ($shift->status === 'closed')
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            <span
                                                class="text-[10px] font-bold text-slate-600 dark:text-slate-400">{{ $shift->closed_at->format('H:i') }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                            <span
                                                class="text-[10px] font-black text-sky-500 uppercase tracking-widest">Active</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td
                                class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-xs font-bold text-slate-500">
                                <p>Start: {{ number_format($shift->starting_cash, 0) }}</p>
                                @if ($shift->status === 'closed')
                                    <p class="text-slate-900 dark:text-white mt-1">Actual:
                                        {{ number_format($shift->actual_ending_cash, 0) }}</p>
                                @endif
                            </td>
                            <td
                                class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                @if ($shift->status === 'closed')
                                    @php
                                        $variance = $shift->actual_ending_cash - $shift->expected_ending_cash;
                                    @endphp
                                    <span
                                        class="text-xs font-black {{ $variance == 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 0) }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-300 italic">Calculating...</span>
                                @endif
                            </td>
                            <td
                                class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right">
                                <span
                                    class="px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-[0.2em] {{ $shift->status === 'closed' ? 'bg-slate-100 text-slate-500' : 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' }}">
                                    {{ $shift->status === 'closed' ? 'Terminated' : 'In Progress' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-32 text-center grayscale opacity-30">
                                <x-heroicon-o-calendar-days class="w-12 h-12 mx-auto text-slate-300 mb-4" />
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">No
                                    Operational Records</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-8 border-t border-slate-50 dark:border-slate-800">
            {{ $shifts->links() }}
        </div>
    </div>
</div>
