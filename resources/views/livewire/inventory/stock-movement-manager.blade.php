<div>
    <div class="mb-6 flex justify-between items-center">
        <div class="flex-1 max-w-sm">
            <x-text-input wire:model.live.debounce.300ms="searchTerm" type="text" placeholder="Search by Product Name..." class="w-full" />
        </div>
        <x-primary-button wire:click="$toggle('showForm')" class="ml-4">
            {{ $showForm ? 'Close Form' : 'Add Stock Movement' }}
        </x-primary-button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($showForm)
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Record Stock Movement</h3>
            
            <form wire:submit="store">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="product_id" value="Product" />
                        <select wire:model="product_id" id="product_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">Select a Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Movement Type" />
                        <select wire:model="type" id="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">Select Type</option>
                            <option value="purchase">Purchase (Stock In)</option>
                            <option value="opname_add">Opname - Add (Found/Correction)</option>
                            <option value="opname_deduct">Opname - Deduct (Lost/Damaged)</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="quantity" value="Quantity" />
                        <x-text-input wire:model="quantity" id="quantity" type="number" min="1" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notes (Optional)" />
                        <x-text-input wire:model="notes" id="notes" type="text" class="mt-1 block w-full" placeholder="e.g. Broken package, Restock PO#123" />
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="resetFields" class="mr-3">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button>
                        Save Record
                    </x-primary-button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($movements as $movement)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $movement->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $movement->product->name ?? 'Deleted Product' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($movement->type === 'purchase')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Purchase</span>
                                @elseif($movement->type === 'sale')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sale</span>
                                @elseif($movement->type === 'opname_add')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Opname (In)</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Opname (Out)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ in_array($movement->type, ['sale', 'opname_deduct']) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ in_array($movement->type, ['sale', 'opname_deduct']) ? '-' : '+' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $movement->notes ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $movement->user->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                No stock movements found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $movements->links() }}
        </div>
    </div>
</div>
