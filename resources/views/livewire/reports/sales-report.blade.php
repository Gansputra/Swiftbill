<div class="space-y-6">
    <!-- Header & Action Bento -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm relative overflow-hidden group">
        <!-- Abstract Decoration -->
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-50 dark:bg-indigo-900/10 rounded-full blur-[100px] pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>

        <div class="relative z-10">
            <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Revenue Analytics</h2>
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.3em] mt-1">Enterprise Performance Audit</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 relative z-10">
            <div class="flex items-center bg-slate-50 dark:bg-slate-800 rounded-2xl p-1 border border-slate-100 dark:border-slate-800">
                <input type="date" wire:model.live="dateFrom" class="bg-transparent border-none text-[10px] font-black uppercase tracking-widest py-2 px-4 focus:ring-0">
                <span class="text-slate-300 font-black">/</span>
                <input type="date" wire:model.live="dateTo" class="bg-transparent border-none text-[10px] font-black uppercase tracking-widest py-2 px-4 focus:ring-0">
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export.excel', ['from' => $dateFrom, 'to' => $dateTo]) }}" class="p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                    <x-heroicon-o-document-chart-bar class="w-5 h-5" />
                </a>
                <a href="{{ route('reports.export.pdf', ['from' => $dateFrom, 'to' => $dateTo]) }}" target="_blank" class="p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                    <x-heroicon-o-printer class="w-5 h-5" />
                </a>
            </div>
        </div>
    </div>

    <!-- Bento Stats Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Revenue Bento -->
        <div class="bg-indigo-600 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-indigo-500/20 relative overflow-hidden">
            <div class="absolute right-0 top-0 p-8 opacity-10">
                <x-heroicon-o-presentation-chart-line class="w-16 h-16" />
            </div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Gross Intake</span>
            <div class="mt-6">
                <p class="text-3xl font-black tracking-tighter">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <div class="flex items-center gap-2 mt-2 opacity-60">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    <span class="text-[9px] font-bold uppercase">Total Invoiced</span>
                </div>
            </div>
        </div>

        <!-- Profit Bento -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-8 rounded-[2.5rem] shadow-sm">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Net Estimation</span>
            <div class="mt-6 flex flex-col justify-between">
                <p class="text-2xl font-black {{ $totalProfit >= 0 ? 'text-emerald-500' : 'text-rose-500' }} tracking-tighter">
                   {{ $totalProfit < 0 ? '-' : '' }}Rp {{ number_format(abs($totalProfit), 0, ',', '.') }}
                </p>
                <span class="text-[10px] font-bold text-slate-400 uppercase mt-2 tracking-tighter">Operating Margin</span>
            </div>
        </div>

        <!-- Transactions Bento -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-8 rounded-[2.5rem] shadow-sm">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Global Volume</span>
            <div class="mt-6">
                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($totalTransactions, 0) }}</p>
                <div class="flex items-center gap-1 mt-1">
                    <span class="text-[9px] font-bold text-indigo-500 uppercase">Settled Orders</span>
                </div>
            </div>
        </div>

        <!-- Discounts Bento -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-8 rounded-[2.5rem] shadow-sm">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Yield Buffer</span>
            <div class="mt-6">
                <p class="text-3xl font-black text-rose-500 tracking-tighter">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</p>
                <div class="flex items-center gap-1 mt-1">
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Applied Discounts</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Transaction Bento Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[3rem] shadow-sm overflow-hidden text-sm relative">
        <div class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between bg-slate-50/30 dark:bg-slate-900/30">
            <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tighter uppercase">Audit Trail</h3>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest border border-slate-200 dark:border-slate-800 px-3 py-1 rounded-full italic">Sorted by Recency</span>
        </div>

        <div class="overflow-x-auto p-4 pb-0">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-2xl">TX Reference</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Timestamp</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Account</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Manifest</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right rounded-r-2xl">Settlement</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/10">
                    @forelse($transactions as $trx)
                        <tr class="group">
                            <td class="px-8 py-6 rounded-l-3xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20 first:border-l last:border-r">
                                <a href="{{ route('pos.receipt', $trx['invoice_number'] ?? '#') }}" target="_blank" class="text-xs font-black text-slate-900 dark:text-white hover:text-indigo-600 transition-colors uppercase tracking-widest border-b border-transparent hover:border-indigo-600">
                                    #{{ $trx['invoice_number'] ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-900 dark:text-white uppercase">{{ isset($trx['created_at']) ? \Carbon\Carbon::parse($trx['created_at'])->format('d M Y') : '-' }}</span>
                                    <span class="text-[9px] text-slate-400 font-bold mt-0.5 tracking-widest">{{ isset($trx['created_at']) ? \Carbon\Carbon::parse($trx['created_at'])->format('H:i:s') : '-' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                <div class="flex items-center gap-2">
                                     <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                     <span class="text-[10px] font-black text-slate-700 dark:text-white uppercase">{{ $trx['user']['name'] ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                <div class="flex flex-wrap gap-1">
                                    @isset($trx['items'])
                                        @foreach($trx['items'] as $item)
                                            <span class="px-2 py-0.5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded text-[8px] font-black text-slate-500 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-all">
                                                {{ $item['product']['name'] ?? 'Item' }} ({{ $item['quantity'] ?? 1 }})
                                            </span>
                                        @endforeach
                                    @endisset
                                </div>
                            </td>
                            <td class="px-8 py-6 rounded-r-3xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right border-y border-slate-50 dark:border-slate-800/20 first:border-l last:border-r">
                                <p class="text-sm font-black text-slate-900 dark:text-white tracking-tighter">Rp {{ number_format($trx['total_price'] ?? 0, 0, ',', '.') }}</p>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 inline-block border border-slate-100 dark:border-slate-800 px-1.5 py-0.5 rounded leading-none italic">
                                    {{ $trx['payment_method'] ?? 'cash' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-32 text-center grayscale opacity-30">
                                <x-heroicon-o-no-symbol class="w-12 h-12 mx-auto text-slate-300 mb-4" />
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Zero Transaction Context</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>