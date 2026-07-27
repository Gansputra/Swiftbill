<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | SwiftBill</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-slate-900 text-slate-100 flex items-center justify-center p-4 sm:p-6 overflow-hidden relative selection:bg-indigo-500 selection:text-white">
    
    <!-- Background Glow Blobs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/30 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-violet-600/25 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-rose-500/15 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="max-w-xl w-full text-center relative z-10">
        <!-- Card Container -->
        <div class="bg-slate-800/60 backdrop-blur-2xl border border-slate-700/60 rounded-[3rem] p-8 sm:p-12 shadow-2xl shadow-slate-950/50">
            
            <!-- Icon / Illustration Badge -->
            <div class="inline-flex items-center justify-center w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 mb-8 shadow-inner relative group">
                <div class="absolute inset-0 bg-indigo-500/20 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <x-heroicon-o-exclamation-triangle class="w-12 h-12 sm:w-14 sm:h-14 relative z-10" />
            </div>

            <!-- Big 404 Code -->
            <span class="block text-xs font-extrabold tracking-widest text-indigo-400 uppercase mb-2">Error 404</span>

            <!-- Main Heading -->
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white mb-4">
                Halaman yang Anda cari tidak tersedia
            </h1>

            <!-- Description -->
            <p class="text-sm sm:text-base text-slate-400 font-medium mb-10 leading-relaxed max-w-md mx-auto">
                Maaf, halaman yang Anda tuju telah dipindahkan, dihapus, atau tidak pernah ada dalam sistem SwiftBill.
            </p>

            <!-- Quick Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('dashboard') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-bold text-xs shadow-lg shadow-indigo-600/30 active:scale-95 transition-all">
                    <x-heroicon-o-home class="w-4 h-4" />
                    Kembali ke Dashboard
                </a>
                
                <a href="{{ route('pos.index') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-700/60 hover:bg-slate-700 text-slate-200 border border-slate-600/50 rounded-2xl font-bold text-xs active:scale-95 transition-all">
                    <x-heroicon-o-shopping-cart class="w-4 h-4" />
                    Kasir (POS)
                </a>
            </div>

        </div>
    </div>
</body>
</html>
