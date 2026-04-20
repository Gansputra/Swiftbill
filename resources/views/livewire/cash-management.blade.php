<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Cash Management</h2>
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">Operational Ledger</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-plus-circle class="w-5 h-5" />
            {{ $showForm ? 'View Ledger' : 'New Transaction' }}
        </button>
    </div>

    @if (session()->has('success'))
        <div class="p-6 bg-emerald-50 text-emerald-600 rounded-3xl text-[10px] font-black border border-emerald-100 uppercase tracking-widest flex items-center gap-3">
             <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="p-6 bg-rose-50 text-rose-600 rounded-3xl text-[10px] font-black border border-rose-100 uppercase tracking-widest flex items-center gap-3">
             {{ session('error') }}
        </div>
    @endif

    <!-- Alert for inactive shift -->
    @if(!$currentShift)
        <div class="p-6 bg-yellow-50 text-yellow-700 rounded-3xl text-[10px] font-black border border-yellow-200 uppercase tracking-widest flex items-center gap-3">
             <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
             Warning: You must open a shift in Point of Sale before adding new cash transactions!
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm relative overflow-hidden">
            @if(!$currentShift)
                <div class="absolute inset-0 z-10 bg-white/60 dark:bg-slate-900/60 backdrop-blur-sm flex flex-col items-center justify-center">
                    <x-heroicon-o-lock-closed class="w-12 h-12 text-slate-400 mb-4" />
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest text-center px-6">Shift Closed</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-2">Cannot mutate cash balance without an active shift.</p>
                </div>
            @endif

            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wider mb-8">Register Cash Transaction</h3>
            <form wire:submit.prevent="store" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Transaction Type</label>
                            <select wire:model="type" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                <option value="">Select Type</option>
                                <option value="in">Cash In (Tarik Dana/Setoran)</option>
                                <option value="out">Cash Out (Pengeluaran Operasional)</option>
                            </select>
                            @error('type') <span class="text-[10px] text-rose-500 font-bold mt-2">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Amount (Rp)</label>
                            <input type="number" wire:model="amount" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            @error('amount') <span class="text-[10px] text-rose-500 font-bold mt-2">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Transaction Notes / Motif</label>
                            <textarea wire:model="notes" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" rows="5" placeholder="Reason for this cash movement (e.g. 'Beli air mineral untuk toko')..."></textarea>
                            @error('notes') <span class="text-[10px] text-rose-500 font-bold mt-2">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-all">Cancel</button>
                    <button type="submit" class="px-12 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all" {{ !$currentShift ? 'disabled' : '' }}>
                        Record Transaction
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="p-4 border-b border-slate-50 dark:border-slate-800">
                <input type="text" wire:model.live="searchTerm" placeholder="Filter records by notes..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-3 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all">
            </div>

            <div class="overflow-x-auto p-4 pb-0">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-2xl">Date & Type</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Description</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Operator</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @forelse($transactions as $transaction)
                            <tr class="group">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-[10px] font-black text-slate-900 dark:text-white">{{ $transaction->created_at->format('d M, H:i') }}</p>
                                    <span class="px-2 py-0.5 mt-1 inline-block rounded text-[8px] font-black uppercase tracking-widest {{ $transaction->type === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        CASH {{ strtoupper($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs font-black text-slate-900 dark:text-white">{{ $transaction->notes }}</p>
                                    @if($transaction->shift && $transaction->shift->status === 'open')
                                        <p class="text-[8px] text-indigo-400 font-bold uppercase tracking-widest mt-1">Active Shift</p>
                                    @elseif($transaction->shift)
                                        <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-1">Shift on {{ $transaction->shift->created_at->format('d/m') }}</p>
                                    @endif
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">By {{ $transaction->user->name ?? 'System' }}</span>
                                </td>
                                <td class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right">
                                    <span class="text-sm font-black {{ $transaction->type === 'in' ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $transaction->type === 'in' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-32 text-center grayscale opacity-30">
                                    <x-heroicon-o-banknotes class="w-12 h-12 mx-auto text-slate-300 mb-4" />
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">No Cash Records</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-8 border-t border-slate-50 dark:border-slate-800">
                {{ $transactions->links() }}
            </div>
        </div>
    @endif
</div>
