<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Kategori</h2>
            <p class="text-xs text-slate-500 font-medium">Klasifikasi Inventaris</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-xs font-semibold shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-plus-circle class="w-5 h-5" />
            {{ $showForm ? 'Lihat Semua' : 'Tambah Kategori' }}
        </button>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-8 rounded-[2rem] border border-indigo-100 dark:border-indigo-900/30">
            <span class="text-xs font-semibold text-indigo-400 block mb-2">Total Kategori</span>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-indigo-600 dark:text-indigo-400 tracking-tighter">{{ count($categories) }}</span>
                <span class="text-xs font-bold text-indigo-400/60">Aktif</span>
            </div>
        </div>
        <div class="md:col-span-2 bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 block mb-2">Informasi Pengelolaan</span>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-400 max-w-md italic">Kelola inventaris Anda dengan lebih efisien melalui pengelompokan produk ke dalam kategori.</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl text-slate-300">
                <x-heroicon-o-rectangle-group class="w-10 h-10" />
            </div>
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
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">{{ $isEditing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Nama Kategori</label>
                        <input type="text" wire:model="name" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        @error('name') <span class="text-xs text-rose-500 font-medium mt-2 inline-block px-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="group">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Deskripsi (Opsional)</label>
                        <input type="text" wire:model="description" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-4 text-xs font-semibold text-slate-400 hover:text-slate-600 transition-all">Batal</button>
                    <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl text-xs font-semibold shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Kategori' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="p-6 border-b border-slate-50 dark:border-slate-800">
                <input type="text" wire:model.live="searchTerm" placeholder="Cari kategori cepat..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="overflow-x-auto p-4 pb-0">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 rounded-l-2xl">Nama Kategori</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Deskripsi</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 text-right rounded-r-2xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @forelse($categories as $category)
                            <tr class="group" wire:key="category-{{ $category->id }}">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $category->name }}</p>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs font-medium text-slate-500 truncate w-64 italic">{{ $category->description ?: 'Tidak ada deskripsi' }}</p>
                                </td>
                                <td class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-30 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="edit({{ $category->id }})" class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </button>
                                        <button onclick="confirm('Hapus kategori ini secara permanen?') || event.stopImmediatePropagation()" wire:click="delete({{ $category->id }})" class="p-2 bg-rose-50 dark:bg-rose-900/30 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-32 text-center grayscale opacity-30">
                                    <x-heroicon-o-tag class="w-12 h-12 mx-auto text-slate-300 mb-4" />
                                    <p class="text-xs font-medium text-slate-400">Belum Ada Kategori</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-8 border-t border-slate-50 dark:border-slate-800">
                {{ $categories->links() }}
            </div>
        </div>
    @endif
</div>
