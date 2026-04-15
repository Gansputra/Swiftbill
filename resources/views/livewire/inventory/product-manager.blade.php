<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Product Catalog</h2>
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">SKU Repository</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-plus-circle class="w-5 h-5" />
            {{ $showForm ? 'View Catalog' : 'Add New SKU' }}
        </button>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-indigo-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-indigo-500/20 flex flex-col justify-between">
            <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Inventory Health</span>
            <div class="mt-4">
                <span class="text-4xl font-black tracking-tighter">98%</span>
                <p class="text-[9px] font-bold opacity-60 uppercase mt-1 tracking-widest">Stock accuracy</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Low Stock</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black {{ $lowStockProducts > 0 ? 'text-rose-500' : 'text-slate-900 dark:text-white' }} tracking-tighter">{{ $lowStockProducts }}</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase italic">Items Alert</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Total SKU</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ count($products) }}</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase italic">Registered</span>
            </div>
        </div>
        <div class="bg-slate-900 dark:bg-indigo-950 p-8 rounded-[2.5rem] text-white flex items-center justify-center">
             <x-heroicon-o-sparkles class="w-10 h-10 text-indigo-400 animate-pulse" />
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-6 bg-emerald-50 text-emerald-600 rounded-3xl text-[10px] font-black border border-emerald-100 uppercase tracking-widest flex items-center gap-3">
             <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm">
            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wider mb-10">{{ $isEditing ? 'Modify SKU' : 'Register New Item' }}</h3>
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-8">
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Product Descriptor</label>
                            <input type="text" wire:model="name" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            @error('name') <span class="text-[10px] text-rose-500 font-bold mt-2 inline-block px-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Unique SKU Code</label>
                            <input type="text" wire:model="sku" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            @error('sku') <span class="text-[10px] text-rose-500 font-bold mt-2 inline-block px-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Category</label>
                                <select wire:model="category_id" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                    <option value="">Choose Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Source Vendor</label>
                                <select wire:model="supplier_id" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                    <option value="">Choose Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Acquisition Cost</label>
                                <input type="number" wire:model="buy_price" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Retail Price</label>
                                <input type="number" wire:model="sell_price" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800">
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Current Stock</label>
                                <input type="number" wire:model="stock" class="block w-full bg-white dark:bg-slate-900 border-none rounded-2xl text-xs font-bold py-3 px-4 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Alert Level</label>
                                <input type="number" wire:model="min_stock" class="block w-full bg-white dark:bg-slate-900 border-none rounded-2xl text-xs font-bold py-3 px-4 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Visual Asset</label>
                            <input type="file" wire:model="image" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-indigo-600 file:text-white hover:file:bg-slate-900 file:transition-all cursor-pointer">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-8 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-all">Cancel</button>
                    <button type="submit" class="px-12 py-4 bg-indigo-600 text-white rounded-[1.8rem] text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all">
                        {{ $isEditing ? 'Commit Update' : 'Register SKU' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between">
                <div class="relative max-w-md w-full">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-300 absolute left-4 top-1/2 -translate-y-1/2" />
                    <input type="text" wire:model.live="searchTerm" placeholder="Search SKU or Name..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-[1.5rem] text-xs font-bold py-3 pl-12 pr-6 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                </div>
            </div>

            <div class="overflow-x-auto p-4 pb-0">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-2xl">Item Info</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">In Stock</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pricing</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right rounded-r-2xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @forelse($products as $product)
                            <tr class="group">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20 first:border-l last:border-r">
                                    <div class="flex items-center gap-4">
                                        <div class="h-14 w-14 rounded-2xl bg-white dark:bg-slate-800 p-1 flex-shrink-0 shadow-sm overflow-hidden border border-slate-100">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full object-cover rounded-xl">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-[8px] font-black text-slate-300">VOID</div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tighter">{{ $product->name }}</p>
                                            <p class="text-[9px] text-indigo-500 font-bold uppercase tracking-widest mt-1">{{ $product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base font-black {{ $product->stock <= $product->min_stock ? 'text-rose-500' : 'text-slate-900 dark:text-white' }} tracking-tighter">{{ number_format($product->stock,0) }}</span>
                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Units</span>
                                        </div>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter mt-1">{{ $product->category->name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                    <div class="flex flex-col">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase">Cost: {{ number_format($product->buy_price,0) }}</p>
                                        <p class="text-sm font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format($product->sell_price,0) }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right border-y border-slate-50 dark:border-slate-800/20 first:border-l last:border-r">
                                    <div class="flex items-center justify-end gap-3 opacity-30 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="edit({{ $product->id }})" class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </button>
                                        <button onclick="confirm('Permanent deletion?') || event.stopImmediatePropagation()" wire:click="delete({{ $product->id }})" class="p-2.5 bg-rose-50 dark:bg-rose-900/30 text-rose-500 dark:text-rose-400 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-40 text-center grayscale opacity-30">
                                    <x-heroicon-o-cube-transparent class="w-12 h-12 mx-auto text-slate-300 mb-4" />
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Empty Inventory State</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-8 border-t border-slate-50 dark:border-slate-800">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
