<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ScrapeToko') }}</title>

        <script>
            (function () {
                const theme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'dark' || (!theme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink-900 antialiased dark:text-ink-100">
        <div class="min-h-screen flex relative overflow-hidden bg-white dark:bg-ink-950">
            <!-- Gradient blobs dekoratif, membentang di seluruh halaman -->
            <div class="pointer-events-none absolute -top-28 -right-28 h-96 w-96 rounded-full bg-brand-200/15 dark:bg-brand-900/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-28 h-96 w-96 rounded-full bg-brand-200/15 dark:bg-brand-900/10 blur-3xl"></div>

            <!-- Panel ilustrasi (kiri) -->
            <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-16 relative overflow-hidden bg-gradient-to-br from-brand-100/40 via-brand-50/70 to-purple-100/30 dark:from-brand-950/40 dark:to-ink-950">
                <!-- Inner blur blobs -->
                <div class="pointer-events-none absolute -top-12 -right-12 h-80 w-80 rounded-full bg-brand-200/30 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-12 -left-12 h-80 w-80 rounded-full bg-purple-200/30 blur-3xl"></div>
                
                <img src="{{ asset('images/illustrations/login_light.png') }}" alt="Ilustrasi"
                     class="max-w-md w-full h-auto object-contain select-none pointer-events-none relative z-10 dark:hidden block">
                <img src="{{ asset('images/illustrations/login_dark.png') }}" alt="Ilustrasi"
                     class="max-w-md w-full h-auto object-contain select-none pointer-events-none relative z-10 hidden dark:block">
            </div>

            <!-- Panel form (kanan) -->
            <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 relative z-10">
                <div class="mb-8">
                    <a href="/" class="text-3xl font-extrabold text-ink-900 dark:text-white flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 shadow-soft">
                            <span class="icon icon-md text-white">shopping_cart</span>
                        </span>
                        ScrapeToko
                    </a>
                </div>

                <div class="w-full sm:max-w-md px-6 py-8 sm:px-8 sm:py-10 bg-white dark:bg-ink-900 shadow-card overflow-hidden rounded-2xl border border-ink-100 dark:border-ink-800">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
