<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Kas</h2>
            <p class="text-xs text-slate-500 font-medium">Buku Kas Operasional</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-xs font-semibold shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-plus-circle class="w-5 h-5" />
            {{ $showForm ? 'Lihat Buku Kas' : 'Tambah Transaksi Kas' }}
        </button>
    </div>

    @if (session()->has('success'))
        <div class="p-6 bg-emerald-50 text-emerald-600 rounded-3xl text-xs font-semibold border border-emerald-100 flex items-center gap-3">
             <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="p-6 bg-rose-50 text-rose-600 rounded-3xl text-xs font-semibold border border-rose-100 flex items-center gap-3">
             {{ session('error') }}
        </div>
    @endif

    <!-- Alert for inactive shift -->
    @if(!$currentShift)
        <div class="p-6 bg-yellow-50 text-yellow-700 rounded-3xl text-xs font-semibold border border-yellow-200 flex items-center gap-3">
             <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
             Peringatan: Anda harus membuka shift di modul Kasir (POS) sebelum menambah transaksi kas baru!
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm relative overflow-hidden">
            @if(!$currentShift)
                <div class="absolute inset-0 z-10 bg-white/60 dark:bg-slate-900/60 backdrop-blur-sm flex flex-col items-center justify-center">
                    <x-heroicon-o-lock-closed class="w-12 h-12 text-slate-400 mb-4" />
                    <p class="text-xs font-bold text-slate-500 text-center px-6">Shift Ditutup</p>
                    <p class="text-xs font-medium text-slate-400 mt-2">Tidak dapat menambah atau mengurangi saldo kas tanpa shift yang aktif.</p>
                </div>
            @endif

            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Catat Transaksi Kas</h3>
            <form wire:submit.prevent="store" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-8">
                        <div class="group">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Jenis Transaksi</label>
                            <select wire:model="type" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                <option value="">Pilih Jenis Transaksi</option>
                                <option value="in">Kas Masuk (Setoran / Tambah Modal)</option>
                                <option value="out">Kas Keluar (Pengeluaran Operasional)</option>
                            </select>
                            @error('type') <span class="text-xs text-rose-500 font-medium mt-2">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Jumlah (Rp)</label>
                            <input type="number" wire:model="amount" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            @error('amount') <span class="text-xs text-rose-500 font-medium mt-2">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="group">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 px-1">Catatan / Alasan Transaksi</label>
                            <textarea wire:model="notes" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" rows="5" placeholder="Alasan transaksi kas ini (contoh: 'Beli air mineral untuk toko')..."></textarea>
                            @error('notes') <span class="text-xs text-rose-500 font-medium mt-2">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-4 text-xs font-semibold text-slate-400 hover:text-slate-600 transition-all">Batal</button>
                    <button type="submit" class="px-12 py-4 bg-indigo-600 text-white rounded-2xl text-xs font-semibold shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all" {{ !$currentShift ? 'disabled' : '' }}>
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="p-4 border-b border-slate-50 dark:border-slate-800">
                <input type="text" wire:model.live="searchTerm" placeholder="Cari transaksi berdasarkan catatan..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-3 px-6 focus:ring-4 focus:ring-indigo-500/10 transition-all">
            </div>

            <div class="overflow-x-auto p-4 pb-0">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 rounded-l-2xl">Tanggal & Jenis</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Keterangan</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400">Kasir / Petugas</th>
                            <th class="px-8 py-5 text-xs font-semibold text-slate-500 dark:text-slate-400 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @forelse($transactions as $transaction)
                            <tr class="group">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $transaction->created_at->format('d M, H:i') }}</p>
                                    <span class="px-2 py-0.5 mt-1 inline-block rounded text-xs font-semibold {{ $transaction->type === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        Kas {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $transaction->notes }}</p>
                                    @if($transaction->shift && $transaction->shift->status === 'open')
                                        <p class="text-xs text-indigo-500 font-medium mt-1">Shift Aktif</p>
                                    @elseif($transaction->shift)
                                        <p class="text-xs text-slate-400 font-medium mt-1">Shift tanggal {{ $transaction->shift->created_at->format('d/m') }}</p>
                                    @endif
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <span class="text-xs font-medium text-slate-500">Oleh {{ $transaction->user->name ?? 'Sistem' }}</span>
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
                                    <p class="text-xs font-medium text-slate-400">Belum Ada Catatan Transaksi Kas</p>
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
