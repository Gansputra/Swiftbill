<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ 
          darkMode: localStorage.getItem('dark-mode') === 'true',
          mobileMenu: false,
          scrolled: false,
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('dark-mode', this.darkMode);
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          },
          closeMobileMenu() {
              this.mobileMenu = false;
          }
      }"
      x-init="
          $watch('darkMode', val => {
              localStorage.setItem('dark-mode', val);
              val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
          })
      "
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>
        
        <script>
            if (localStorage.getItem('dark-mode') === 'true' || (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
        
        <style>
            body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; }
            [x-cloak] { display: none !important; }
            
            /* Sembunyikan semua scrollbar jadul secara global ditiap halaman */
            * {
                -ms-overflow-style: none !important;  /* IE & Edge */
                scrollbar-width: none !important;  /* Firefox */
            }
            *::-webkit-scrollbar {
                display: none !important;
                width: 0px !important;
                height: 0px !important;
                background: transparent !important;
            }

            /* Remove number input spinners */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            input[type=number] {
                -moz-appearance: textfield;
            }

            /* Header Title Styles - Uses existing app color palette */
            .header-title-container > h2,
            .header-title-container > h1,
            .header-title-container {
                font-size: 1.625rem; /* 26px */
                font-weight: 700;
                color: inherit;
                line-height: 1.2;
                letter-spacing: -0.025em;
                margin: 0;
                padding: 0;
            }
            @media (max-width: 640px) {
                .header-title-container > h2,
                .header-title-container > h1,
                .header-title-container {
                    font-size: 1.25rem; /* 20px */
                }
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4f46e5">
        <link rel="apple-touch-icon" href="{{ asset('icon-192x192.png') }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
        
        <!-- ApexCharts -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <!-- Cropper.js -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 transition-colors duration-300">
        
        <script>
            document.addEventListener('livewire:navigated', () => {
                if (localStorage.getItem('dark-mode') === 'true') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            });
        </script>

        <div class="flex h-screen overflow-hidden bg-slate-50 dark:bg-slate-950">
            {{-- MOBILE OVERLAY --}}
            <div x-show="mobileMenu" @click="closeMobileMenu()" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden" x-cloak></div>
            
            {{-- MOBILE SIDEBAR --}}
            <aside x-show="mobileMenu" x-transition:enter="transition-transform ease-in-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition-transform ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed z-50 flex flex-col w-64 h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 shadow-sm md:hidden" x-cloak>
                <div class="p-6 flex items-center space-x-3">
                    <div class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">S</div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">SwiftBill</span>
                </div>
                
                {{-- NAV MENU - DISESUAIKAN DENGAN web.php ABANG --}}
                <nav @click="closeMobileMenu()" class="flex-grow px-4 pb-4 space-y-1 overflow-y-auto custom-scrollbar">
                    <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center gap-2">
                        <x-heroicon-o-home class="w-5 h-5"/> Dashboard
                    </x-nav-link-sidebar>
                    
                    <x-nav-link-sidebar :href="route('pos.index')" :active="request()->routeIs('pos.index')" class="flex items-center gap-2">
                        <x-heroicon-o-shopping-cart class="w-5 h-5"/> Kasir (POS)
                    </x-nav-link-sidebar>

                    <x-nav-link-sidebar :href="route('cash-management')" :active="request()->routeIs('cash-management')" class="flex items-center gap-2">
                        <x-heroicon-o-banknotes class="w-5 h-5"/> Manajemen Kas
                    </x-nav-link-sidebar>

                    @if(auth()->user()->role === 'admin')
                        {{-- INVENTORY SECTION --}}
                        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400">Inventaris</div>
                        <x-nav-link-sidebar :href="route('categories.index')" :active="request()->routeIs('categories.index')" class="flex items-center gap-2">
                            <x-heroicon-o-tag class="w-5 h-5"/> Kategori
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('suppliers.index')" :active="request()->routeIs('suppliers.index')" class="flex items-center gap-2">
                            <x-heroicon-o-truck class="w-5 h-5"/> Pemasok
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('products.index')" :active="request()->routeIs('products.index')" class="flex items-center gap-2">
                            <x-heroicon-o-cube class="w-5 h-5"/> Produk
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('stock-movements.index')" :active="request()->routeIs('stock-movements.index')" class="flex items-center gap-2">
                            <x-heroicon-o-arrows-right-left class="w-5 h-5"/> Riwayat Stok
                        </x-nav-link-sidebar>

                        {{-- ANALYTICS SECTION --}}
                        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400">Analisis</div>
                        <x-nav-link-sidebar :href="route('ai-dashboard')" :active="request()->routeIs('ai-dashboard')" class="flex items-center gap-2">
                            <x-heroicon-o-light-bulb class="w-5 h-5"/> Wawasan AI
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('reports.sales')" :active="request()->routeIs('reports.sales')" class="flex items-center gap-2">
                            <x-heroicon-o-chart-bar class="w-5 h-5"/> Laporan Penjualan
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('reports.shifts')" :active="request()->routeIs('reports.shifts')" class="flex items-center gap-2">
                            <x-heroicon-o-clock class="w-5 h-5"/> Log Shift
                        </x-nav-link-sidebar>

                        {{-- ADMINISTRATION SECTION --}}
                        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400">Administrasi</div>
                        <x-nav-link-sidebar :href="route('users.index')" :active="request()->routeIs('users.index')" class="flex items-center gap-2">
                            <x-heroicon-o-users class="w-5 h-5"/> Karyawan
                        </x-nav-link-sidebar>
                    @endif
                </nav>

                {{-- FOOTER SIDEBAR --}}
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-3">
                    <button @click="toggleTheme()" 
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-indigo-500 hover:text-indigo-600 transition-all duration-300">
                        <div class="flex items-center">
                            <span class="text-xs font-semibold" x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                        </div>
                        <div class="flex items-center">
                            <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg x-show="!darkMode" x-cloak class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </div>
                    </button>

                    <livewire:layout.sidebar-profile />
                </div>
            </aside>

            {{-- DESKTOP SIDEBAR --}}
            <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 shadow-sm transition-colors duration-300">
                <div class="p-6 flex items-center space-x-3">
                    <div class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">S</div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">SwiftBill</span>
                </div>
                
                {{-- NAV MENU - DISESUAIKAN DENGAN web.php ABANG --}}
                <nav class="flex-grow px-4 pb-4 space-y-1 overflow-y-auto custom-scrollbar">
                    <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center gap-2">
                        <x-heroicon-o-home class="w-5 h-5"/> Dashboard
                    </x-nav-link-sidebar>
                    
                    <x-nav-link-sidebar :href="route('pos.index')" :active="request()->routeIs('pos.index')" class="flex items-center gap-2">
                        <x-heroicon-o-shopping-cart class="w-5 h-5"/> Kasir (POS)
                    </x-nav-link-sidebar>

                    <x-nav-link-sidebar :href="route('cash-management')" :active="request()->routeIs('cash-management')" class="flex items-center gap-2">
                        <x-heroicon-o-banknotes class="w-5 h-5"/> Manajemen Kas
                    </x-nav-link-sidebar>

                    @if(auth()->user()->role === 'admin')
                        {{-- INVENTORY SECTION --}}
                        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400">Inventaris</div>
                        <x-nav-link-sidebar :href="route('categories.index')" :active="request()->routeIs('categories.index')" class="flex items-center gap-2">
                            <x-heroicon-o-tag class="w-5 h-5"/> Kategori
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('suppliers.index')" :active="request()->routeIs('suppliers.index')" class="flex items-center gap-2">
                            <x-heroicon-o-truck class="w-5 h-5"/> Pemasok
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('products.index')" :active="request()->routeIs('products.index')" class="flex items-center gap-2">
                            <x-heroicon-o-cube class="w-5 h-5"/> Produk
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('stock-movements.index')" :active="request()->routeIs('stock-movements.index')" class="flex items-center gap-2">
                            <x-heroicon-o-arrows-right-left class="w-5 h-5"/> Riwayat Stok
                        </x-nav-link-sidebar>

                        {{-- ANALYTICS SECTION --}}
                        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400">Analisis</div>
                        <x-nav-link-sidebar :href="route('ai-dashboard')" :active="request()->routeIs('ai-dashboard')" class="flex items-center gap-2">
                            <x-heroicon-o-light-bulb class="w-5 h-5"/> Wawasan AI
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('reports.sales')" :active="request()->routeIs('reports.sales')" class="flex items-center gap-2">
                            <x-heroicon-o-chart-bar class="w-5 h-5"/> Laporan Penjualan
                        </x-nav-link-sidebar>
                        <x-nav-link-sidebar :href="route('reports.shifts')" :active="request()->routeIs('reports.shifts')" class="flex items-center gap-2">
                            <x-heroicon-o-clock class="w-5 h-5"/> Log Shift
                        </x-nav-link-sidebar>

                        {{-- ADMINISTRATION SECTION --}}
                        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400">Administrasi</div>
                        <x-nav-link-sidebar :href="route('users.index')" :active="request()->routeIs('users.index')" class="flex items-center gap-2">
                            <x-heroicon-o-users class="w-5 h-5"/> Karyawan
                        </x-nav-link-sidebar>
                    @endif
                </nav>

                {{-- FOOTER SIDEBAR --}}
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-3">
                    <button @click="toggleTheme()" 
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-indigo-500 hover:text-indigo-600 transition-all duration-300">
                        <div class="flex items-center">
                            <span class="text-xs font-semibold" x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                        </div>
                        <div class="flex items-center">
                            <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg x-show="!darkMode" x-cloak class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </div>
                    </button>

                    <livewire:layout.sidebar-profile />
                </div>
            </aside>

            {{-- MAIN CONTENT AREA --}}
            <div class="flex-grow flex flex-col min-w-0 bg-slate-50 dark:bg-slate-900 transition-colors duration-300 relative">
                <header class="h-[76px] bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between px-6 md:px-8 lg:px-10 z-[100] sticky top-0 transition-all duration-300"
                        :class="{ 'shadow-md shadow-slate-900/5 dark:shadow-black/30 border-slate-200 dark:border-slate-800': scrolled, 'shadow-sm': !scrolled }">
                    <div class="flex items-center space-x-3 sm:space-x-4 min-w-0">
                        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/80 border border-slate-200/60 dark:border-slate-800 transition-all duration-200 flex-shrink-0">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        
                        <div class="flex flex-col justify-center min-w-0">
                            <div class="flex items-center space-x-1.5 text-xs font-medium text-slate-400 dark:text-slate-500">
                                <span>SwiftBill</span>
                                <svg class="w-2.5 h-2.5 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Ringkasan</span>
                            </div>
                            <div class="header-title-container font-bold text-xl sm:text-2xl lg:text-[26px] text-slate-900 dark:text-white tracking-tight leading-tight truncate">
                                {{ $header ?? '' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 md:space-x-4 flex-shrink-0">
                        <!-- Spotlight Quick Search Trigger -->
                        <button @click="$dispatch('open-command-palette')"
                                class="hidden sm:flex items-center gap-3 px-4 py-2 bg-slate-100/80 dark:bg-slate-800/60 hover:bg-white dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 hover:border-indigo-500 dark:hover:border-indigo-500 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-2xl transition-all duration-300 shadow-sm group">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" />
                            <span class="text-xs font-medium pr-2">Cari cepat...</span>
                            <kbd class="px-2 py-0.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-semibold text-slate-500 dark:text-slate-400">Ctrl K</kbd>
                        </button>
                        <button @click="$dispatch('open-command-palette')" class="sm:hidden p-2.5 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-xl hover:text-indigo-600">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </button>

                        <livewire:layout.navigation />
                    </div>
                </header>

                <main @scroll="scrolled = ($el.scrollTop > 10)" class="flex-grow overflow-y-auto p-4 md:p-8 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <!-- Ambient Keyboard Shortcut Floating Banner -->
        <div x-data="{ showHint: !localStorage.getItem('hide-ctrlk-hint') }"
             x-show="showHint"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             x-init="setTimeout(() => { showHint = false; }, 9000)"
             class="fixed bottom-6 right-6 z-[90] hidden md:flex items-center gap-3 p-4 bg-slate-900/90 dark:bg-slate-800/90 text-white rounded-2xl shadow-2xl backdrop-blur-xl border border-slate-700/80"
             x-cloak>
            <div class="p-2 bg-indigo-600/30 text-indigo-400 rounded-xl">
                <x-heroicon-o-sparkles class="w-5 h-5 animate-pulse" />
            </div>
            <div class="text-xs">
                <p class="font-bold">Pencarian Pintar & Navigasi Cepat</p>
                <p class="text-slate-400 text-[11px] mt-0.5">Tekan <kbd class="px-1.5 py-0.5 bg-indigo-600 text-white font-mono text-[10px] rounded font-bold">Ctrl + K</kbd> kapan saja untuk mencari produk & menu.</p>
            </div>
            <button @click="showHint = false; localStorage.setItem('hide-ctrlk-hint', 'true')" class="ml-2 text-slate-400 hover:text-white transition p-1 rounded-lg">
                <x-heroicon-o-x-mark class="w-4 h-4" />
            </button>
        </div>

        <livewire:command-palette />
        @stack('scripts')
    </body>
</html>