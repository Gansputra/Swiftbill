<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Staff Directory</h2>
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">Access & Permissions</p>
        </div>
        <button wire:click="openCreateModal" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900 transition-all flex items-center gap-2">
            <x-heroicon-o-user-plus class="w-5 h-5" />
            {{ $isModalOpen ? 'View All Staff' : 'Register Employee' }}
        </button>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-indigo-600 p-8 rounded-[2rem] text-white shadow-xl shadow-indigo-500/20 flex flex-col justify-between">
            <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Active Personnel</span>
            <div class="flex items-baseline gap-2 mt-4">
                <span class="text-4xl font-black tracking-tighter">{{ count($users) }}</span>
                <span class="text-xs font-bold opacity-60">Members</span>
            </div>
        </div>
        <div class="md:col-span-2 bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 flex items-center justify-between group">
            <div class="flex-grow">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Access Management</span>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-400 max-w-lg">Grant secure access to your cashiers and managers.</p>
            </div>
            <x-heroicon-o-shield-check class="w-10 h-10 text-slate-200" />
        </div>
    </div>

    @if($isModalOpen)
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-10 shadow-sm">
            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wider mb-8">{{ $isEditMode ? 'Modify Personnel' : 'Credential Setup' }}</h3>
            <form wire:submit.prevent="saveUser" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Full Legal Name</label>
                        <input type="text" wire:model="name" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                        @error('name') <span class="text-[10px] text-rose-500 font-bold mt-2 px-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Email Address</label>
                        <input type="email" wire:model="email" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                        @error('email') <span class="text-[10px] text-rose-500 font-bold mt-2 px-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">{{ $isEditMode ? 'New Password (Optional)' : 'Security Clearance (Password)' }}</label>
                        <input type="password" wire:model="password" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                        @error('password') <span class="text-[10px] text-rose-500 font-bold mt-2 px-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Authorization Role</label>
                        <select wire:model="role" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                            <option value="cashier">Standard Cashier</option>
                            <option value="admin">System Administrator</option>
                        </select>
                        @error('role') <span class="text-[10px] text-rose-500 font-bold mt-2 px-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-slate-800">
                    <button type="button" wire:click="closeModal" class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cancel</button>
                    <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-slate-900">
                        {{ $isEditMode ? 'Commit Modification' : 'Confirm Registration' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm overflow-hidden text-sm">
            <div class="overflow-x-auto p-4">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-l-2xl">User Identity</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Clearance</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right rounded-r-2xl">Settings</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @foreach($users as $user)
                            <tr class="group">
                                <td class="px-8 py-6 rounded-l-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/40 rounded-full flex items-center justify-center text-indigo-600 font-black text-xs border border-indigo-100 dark:border-indigo-800">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                             <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter">{{ $user->name }}</p>
                                             <p class="text-[10px] text-slate-400 font-bold lowercase">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all">
                                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $user->role === 'admin' ? 'text-indigo-600 border border-indigo-100 dark:border-indigo-800' : 'text-slate-500 border border-slate-200 dark:border-slate-700' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 rounded-r-2xl group-hover:bg-slate-50/50 dark:group-hover:bg-slate-800/30 transition-all text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-30 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="openEditModal({{ $user->id }})" class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </button>
                                        <button onclick="confirm('Permanent deletion?') || event.stopImmediatePropagation()" wire:click="deleteUser({{ $user->id }})" class="p-2.5 bg-rose-50 dark:bg-rose-900/30 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
