<div class="space-y-8">
    <!-- Header Summary -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">System Overview</h2>
            <p class="text-sm text-slate-500">Welcome back. Here is what is happening with SwiftBill today.</p>
        </div>
        <div class="flex space-x-3">
            <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold shadow-sm hover:bg-slate-50 transition">Export Report</button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-sm hover:bg-indigo-700 transition">Create Sale</button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 transition hover:shadow-md">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Daily Revenue</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($dailyRevenue, 0) }}</h3>
            <div class="mt-4 flex items-center text-[10px] font-bold text-green-500 uppercase">
                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"></path></svg>
                Active Growth
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 transition hover:shadow-md">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Transactions</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $dailySalesCount }}</h3>
            <div class="mt-4 flex items-center text-[10px] font-bold text-slate-400 uppercase">
                Updated just now
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 transition hover:shadow-md">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Stock Alerts</p>
            <h3 class="text-2xl font-bold {{ $lowStockProducts > 0 ? 'text-rose-600' : 'text-slate-900 dark:text-white' }}">{{ $lowStockProducts }}</h3>
            <div class="mt-4 flex items-center text-[10px] font-bold {{ $lowStockProducts > 0 ? 'text-rose-500' : 'text-green-500' }} uppercase">
                Items need attention
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 transition hover:shadow-md">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total SKU</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalProducts }}</h3>
            <div class="mt-4 flex items-center text-[10px] font-bold text-indigo-500 uppercase">
                Managed Products
            </div>
        </div>
    </div>

    <!-- Data Sections -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Top Products -->
        <div class="md:col-span-4 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Top Performing</h3>
                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 rounded-full">Unit Sales</span>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($topProducts as $item)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-500">{{ $loop->iteration }}</div>
                            <div>
                                <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $item->product->name }}</p>
                                <p class="text-[10px] text-slate-400 uppercase font-medium">{{ $item->product->sku }}</p>
                            </div>
                        </div>
                        <div class="text-xs font-bold text-slate-900 dark:text-white">{{ $item->total_sold }}</div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">No data records found.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="md:col-span-8 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Transactions</h3>
                <button class="text-[10px] font-bold text-slate-400 hover:text-indigo-600 uppercase tracking-widest transition">View Archive</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Invoice</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">User</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $tx->invoice_number }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $tx->created_at->format('M d, H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $tx->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Rp {{ number_format($tx->total_price, 0) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-xs text-slate-400">No transaction records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
