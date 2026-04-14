<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>body { font-family: 'Outfit', sans-serif; }</style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dark Mode Init -->
        <script>
            // We use PHP to check the DB value on page load
            var userPrefersDark = {{ auth()->check() && auth()->user()->dark_mode ? 'true' : 'false' }};
            if (userPrefersDark || (!{{ auth()->check() ? 'true' : 'false' }} && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- PWA Meta & Script -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4f46e5">
        <link rel="apple-touch-icon" href="{{ asset('icon-192x192.png') }}">
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar (Desktop) -->
            <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 shadow-sm z-50">
                <div class="p-6 flex items-center space-x-3">
                    <div class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">S</div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">SwiftBill</span>
                </div>
                
                <nav class="flex-grow px-4 pb-4 space-y-1">
                    <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link-sidebar>
                    <x-nav-link-sidebar :href="route('pos.index')" :active="request()->routeIs('pos.index')">
                        Point of Sale
                    </x-nav-link-sidebar>

                    @if(auth()->user()->role === 'admin')
                    <div class="pt-4 pb-2 px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Inventory</div>
                    <x-nav-link-sidebar :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                        Categories
                    </x-nav-link-sidebar>
                    <x-nav-link-sidebar :href="route('suppliers.index')" :active="request()->routeIs('suppliers.index')">
                        Suppliers
                    </x-nav-link-sidebar>
                    <x-nav-link-sidebar :href="route('products.index')" :active="request()->routeIs('products.index')">
                        Products
                    </x-nav-link-sidebar>
                    <x-nav-link-sidebar :href="route('stock-movements.index')" :active="request()->routeIs('stock-movements.index')">
                        Stock Movements
                    </x-nav-link-sidebar>
                    
                    <div class="pt-4 pb-2 px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Analytics</div>
                    <x-nav-link-sidebar :href="route('ai-dashboard')" :active="request()->routeIs('ai-dashboard')">
                        AI Insights
                    </x-nav-link-sidebar>
                    <x-nav-link-sidebar :href="route('reports.sales')" :active="request()->routeIs('reports.sales')">
                        Sales Report
                    </x-nav-link-sidebar>
                    <x-nav-link-sidebar :href="route('reports.shifts')" :active="request()->routeIs('reports.shifts')">
                        Shift Logs
                    </x-nav-link-sidebar>

                    <div class="pt-4 pb-2 px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Administration</div>
                    <x-nav-link-sidebar :href="route('users.index')" :active="request()->routeIs('users.*')">
                        Employees
                    </x-nav-link-sidebar>
                    @endif
                </nav>

                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-3 px-3 py-2 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold uppercase">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div class="flex-grow min-w-0">
                            <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">{{ auth()->user()->role }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-grow flex flex-col min-w-0 bg-slate-50 dark:bg-slate-950">
                <!-- Top Navbar -->
                <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 z-40 sticky top-0">
                    <div class="flex items-center space-x-4">
                        <button class="md:hidden p-2 text-slate-500 hover:text-indigo-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <h1 class="text-sm font-semibold text-slate-500 uppercase tracking-widest">
                            {{ $header ?? '' }}
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:block relative">
                            <input type="text" placeholder="Search data..." class="h-9 w-64 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-xs focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <!-- Dark Mode Toggle Component -->
                        <livewire:theme-toggle />

                        <livewire:layout.navigation />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-grow overflow-y-auto p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
