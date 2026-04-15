<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Stock Ledger</h2>
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">Inventory Logistics</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-plus-circle class="w-5 h-5" />
            {{ $showForm ? 'View Ledger' : 'New Movement' }}
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

    @if($showForm)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm">
            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wider mb-8">Register Stock Movement</h3>
            <form wire:submit.prevent="store" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Target Product</label>
                            <select wire:model="product_id" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                <option value="">Select SKU</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="text-[10px] text-rose-500 font-bold mt-2">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Movement Type</label>
                                <select wire:model="type" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                    <option value="">Select Type</option>
                                    <option value="purchase">Purchase (Stock In)</option>
                                    <option value="opname_add">Opname Adjustment (Add)</option>
                                    <option value="opname_deduct">Opname Adjustment (Deduct)</option>
                                </select>
                                @error('type') <span class="text-[10px] text-rose-500 font-bold mt-2">{{ $message }}</span> @enderror
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Quantity Change</label>
                                <input type="number" wire:model="quantity" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                                @error('quantity') <span class="text-[10px] text-rose-500 font-bold mt-2">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Movement Notes</label>
                            <textarea wire:model="notes" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" rows="5" placeholder="Reason for this stock adjustment..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-all">Cancel</button>
                    <button type="submit" class="px-12 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all">
                        Execute Movement
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="p-4 border-b border-slate-50 dark:border-slate-800">
                <input type="text" wire:model.live="searchTerm" placeholder="Filter movements by reason or SKU..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-3 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all">
            </div>

            <div class="overflow-x-auto p-4 pb-0">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-2xl">Date & Type</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Product</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Auditor</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right rounded-r-2xl">Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @forelse($movements as $movement)
                            <tr class="group">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-[10px] font-black text-slate-900 dark:text-white">{{ $movement->created_at->format('d M, H:i') }}</p>
                                    <span class="px-2 py-0.5 mt-1 inline-block rounded text-[8px] font-black uppercase tracking-widest {{ in_array($movement->type, ['purchase', 'opname_add']) ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        {{ str_replace('_', ' ', $movement->type) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter">{{ $movement->product->name }}</p>
                                    <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">{{ $movement->product->sku }}</p>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">By {{ $movement->user->name ?? 'System' }}</span>
                                    <p class="text-[9px] text-slate-400 italic mt-1">{{ $movement->notes }}</p>
                                </td>
                                <td class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right">
                                    <span class="text-sm font-black {{ in_array($movement->type, ['purchase', 'opname_add']) ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ in_array($movement->type, ['purchase', 'opname_add']) ? '+' : '-' }}{{ number_format($movement->quantity, 0) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-32 text-center grayscale opacity-30">
                                    <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mx-auto text-slate-300 mb-4" />
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">No Ledger Records</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-8 border-t border-slate-50 dark:border-slate-800">
                {{ $movements->links() }}
            </div>
        </div>
    @endif
</div>
