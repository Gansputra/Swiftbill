<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Swiftbill') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Cloudflare Turnstile -->
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        
        <style>
            :root {
                --bg-dark: #121212;
                --bg-darker: #0a0a0a;
                --accent-primary: #8b5cf6;
                --accent-secondary: #3b82f6;
                --text-main: #ffffff;
                --text-muted: #94a3b8;
                --input-bg: #1a1a1a;
                --input-border: #333333;
                --glow-color: rgba(139, 92, 246, 0.4);
                --font-main: 'Outfit', sans-serif;
            }

            body {
                font-family: var(--font-main);
                background-color: var(--bg-dark);
                overflow-x: hidden;
            }
        </style>
    </head>
    <body class="antialiased">
        {{ $slot }}

        <!-- Re-initialize Cloudflare Turnstile on Livewire SPA navigation -->
        <script>
            document.addEventListener('livewire:navigated', () => {
                if (typeof turnstile !== 'undefined') {
                    // Cari semua elemen cf-turnstile di halaman baru yang belum ter-render
                    const elements = document.querySelectorAll('.cf-turnstile');
                    elements.forEach((el) => {
                        // Bersihkan konten di dalam element untuk menghindari double render jika ada cache
                        el.innerHTML = '';
                        // Render ulang widget turnstile secara manual
                        turnstile.render(el, {
                            sitekey: el.getAttribute('data-sitekey'),
                            theme: el.getAttribute('data-theme') || 'dark'
                        });
                    });
                }
            });
        </script>
    </body>
</html>
