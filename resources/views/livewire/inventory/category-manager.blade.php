<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Product Categories</h2>
            <p class="text-xs text-slate-500 uppercase tracking-widest font-bold">Inventory Management</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 text-green-600 rounded-2xl text-[10px] font-bold border border-green-100 uppercase tracking-wide">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Form Section -->
        <div class="md:col-span-4 self-start bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-6 uppercase tracking-wider">{{ $isEditing ? 'Edit Category' : 'Add Category' }}</h3>
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Category Name</label>
                    <input type="text" wire:model="name" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500">
                    @error('name') <span class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Description</label>
                    <textarea wire:model="description" rows="4" class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-3 px-4 focus:ring-2 focus:ring-indigo-500"></textarea>
                    @error('description') <span class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col space-y-3 pt-2">
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-indigo-100 dark:shadow-none hover:bg-slate-900 transition-all">
                        {{ $isEditing ? 'Update Records' : 'Save Category' }}
                    </button>
                    @if($isEditing)
                        <button type="button" wire:click="resetFields" class="w-full py-3 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">
                            Cancel Action
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- List Section -->
        <div class="md:col-span-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <input type="text" wire:model.live="searchTerm" placeholder="Search categories..." class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Name</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Identifier</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $category->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $category->slug }}</span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-4">
                                    <button wire:click="edit({{ $category->id }})" class="text-[10px] font-bold text-indigo-600 uppercase tracking-tighter hover:text-slate-900 transition">Edit</button>
                                    <button onclick="confirm('Proceed with deletion?') || event.stopImmediatePropagation()" wire:click="delete({{ $category->id }})" class="text-[10px] font-bold text-rose-500 uppercase tracking-tighter hover:text-slate-900 transition">Archive</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-20 text-center flex flex-col items-center justify-center opacity-40">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No data entries available</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-slate-50 dark:bg-slate-800/50">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
