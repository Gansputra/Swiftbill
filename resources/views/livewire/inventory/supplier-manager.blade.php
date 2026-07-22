<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Pemasok</h2>
            <p class="text-xs text-slate-500 font-medium">Jaringan Pemasok & Procurement</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-xs font-semibold shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-user-plus class="w-5 h-5" />
            {{ $showForm ? 'Lihat Daftar' : 'Tambah Pemasok' }}
        </button>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-indigo-600 p-8 rounded-[2rem] text-white shadow-xl shadow-indigo-500/20 flex flex-col justify-between">
            <span class="text-xs font-semibold opacity-80">Total Pemasok</span>
            <div class="flex items-baseline gap-2 mt-4">
                <span class="text-4xl font-black tracking-tighter">{{ count($suppliers) }}</span>
                <span class="text-xs font-bold opacity-60">Terverifikasi</span>
            </div>
        </div>
        <div class="md:col-span-3 bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 flex items-center justify-between group">
            <div class="flex-grow">
                <span class="text-xs font-semibold text-slate-400 block mb-2">Manajemen Rantai Pasok</span>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-400 max-w-lg">Kelola hubungan vendor dan kontak procurement dalam satu sistem terpusat.</p>
            </div>
            <x-heroicon-o-truck class="w-10 h-10 text-slate-200" />
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-6 bg-emerald-50 text-emerald-600 rounded-3xl text-xs font-semibold border border-emerald-100 flex items-center gap-3">
             <x-heroicon-o-check-badge class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">{{ $isEditing ? 'Edit Data Pemasok' : 'Tambah Pemasok Baru' }}</h3>
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Nama Pemasok / Vendor</label>
                        <input type="text" wire:model="name" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                        @error('name') <span class="text-xs text-rose-500 font-medium mt-2">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="group">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Alamat Email</label>
                        <input type="email" wire:model="email" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                    </div>

                    <div class="group">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">No. Telepon / WhatsApp</label>
                        <input type="text" wire:model="phone" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                    </div>
                    
                    <div class="md:col-span-2 group">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Alamat Kantor / Alamat Utama</label>
                        <textarea wire:model="address" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none" rows="3"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-4 text-xs font-semibold text-slate-400">Batal</button>
                    <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl text-xs font-semibold shadow-xl shadow-indigo-500/20 hover:bg-slate-900">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Pemasok' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="p-6 border-b border-slate-50 dark:border-slate-800">
                <input type="text" wire:model.live="searchTerm" placeholder="Cari pemasok..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="overflow-x-auto p-4 pb-0">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 rounded-l-2xl">Nama Pemasok</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Email</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Telepon</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Alamat</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 text-right rounded-r-2xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @foreach($suppliers as $supplier)
                            <tr class="group" wire:key="supplier-{{ $supplier->id }}">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $supplier->name }}</p>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ $supplier->email ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $supplier->phone ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs text-slate-500 font-normal truncate max-w-xs">{{ $supplier->address ?? '-' }}</p>
                                </td>
                                <td class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-30 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="edit({{ $supplier->id }})" class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </button>
                                        <button onclick="confirm('Hapus pemasok ini secara permanen?') || event.stopImmediatePropagation()" wire:click="delete({{ $supplier->id }})" class="p-2 bg-rose-50 dark:bg-rose-900/30 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-8 border-t border-slate-50 dark:border-slate-800">
                {{ $suppliers->links() }}
            </div>
        </div>
    @endif
</div>
