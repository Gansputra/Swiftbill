<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Sales Report</h2>
            <p class="text-xs text-slate-500 uppercase tracking-widest font-bold mt-1">Revenue & Transaction History</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.export.excel', ['from' => $dateFrom, 'to' => $dateTo]) }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition shadow-lg shadow-emerald-100 dark:shadow-none flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Excel
            </a>
            <a href="{{ route('reports.export.pdf', ['from' => $dateFrom, 'to' => $dateTo]) }}" target="_blank" class="px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition shadow-lg shadow-rose-100 dark:shadow-none flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                PDF
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">From</span>
                <input type="date" wire:model.live="dateFrom" class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">To</span>
                <input type="date" wire:model.live="dateTo" class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right">
                <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest">Transactions</span>
                <span class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $totalTransactions }}</span>
            </div>
            <div class="text-right">
                <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest">Discount</span>
                <span class="text-lg font-extrabold text-rose-500">Rp {{ number_format($totalDiscount, 0) }}</span>
            </div>
            <div class="text-right">
                <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest">Revenue</span>
                <span class="text-lg font-extrabold text-emerald-600">Rp {{ number_format($totalRevenue, 0) }}</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Invoice</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cashier</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Items</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Method</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('pos.receipt', $trx['invoice_number']) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline">{{ $trx['invoice_number'] }}</a>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">{{ \Carbon\Carbon::parse($trx['created_at'])->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 dark:text-white">{{ $trx['user']['name'] ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="space-y-0.5">
                                    @foreach($trx['items'] as $item)
                                        <span class="block text-[10px] text-slate-500">{{ $item['product']['name'] ?? 'Unknown' }} × {{ $item['quantity'] }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg text-[8px] font-bold uppercase tracking-widest
                                    {{ $trx['payment_method'] === 'cash' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400' }}">
                                    {{ $trx['payment_method'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-xs font-bold text-slate-900 dark:text-white">Rp {{ number_format($trx['total_price'], 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No transactions found for the selected date range</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
