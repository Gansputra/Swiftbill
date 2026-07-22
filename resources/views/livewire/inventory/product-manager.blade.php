<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Katalog Produk</h2>
            <p class="text-xs text-slate-500 font-medium">Repositori Produk & Stok</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-xs font-semibold shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-plus-circle class="w-5 h-5" />
            {{ $showForm ? 'Lihat Katalog' : 'Tambah Produk Baru' }}
        </button>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-indigo-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-indigo-500/20 flex flex-col justify-between">
            <span class="text-xs font-semibold opacity-80">Kesehatan Stok</span>
            <div class="mt-4">
                <span class="text-4xl font-black tracking-tighter">98%</span>
                <p class="text-xs font-medium opacity-80 mt-1">Akurasi Stok</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800">
            <span class="text-xs font-semibold text-slate-400 block mb-2">Stok Menipis</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black {{ $lowStockProducts > 0 ? 'text-rose-500' : 'text-slate-900 dark:text-white' }} tracking-tighter">{{ $lowStockProducts }}</span>
                <span class="text-xs font-bold text-slate-400 italic">Produk Menipis</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800">
            <span class="text-xs font-semibold text-slate-400 block mb-2">Total Produk</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ count($products) }}</span>
                <span class="text-xs font-bold text-slate-400 italic">Terdaftar</span>
            </div>
        </div>
        <div class="bg-slate-900 dark:bg-indigo-950 p-8 rounded-[2.5rem] text-white flex items-center justify-center">
             <x-heroicon-o-sparkles class="w-10 h-10 text-indigo-400 animate-pulse" />
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-6 bg-emerald-50 text-emerald-600 rounded-3xl text-xs font-semibold border border-emerald-100 flex items-center gap-3">
             <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">{{ $isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-8">
                        <div class="group">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Nama Produk</label>
                            <input type="text" wire:model.live="name" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            @error('name') <span class="text-xs text-rose-500 font-medium mt-2 inline-block px-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Kode SKU</label>
                            <input type="text" wire:model="sku" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            @error('sku') <span class="text-xs text-rose-500 font-medium mt-2 inline-block px-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Kategori</label>
                                <select wire:model="category_id" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="group">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Pemasok / Vendor</label>
                                <select wire:model="supplier_id" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                    <option value="">Pilih Pemasok</option>
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
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Harga Beli (Rp)</label>
                                <input type="number" wire:model="buy_price" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>
                            <div class="group">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Harga Jual (Rp)</label>
                                <input type="number" wire:model="sell_price" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800">
                            <div class="group">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Stok Saat Ini</label>
                                <input type="number" wire:model="stock" class="block w-full bg-white dark:bg-slate-900 border-none rounded-2xl text-xs font-bold py-3 px-4 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            </div>
                            <div class="group">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Batas Stok Minimum</label>
                                <input type="number" wire:model="min_stock" class="block w-full bg-white dark:bg-slate-900 border-none rounded-2xl text-xs font-bold py-3 px-4 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Gambar Produk</label>
                            <input type="file" wire:model="image" class="block w-full text-xs text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-slate-900 file:transition-all cursor-pointer">
                            <div wire:loading wire:target="image" class="text-xs text-indigo-500 font-semibold mt-2 block animate-pulse">
                                Mengunggah Gambar...
                            </div>
                            
                            @if ($image)
                                <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/50 inline-block">
                                    <span class="block text-xs font-semibold text-slate-400 mb-2">Pratinjau Gambar</span>
                                    <div class="h-24 w-24 rounded-xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700">
                                        <img src="{{ $image->temporaryUrl() }}" class="h-full w-full object-cover">
                                    </div>
                                </div>
                            @elseif ($isEditing && $productId)
                                @php
                                    $currentProduct = \App\Models\Product::find($productId);
                                @endphp
                                @if ($currentProduct && $currentProduct->image)
                                    <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/50 inline-block">
                                        <span class="block text-xs font-semibold text-slate-400 mb-2">Gambar Saat Ini</span>
                                        <div class="h-24 w-24 rounded-xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700">
                                            <img src="{{ asset('storage/' . $currentProduct->image) }}" class="h-full w-full object-cover">
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-8 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-4 text-xs font-semibold text-slate-400 hover:text-slate-600 transition-all">Batal</button>
                    <button type="submit" class="px-12 py-4 bg-indigo-600 text-white rounded-[1.8rem] text-xs font-semibold shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Produk' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between">
                <div class="relative max-w-md w-full">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-300 absolute left-4 top-1/2 -translate-y-1/2" />
                    <input type="text" wire:model.live="searchTerm" placeholder="Cari SKU atau Nama Produk..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-[1.5rem] text-xs font-bold py-3 pl-12 pr-6 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                </div>
            </div>

            <div class="overflow-x-auto p-4 pb-0">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 rounded-l-2xl">Produk</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Kategori</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">SKU</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Harga Beli</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Harga Jual</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 text-center">Stok</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 text-right rounded-r-2xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @forelse($products as $product)
                            <tr class="group" wire:key="product-{{ $product->id }}">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20 first:border-l last:border-r">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-2xl bg-white dark:bg-slate-800 p-1 flex-shrink-0 shadow-sm overflow-hidden border border-slate-100 dark:border-slate-800/40">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full object-cover rounded-xl">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-[8px] font-bold text-slate-300">KOSONG</div>
                                            @endif
                                        </div>
                                        <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $product->name }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                    <span class="text-xs font-medium text-slate-500">{{ $product->category->name }}</span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                    <span class="text-xs text-indigo-500 font-semibold">{{ $product->sku }}</span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                    <span class="text-xs font-semibold text-slate-400">Rp {{ number_format($product->buy_price,0) }}</span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20">
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($product->sell_price,0) }}</span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all border-y border-slate-50 dark:border-slate-800/20 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-bold {{ $product->stock <= $product->min_stock ? 'text-rose-500' : 'text-slate-900 dark:text-white' }}">{{ number_format($product->stock,0) }}</span>
                                        @if($product->stock <= $product->min_stock)
                                            <span class="text-[9px] font-semibold text-rose-500 leading-none">Stok Menipis</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right border-y border-slate-50 dark:border-slate-800/20 first:border-l last:border-r">
                                    <div class="flex items-center justify-end gap-3 opacity-30 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="edit({{ $product->id }})" class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </button>
                                        <button onclick="confirm('Hapus produk ini secara permanen?') || event.stopImmediatePropagation()" wire:click="delete({{ $product->id }})" class="p-2.5 bg-rose-50 dark:bg-rose-900/30 text-rose-500 dark:text-rose-400 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-40 text-center grayscale opacity-30">
                                    <x-heroicon-o-cube-transparent class="w-12 h-12 mx-auto text-slate-300 mb-4" />
                                    <p class="text-xs font-medium text-slate-400">Belum Ada Produk Terdaftar</p>
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
