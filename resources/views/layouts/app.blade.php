<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ 
          darkMode: localStorage.getItem('dark-mode') === 'true',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('dark-mode', this.darkMode);
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
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
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <style>
            body { font-family: 'Outfit', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4f46e5">
        <link rel="apple-touch-icon" href="{{ asset('icon-192x192.png') }}">
    </head>
    {{-- Update: Tambah transition-colors di body --}}
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

        <div class="flex h-screen overflow-hidden">
            {{-- SIDEBAR --}}
            <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 shadow-sm z-50 transition-colors duration-300">
                <div class="p-6 flex items-center space-x-3">
                    <div class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">S</div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">SwiftBill</span>
                </div>
                
                <nav class="flex-grow px-4 pb-4 space-y-1">
                    <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate class="flex items-center gap-2">
                        <x-heroicon-o-home class="w-5 h-5"/> Dashboard
                    </x-nav-link-sidebar>
                    
                    <x-nav-link-sidebar :href="route('pos.index')" :active="request()->routeIs('pos.index')" wire:navigate class="flex items-center gap-2">
                        <x-heroicon-o-shopping-cart class="w-5 h-5"/> Point of Sale
                    </x-nav-link-sidebar>

                    @if(auth()->user()->role === 'admin')
                        <div class="pt-4 pb-2 px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Inventory</div>
                        <x-nav-link-sidebar :href="route('categories.index')" :active="request()->routeIs('categories.index')" wire:navigate class="flex items-center gap-2">
                            <x-heroicon-o-tag class="w-5 h-5"/> Categories
                        </x-nav-link-sidebar>
                    @endif
                </nav>

                <div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-3">
                    <button @click="toggleTheme()" 
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-indigo-500 hover:text-indigo-600 transition-all duration-300">
                        <div class="flex items-center">
                            <span class="text-xs font-bold uppercase tracking-wider" x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
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

                    <div class="flex items-center space-x-3 px-3 py-2 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <img class="h-8 w-8 rounded-full object-cover border border-white dark:border-slate-700 shadow-sm" 
                             src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=6366f1&background=EEF2FF&bold=true' }}" 
                             alt="{{ auth()->user()->name }}">
                        
                        <div class="flex-grow min-w-0">
                            <p class="text-sm font-semibold truncate dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">{{ auth()->user()->role }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- MAIN CONTENT AREA --}}
            {{-- Perubahan: Paksa background dark di div pembungkus dan main --}}
            <div class="flex-grow flex flex-col min-w-0 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
                <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 z-40 sticky top-0 transition-colors duration-300">
                    <div class="flex items-center space-x-4">
                        <button class="md:hidden p-2 text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <h1 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                            {{ $header ?? '' }}
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:block relative">
                            <input type="text" placeholder="Search data..." class="h-9 w-64 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 placeholder-slate-400 dark:placeholder-slate-500 transition-colors duration-300">
                        </div>
                        
                        <livewire:layout.navigation />
                    </div>
                </header>

                {{-- Update: Tambahkan class dark:bg-slate-950 di sini --}}
                <main class="flex-grow overflow-y-auto p-8 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>