<div class="h-full relative overflow-hidden">
    @if (!$currentShift)
        <!-- Open Shift Overlay - Bento Style -->
        <div
            class="absolute inset-0 z-50 flex items-center justify-center bg-slate-50/60 dark:bg-slate-950/60 backdrop-blur-xl rounded-[3rem]">
            <div
                class="bg-white dark:bg-slate-900 p-10 rounded-[3rem] shadow-2xl max-w-md w-full border border-white dark:border-slate-800">
                <div class="text-center mb-10">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-3xl mb-6 shadow-inner">
                        <x-heroicon-o-key class="w-10 h-10" />
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">Shift Authorization
                    </h2>
                    <p class="text-[10px] text-slate-400 uppercase tracking-[0.3em] font-black mt-3">Initial cash
                        declaration required</p>
                </div>

                @if ($hasShiftError)
                    <div
                        class="mb-8 p-5 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center">
                        {{ $hasShiftError }}
                    </div>
                @endif

                <div class="space-y-8">
                    <div class="group">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 text-center">Starting
                            Balance in Drawer</label>
                        <div class="relative">
                            <span
                                class="absolute left-6 top-1/2 -translate-y-1/2 text-xl font-black text-slate-300">Rp</span>
                            <input type="number" wire:model="startingCash"
                                class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-[2rem] text-3xl py-6 pl-16 pr-8 text-center focus:ring-4 focus:ring-indigo-500/10 font-black text-slate-900 dark:text-white"
                                placeholder="0">
                        </div>
                    </div>

                    <button wire:click="openShift"
                        class="w-full py-6 bg-indigo-600 hover:bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-[0.3em] shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all">
                        Initialize Shift
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($isClosingShift)
        <!-- Close Shift Modal - Bento Style -->
        <div
            class="absolute inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-md rounded-[3rem] p-4">
            <div
                class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] shadow-2xl max-w-md w-full border border-slate-200 dark:border-slate-800">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">Terminate Shift
                        </h2>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mt-1">Cash
                            reconciliation</p>
                    </div>
                    <button wire:click="$set('isClosingShift', false)"
                        class="p-3 bg-slate-50 dark:bg-slate-800 rounded-2xl text-slate-400 hover:text-rose-500 transition-all">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <div class="space-y-8">
                    <div class="bg-indigo-600 p-8 rounded-[2rem] text-white shadow-xl shadow-indigo-500/20">
                        <span
                            class="block text-[10px] font-black uppercase tracking-[0.2em] opacity-70 mb-2 text-center">Expected
                            Ledger Balance</span>
                        <span class="block text-3xl font-black text-center tracking-tighter">Rp
                            {{ number_format($this->calculateExpectedCash(), 0) }}</span>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Actual
                                Physical Cash</label>
                            <input type="number" wire:model="actualCash"
                                class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xl py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 font-black text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Observational
                                Notes</label>
                            <textarea wire:model="closingNotes"
                                class="block w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10"
                                rows="3" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>

                    <button wire:click="confirmCloseShift"
                        class="w-full py-5 bg-rose-500 hover:bg-rose-600 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-[0.3em] shadow-2xl shadow-rose-500/20 active:scale-95 transition-all">
                        Finalize & Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Bento POS Layout -->
    <div
        class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full p-2 {{ !$currentShift || $isClosingShift ? 'opacity-10 pointer-events-none blur-xl' : '' }} transition-all duration-700">

        <!-- Column Left: Catalog (8/12) -->
        <div class="lg:col-span-8 flex flex-col gap-6 overflow-hidden">

            <!-- Box 1: Search & Filter Bento -->
            <div
                class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-4 flex items-center gap-4 shadow-sm group">
                <div class="flex-grow relative">
                    <x-heroicon-o-magnifying-glass
                        class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors" />
                    <input type="text" wire:model.live="searchTerm" placeholder="Scan SKU or Search for products..."
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-[1.8rem] text-sm font-bold py-4 pl-14 pr-6 focus:ring-4 focus:ring-indigo-500/10 placeholder-slate-300 transition-all">
                </div>
                <div
                    class="hidden md:flex items-center gap-2 px-6 py-4 border-l border-slate-100 dark:border-slate-800">
                    <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                    <span
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Cashier:
                        {{ auth()->user()->name }}</span>
                </div>
                <button wire:click="initiateCloseShift"
                    class="p-4 bg-rose-50 dark:bg-rose-950/30 text-rose-500 rounded-2xl hover:bg-rose-500 hover:text-white transition-all group/btn">
                    <x-heroicon-o-power class="w-6 h-6 group-hover/btn:rotate-12 transition-transform" />
                </button>
            </div>

            <!-- Box 2: Product Grid Bento -->
            <div
                class="flex-grow bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-8 overflow-y-auto shadow-sm">
                <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-6">
                    @forelse($products as $product)
                        <div wire:click="addToCart({{ $product->id }})" class="group cursor-pointer">
                            <div
                                class="relative aspect-square rounded-[2rem] bg-slate-50 dark:bg-slate-800 border border-transparent group-hover:border-indigo-500 transition-all overflow-hidden shadow-sm group-hover:shadow-xl group-hover:shadow-indigo-500/10 group-active:scale-95">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                        class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="h-full w-full flex items-center justify-center text-[10px] font-black text-slate-300 uppercase opacity-50">
                                        SKU Void</div>
                                @endif

                                <div
                                    class="absolute inset-0 bg-indigo-600/0 group-hover:bg-indigo-600/80 p-6 flex flex-col justify-end opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <p class="text-white text-[10px] font-black uppercase tracking-widest mb-1">Add to
                                        Cart</p>
                                    <p class="text-white text-lg font-black tracking-tighter">Rp
                                        {{ number_format($product->sell_price, 0) }}</p>
                                </div>
                            </div>
                            <div class="mt-4 px-2">
                                <p
                                    class="text-xs font-black text-slate-800 dark:text-white truncate uppercase tracking-tighter">
                                    {{ $product->name }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                    {{ $product->stock }} in stock</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-40 flex flex-col items-center justify-center grayscale opacity-30">
                            <x-heroicon-o-cube-transparent class="w-16 h-16 text-slate-300 mb-6" />
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Empty Catalog
                                State</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Column Right: Checkout (4/12) - Bento Box Taller -->
        <div class="lg:col-span-4 flex flex-col gap-6 overflow-hidden">

            <!-- Box 3: Cart Feed Bento -->
            <div
                class="flex-grow bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[3rem] shadow-2xl flex flex-col overflow-hidden relative">
                <div
                    class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-[80px] pointer-events-none">
                </div>

                <div
                    class="relative p-8 border-b border-slate-200/80 dark:border-white/5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tighter">Current Order
                        </h3>
                        <p
                            class="text-[9px] font-black text-slate-500 dark:text-white/40 uppercase tracking-widest mt-1">
                            {{ count($cart) }} line items</p>
                    </div>
                    <button wire:click="removeFromCart('all')"
                        class="text-[9px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-700 transition">Clear
                        all</button>
                </div>

                <div class="relative flex-grow overflow-y-auto p-6 space-y-4">
                    @forelse($cart as $id => $item)
                        <div
                            class="group bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 px-6 py-5 rounded-[2rem] flex items-center gap-4 hover:bg-slate-100 dark:hover:bg-white/10 transition-all">
                            <div class="flex-grow min-w-0 font-bold text-slate-900 dark:text-white">
                                <h4 class="text-xs truncate uppercase tracking-tighter">{{ $item['name'] }}</h4>
                                <p class="text-[10px] text-indigo-600 dark:text-indigo-400 mt-1">Rp
                                    {{ number_format($item['sell_price'], 0) }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-white hover:bg-rose-500 dark:hover:bg-rose-500 hover:text-white transition-all font-black text-xl leading-none">-</button>
                                <span
                                    class="text-base font-black text-slate-900 dark:text-white w-4 text-center">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-white hover:bg-indigo-500 dark:hover:bg-indigo-500 hover:text-white transition-all font-black text-xl leading-none">+</button>
                            </div>
                            <button wire:click="removeFromCart({{ $id }})"
                                class="opacity-0 group-hover:opacity-100 p-2 text-slate-500 hover:text-rose-500 transition-all">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center py-20 opacity-20">
                            <x-heroicon-o-shopping-bag class="w-12 h-12 text-slate-400 dark:text-white mb-4" />
                            <p class="text-[10px] font-black text-slate-500 dark:text-white uppercase tracking-widest">
                                Awaiting selection...</p>
                        </div>
                    @endforelse
                </div>

                <!-- Box 4: Execution Bento (Sticky Bottom) -->
                <div
                    class="relative p-8 bg-slate-50 dark:bg-white/5 border-t border-slate-200/60 dark:border-white/10 space-y-8">
                    <div class="space-y-4">
                        @if ($this->totalDiscount > 0)
                            <div class="flex justify-between items-center px-2">
                                <span
                                    class="text-[10px] font-black text-slate-500 dark:text-white/40 uppercase tracking-widest">Global
                                    Savings</span>
                                <span class="text-sm font-black text-rose-500 tracking-tighter">- Rp
                                    {{ number_format($this->totalDiscount, 0) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center px-2">
                            <span
                                class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest">Settlement</span>
                            <span class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">Rp
                                {{ number_format($this->total, 0) }}</span>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div
                            class="grid grid-cols-2 gap-px bg-slate-200/50 dark:bg-white/10 border border-slate-200/50 dark:border-white/10 rounded-[1.8rem] overflow-hidden">
                            <div class="p-4 bg-slate-50 dark:bg-white/5">
                                <label
                                    class="block text-[8px] font-black text-slate-500 dark:text-white/40 uppercase tracking-widest mb-1 px-1">Instrument</label>
                                <select wire:model.live="paymentMethod"
                                    class="w-full bg-transparent border-none p-0 text-[10px] font-black text-indigo-600 dark:text-indigo-400 focus:ring-0 uppercase tracking-widest cursor-pointer">
                                    <option value="cash">Cash</option>
                                    <option value="qris">QRIS</option>
                                    <option value="transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-white/5">
                                <label
                                    class="block text-[8px] font-black text-slate-500 dark:text-white/40 uppercase tracking-widest mb-1 px-1">Received</label>
                                <input type="number" wire:model.live="totalPaid"
                                    class="w-full bg-transparent border-none p-0 text-sm font-black text-slate-900 dark:text-white focus:ring-0 placeholder-slate-400 dark:placeholder-white/20"
                                    placeholder="0" {{ $paymentMethod !== 'cash' ? 'readonly' : '' }}>
                            </div>
                        </div>

                        @if (session()->has('error'))
                            <div class="px-6 py-4 mb-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center animate-bounce">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div
                            class="flex items-center justify-between px-6 py-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-200/50 dark:border-white/10">
                            <span
                                class="text-[9px] font-black text-slate-500 dark:text-white/40 uppercase tracking-widest">Change</span>
                            <span
                                class="text-lg font-black {{ $this->change >= 0 ? 'text-emerald-400' : 'text-rose-500 animate-pulse' }}">Rp
                                {{ number_format($this->change, 0) }}</span>
                        </div>

                        <button wire:click="checkout" wire:loading.attr="disabled"
                            class="group relative w-full py-6 bg-indigo-600 hover:bg-white text-white hover:text-indigo-950 disabled:opacity-50 disabled:cursor-not-allowed rounded-[2rem] font-black text-[11px] uppercase tracking-[0.4em] shadow-2xl shadow-indigo-600/30 active:scale-95 transition-all duration-500 overflow-hidden">
                            <span class="relative z-10 flex items-center justify-center gap-3" wire:loading.remove wire:target="checkout">
                                {{ $snapToken ? 'RESUME PAYMENT' : 'PAY' }}
                                <x-heroicon-o-arrow-right-circle
                                    class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                            </span>
                            <span class="relative z-10 flex items-center justify-center gap-3 animate-pulse" wire:loading wire:target="checkout">
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-midtrans', (event) => {
                const snapToken = event.snapToken;
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        @this.finalizeTransaction(result);
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        alert('Payment is pending. Please complete the payment.');
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        alert('Payment failed!');
                    },
                    onClose: function() {
                        console.log('Customer closed the popup without finishing the payment');
                    }
                });
            });
        });
    </script>
@endpush
</div>
