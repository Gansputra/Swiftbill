<div class="h-full flex flex-col space-y-6">
    <div class="flex-grow grid grid-cols-1 lg:grid-cols-12 gap-8 overflow-hidden">
        
        <!-- Product Catalog (Left) -->
        <div class="lg:col-span-8 flex flex-col space-y-6 overflow-hidden">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Catalog</h3>
                </div>
                <div class="w-1/2">
                    <input type="text" wire:model.live="searchTerm" placeholder="Search products..." class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex-grow overflow-y-auto grid grid-cols-2 md:grid-cols-4 gap-4 pr-1 pb-4">
                @forelse($products as $product)
                    <div wire:click="addToCart({{ $product->id }})" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 cursor-pointer hover:border-indigo-500 hover:shadow-lg transition-all group relative">
                        <div class="aspect-square mb-3 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden relative">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-[10px] text-slate-400 font-bold uppercase">No Photo</div>
                            @endif
                            <div class="absolute inset-0 bg-indigo-600/0 group-hover:bg-indigo-600/10 transition-colors"></div>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate mb-1">{{ $product->name }}</h4>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-indigo-600">Rp {{ number_format($product->sell_price, 0) }}</span>
                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">{{ $product->stock }} Left</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center flex flex-col items-center justify-center space-y-3">
                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No matching products found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Checkout Section (Right) -->
        <div class="lg:col-span-4 flex flex-col space-y-6 overflow-hidden">
            <div class="flex-grow bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl flex flex-col overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Current Cart</h3>
                    <span class="text-[10px] font-bold px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-500">{{ count($cart) }} Items</span>
                </div>

                <div class="flex-grow overflow-y-auto p-4 space-y-3">
                    @if (session()->has('error'))
                        <div class="p-4 bg-rose-50 text-rose-600 rounded-xl text-[10px] font-bold border border-rose-100 uppercase tracking-wide">
                            {{ session('error') }}
                        </div>
                    @endif

                    @forelse($cart as $id => $item)
                        <div class="group bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl flex flex-col transition hover:bg-slate-100 dark:hover:bg-slate-800 relative space-y-2">
                            <div class="flex items-start justify-between">
                                <div class="flex-grow min-w-0 pr-4">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $item['name'] }}</h4>
                                    <p class="text-[10px] font-bold text-indigo-500">Rp {{ number_format($item['sell_price'], 0) }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-1">
                                        <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="p-1 hover:text-indigo-600"><svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
                                        <span class="text-xs font-bold w-6 text-center text-slate-900 dark:text-white">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="p-1 hover:text-indigo-600"><svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                                    </div>
                                    <button wire:click="removeFromCart({{ $id }})" class="text-slate-300 hover:text-rose-500 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center text-[10px] font-bold">
                                <span class="text-slate-400 mr-2 uppercase">Disc Rp:</span>
                                <input type="number" min="0" wire:change="updateDiscount({{ $id }}, $event.target.value)" value="{{ $item['discount'] ?? 0 }}" class="w-20 h-6 px-1 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 rounded text-center text-rose-500 focus:ring-0 focus:border-indigo-500" placeholder="0">
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center space-y-4 py-20 opacity-30">
                            <div class="w-16 h-16 border-2 border-dashed border-slate-300 rounded-full flex items-center justify-center">
                                <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cart is empty</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-6 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 space-y-6">
                    @if($this->totalDiscount > 0)
                    <div class="flex justify-between items-end border-b border-dashed border-slate-200 dark:border-slate-700 pb-2">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Discount</span>
                        <span class="text-sm font-bold text-rose-500 tracking-tighter">- Rp {{ number_format($this->totalDiscount, 0) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Grand Total</span>
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tighter">Rp {{ number_format($this->total, 0) }}</span>
                    </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b dark:border-slate-800 pb-2">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-tight">Method</span>
                                <select wire:model.live="paymentMethod" class="text-xs font-bold bg-transparent border-none focus:ring-0 p-0 text-indigo-600 cursor-pointer">
                                    <option value="cash">Cash Payment</option>
                                    <option value="qris">QRIS Digital</option>
                                    <option value="transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl">
                                    <span class="text-[8px] font-bold text-slate-400 uppercase block mb-1">Received</span>
                                    <input type="number" wire:model.live="totalPaid" class="w-full text-sm font-bold bg-transparent border-none focus:ring-0 p-0 text-slate-900 dark:text-white" 
                                           placeholder="0" 
                                           {{ $paymentMethod !== 'cash' ? 'readonly' : '' }}>
                                </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl">
                                <span class="text-[8px] font-bold text-slate-400 uppercase block mb-1">Return</span>
                                <span class="text-sm font-bold {{ $this->change >= 0 ? 'text-green-500' : 'text-rose-500' }}">Rp {{ number_format($this->change, 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <button wire:click="checkout" class="group relative w-full py-4 bg-indigo-600 hover:bg-slate-900 text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 dark:shadow-none transition-all overflow-hidden">
                        <span class="relative z-10">Complete Checkout</span>
                        <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
