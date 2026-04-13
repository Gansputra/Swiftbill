<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Stock Inventory</h2>
            <p class="text-xs text-slate-500 uppercase tracking-widest font-bold">Catalog Management</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-indigo-100 dark:shadow-none hover:bg-slate-900 transition-all">
            {{ $showForm ? 'View Catalog' : 'Add New SKU' }}
        </button>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 text-green-600 rounded-2xl text-[10px] font-bold border border-green-100 uppercase tracking-wide">
            {{ session('success') }}
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-8 uppercase tracking-wider">{{ $isEditing ? 'Modify Product' : 'Register New SKU' }}</h3>
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Product Name</label>
                            <input type="text" wire:model="name" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500">
                            @error('name') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Stock Keeping Unit (SKU)</label>
                            <input type="text" wire:model="sku" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500">
                            @error('sku') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Internal Category</label>
                                <select wire:model="category_id" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Trusted Supplier</label>
                                <select wire:model="supplier_id" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Purchase Cost</label>
                                <input type="number" wire:model="buy_price" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500">
                                @error('buy_price') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Retail Price</label>
                                <input type="number" wire:model="sell_price" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500">
                                @error('sell_price') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Initial Stock</label>
                                <input type="number" wire:model="stock" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500">
                                @error('stock') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Alert Threshold</label>
                                <input type="number" wire:model="min_stock" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500">
                                @error('min_stock') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Product Visual</label>
                            <input type="file" wire:model="image" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            @error('image') <span class="text-[10px] text-rose-500 font-bold mt-1 tracking-tighter">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-3 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">
                        Discard Changes
                    </button>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-indigo-100 dark:shadow-none hover:bg-slate-900 transition-all">
                        {{ $isEditing ? 'Commit Modification' : 'Confirm Registration' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <input type="text" wire:model.live="searchTerm" placeholder="Quick find product SKU or name..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Product Details</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Inventory State</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Valuation</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="h-10 w-10 flex-shrink-0 bg-slate-100 dark:bg-slate-800 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-[10px] text-slate-400 font-bold uppercase">NA</div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $product->name }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium uppercase truncate w-32 tracking-wider">{{ $product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="flex items-center space-x-1">
                                            <span class="text-xs font-bold {{ $product->stock <= $product->min_stock ? 'text-rose-500' : 'text-slate-900 dark:text-white' }}">{{ $product->stock }}</span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase">Available</span>
                                        </div>
                                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-tighter">{{ $product->category->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase">Buy: {{ number_format($product->buy_price,0) }}</p>
                                    <p class="text-xs font-bold text-indigo-600">Sell: {{ number_format($product->sell_price,0) }}</p>
                                </td>
                                <td class="px-6 py-4 text-right space-x-4">
                                    <button wire:click="edit({{ $product->id }})" class="text-[10px] font-bold text-indigo-600 uppercase tracking-tighter hover:text-slate-900 transition">Update</button>
                                    <button onclick="confirm('Proceed with permanent removal?') || event.stopImmediatePropagation()" wire:click="delete({{ $product->id }})" class="text-[10px] font-bold text-rose-500 uppercase tracking-tighter hover:text-slate-900 transition">Remove</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-24 text-center flex flex-col items-center justify-center opacity-40">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No matching SKU in repository</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-slate-50 dark:bg-slate-800/50">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
