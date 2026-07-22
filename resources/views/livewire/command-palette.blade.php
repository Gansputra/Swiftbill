<div x-data="{
        open: false,
        selectedIndex: 0,
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.selectedIndex = 0;
                $nextTick(() => {
                    if ($refs.searchInput) $refs.searchInput.focus();
                });
            }
        },
        close() {
            this.open = false;
        }
    }"
    @keydown.window.ctrl.k.prevent="toggle()"
    @keydown.window.cmd.k.prevent="toggle()"
    @open-command-palette.window="open = true; $nextTick(() => { if($refs.searchInput) $refs.searchInput.focus(); })"
    @keydown.escape.window="close()"
    x-show="open"
    x-cloak
    class="relative z-[300]">

    <!-- Glassmorphic Backdrop -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xl"></div>

    <!-- Spotlight Command Palette Box -->
    <div class="fixed inset-0 flex items-start justify-center pt-16 md:pt-24 px-4 pointer-events-none">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
             class="pointer-events-auto relative w-full max-w-2xl bg-white/95 dark:bg-slate-900/95 border border-slate-200/80 dark:border-slate-800 rounded-[2.5rem] shadow-2xl shadow-indigo-500/10 overflow-hidden flex flex-col max-h-[80vh]">

            <!-- Top Search Input Header -->
            <div class="p-6 border-b border-slate-100 dark:border-slate-800/80 flex items-center gap-4 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex-shrink-0">
                    <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                </div>
                <input x-ref="searchInput"
                       type="text" 
                       wire:model.live.debounce.150ms="search"
                       placeholder="Ketik pencarian (produk, SKU, menu navigasi)..." 
                       class="w-full bg-transparent border-none text-base md:text-lg font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0 outline-none p-0">
                
                @if(!empty($search))
                    <button wire:click="$set('search', '')" class="text-xs text-slate-400 hover:text-rose-500 px-2 py-1 rounded-lg transition-colors">
                        Bersihkan
                    </button>
                @endif

                <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
                    ESC
                </span>
            </div>

            <!-- Scrollable Results Feed -->
            <div class="flex-grow overflow-y-auto p-4 space-y-6">

                <!-- Section 1: Product Search Results (If query active & products exist) -->
                @if(count($products) > 0)
                    <div>
                        <div class="px-4 py-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                            <x-heroicon-o-cube class="w-4 h-4" />
                            Katalog Produk Match
                        </div>
                        <div class="space-y-1 mt-1">
                            @foreach($products as $product)
                                <a href="{{ route('products.index') }}" 
                                   @click="close()"
                                   class="flex items-center justify-between p-3.5 rounded-2xl hover:bg-indigo-50/70 dark:hover:bg-indigo-900/20 transition-all group border border-transparent hover:border-indigo-100 dark:hover:border-indigo-800/30">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800 p-0.5 overflow-hidden flex-shrink-0 border border-slate-200/60 dark:border-slate-700">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full object-cover rounded-lg">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-[9px] font-bold text-slate-400">P</div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $product->name }}
                                            </p>
                                            <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                                                <span class="font-semibold text-slate-500">{{ $product->sku }}</span>
                                                <span>•</span>
                                                <span>{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0 pl-3">
                                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                        </p>
                                        <span class="text-[10px] font-medium {{ $product->stock <= $product->min_stock ? 'text-rose-500' : 'text-slate-400' }}">
                                            Stok: {{ number_format($product->stock, 0) }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Section 2: Navigation Items -->
                @if(count($navigation) > 0)
                    <div>
                        <div class="px-4 py-2 text-xs font-semibold text-slate-400 dark:text-slate-500 flex items-center gap-2">
                            <x-heroicon-o-rectangle-group class="w-4 h-4" />
                            Menu & Navigasi Cepat
                        </div>
                        <div class="space-y-1 mt-1">
                            @foreach($navigation as $nav)
                                <a href="{{ $nav['route'] }}" 
                                   @click="close()"
                                   class="flex items-center justify-between p-3.5 rounded-2xl hover:bg-slate-100/80 dark:hover:bg-slate-800/60 transition-all group border border-transparent hover:border-slate-200/60 dark:hover:border-slate-800">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-all flex-shrink-0">
                                            @if($nav['icon'] === 'home')
                                                <x-heroicon-o-home class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'shopping-cart')
                                                <x-heroicon-o-shopping-cart class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'banknotes')
                                                <x-heroicon-o-banknotes class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'tag')
                                                <x-heroicon-o-tag class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'truck')
                                                <x-heroicon-o-truck class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'cube')
                                                <x-heroicon-o-cube class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'arrows-right-left')
                                                <x-heroicon-o-arrows-right-left class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'light-bulb')
                                                <x-heroicon-o-light-bulb class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'chart-bar')
                                                <x-heroicon-o-chart-bar class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'clock')
                                                <x-heroicon-o-clock class="w-4 h-4" />
                                            @elseif($nav['icon'] === 'users')
                                                <x-heroicon-o-users class="w-4 h-4" />
                                            @else
                                                <x-heroicon-o-user-circle class="w-4 h-4" />
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $nav['title'] }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 font-normal truncate mt-0.5">
                                                {{ $nav['subtitle'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <x-heroicon-o-chevron-right class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:translate-x-0.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-all flex-shrink-0" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Section 3: Empty Search Result State -->
                @if(!empty($search) && count($products) === 0 && count($navigation) === 0)
                    <div class="py-16 text-center">
                        <x-heroicon-o-magnifying-glass class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto mb-3" />
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Tidak ada hasil untuk "{{ $search }}"</p>
                        <p class="text-[11px] text-slate-400 mt-1">Coba kata kunci lain seperti nama produk, SKU, atau nama menu.</p>
                    </div>
                @endif

            </div>

            <!-- Footer Quick Shortcut Legend -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between text-[11px] text-slate-400 dark:text-slate-500 gap-2">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5">
                        <kbd class="px-2 py-0.5 bg-white dark:bg-slate-800 rounded-md border border-slate-200 dark:border-slate-700 font-mono text-[10px] text-slate-600 dark:text-slate-300">Ctrl + K</kbd>
                        <span>Pintasan</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <kbd class="px-2 py-0.5 bg-white dark:bg-slate-800 rounded-md border border-slate-200 dark:border-slate-700 font-mono text-[10px] text-slate-600 dark:text-slate-300">ESC</kbd>
                        <span>Tutup</span>
                    </span>
                </div>
                <div class="flex items-center gap-1.5 font-medium text-slate-500 dark:text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>SwiftBill Command Palette v2.0</span>
                </div>
            </div>

        </div>
    </div>
</div>
