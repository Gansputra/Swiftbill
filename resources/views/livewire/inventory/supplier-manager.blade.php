<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Suppliers</h2>
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">Procurement Network</p>
        </div>
        <button wire:click="$toggle('showForm')" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-user-plus class="w-5 h-5" />
            {{ $showForm ? 'View Directory' : 'Register Supplier' }}
        </button>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-indigo-600 p-8 rounded-[2rem] text-white shadow-xl shadow-indigo-500/20 flex flex-col justify-between">
            <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Vendor Count</span>
            <div class="flex items-baseline gap-2 mt-4">
                <span class="text-4xl font-black tracking-tighter">{{ count($suppliers) }}</span>
                <span class="text-xs font-bold opacity-60">Verified</span>
            </div>
        </div>
        <div class="md:col-span-3 bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 flex items-center justify-between group">
            <div class="flex-grow">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Supply Chain Logic</span>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-400 max-w-lg">Manage your vendor relationships and procurement contact details in one unified repository.</p>
            </div>
            <x-heroicon-o-truck class="w-10 h-10 text-slate-200" />
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-6 bg-emerald-50 text-emerald-600 rounded-3xl text-[10px] font-black border border-emerald-100 uppercase tracking-widest flex items-center gap-3">
             <x-heroicon-o-check-badge class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm">
            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wider mb-8">{{ $isEditing ? 'Modify Supplier' : 'Register New Vendor' }}</h3>
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Vendor Name</label>
                        <input type="text" wire:model="name" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                        @error('name') <span class="text-[10px] text-rose-500 font-bold mt-2">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Email Address</label>
                        <input type="email" wire:model="email" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Phone Number</label>
                        <input type="text" wire:model="phone" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                    </div>
                    
                    <div class="md:col-span-2 group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">HQ Address</label>
                        <textarea wire:model="address" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none" rows="3"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="resetFields" class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cancel</button>
                    <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900">
                        {{ $isEditing ? 'Commit Update' : 'Authorize Vendor' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="p-6 border-b border-slate-50 dark:border-slate-800">
                <input type="text" wire:model.live="searchTerm" placeholder="Search vendor directory..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="overflow-x-auto p-4 pb-0">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-2xl">Vendor Name</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email Address</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Phone</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">HQ Address</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right rounded-r-2xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @foreach($suppliers as $supplier)
                            <tr class="group">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter">{{ $supplier->name }}</p>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ $supplier->email ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <span class="text-[14px] font-bold text-slate-500">{{ $supplier->phone ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <p class="text-[14px] text-slate-400 font-medium truncate max-w-xs">{{ $supplier->address ?? '-' }}</p>
                                </td>
                                <td class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-30 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="edit({{ $supplier->id }})" class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </button>
                                        <button onclick="confirm('Permanent deletion?') || event.stopImmediatePropagation()" wire:click="delete({{ $supplier->id }})" class="p-2 bg-rose-50 dark:bg-rose-900/30 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white">
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
