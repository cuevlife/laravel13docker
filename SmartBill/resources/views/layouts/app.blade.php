<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SmartBill') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
        
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: { sans: ['Plus Jakarta Sans', 'Noto Sans Thai', 'sans-serif'] },
                        colors: { 
                            discord: { green: '#23a559', red: '#ed4245', black: '#1e1f22', darkbg: '#313338' },
                            brand: { primary: '#23a559', secondary: '#ed4245' } 
                        }
                    }
                }
            }
        </script>

        <style type="text/tailwindcss">
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', 'Noto Sans Thai', 'sans-serif'; @apply bg-[#fafafa] text-[#1e1f22] dark:bg-[#1e1f22] dark:text-[#f2f3f5] transition-colors duration-300 tracking-tight; }
            .sidebar-rail { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
            .nav-wrapper { @apply relative flex items-center justify-center w-full py-1; }
            .nav-indicator { @apply absolute left-0 w-1 bg-discord-green rounded-r-full transition-all duration-300 scale-y-0 opacity-0; height: 20px; }
            .nav-wrapper.active .nav-indicator { @apply scale-y-100 opacity-100; height: 32px; }
            .nav-btn { @apply flex items-center transition-all duration-200 text-slate-400 bg-transparent h-12 w-12 justify-center rounded-[14px]; }
            .nav-btn:hover { @apply bg-black/5 dark:bg-white/5 text-discord-green; }
            .nav-btn.active { @apply bg-discord-green text-white shadow-lg shadow-green-500/30 !important; }
            .premium-card { @apply bg-white dark:bg-[#2b2d31] border border-black/5 dark:border-white/5 shadow-sm hover:shadow-xl hover:border-discord-green/20 transition-all duration-300 rounded-[2.5rem]; }
            .modal-open-blur { filter: blur(12px); transition: filter 0.3s ease; }
        </style>

        <!-- Prevent Dark Mode Flicker -->
        <script>
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body x-data="{ modalActive: false, profileOpen: false }" :class="{'overflow-hidden': modalActive}" class="bg-[#fafafa] dark:bg-[#1e1f22] font-sans tracking-tight">
        <div class="flex h-[100dvh] overflow-hidden">
            <!-- Sidebar -->
            <aside class="hidden lg:flex flex-col w-20 bg-[#fafafa] dark:bg-[#1e1f22] border-r border-[#e3e5e8]/50 dark:border-[#313338]/50 z-30 transition-colors" :class="{'modal-open-blur': modalActive}">
                @include('layouts.parts.sidebar-desktop')
            </aside>

            <div class="flex-1 flex flex-col min-w-0 relative">
                <!-- Header -->
                @include('layouts.parts.header-desktop')

                <!-- Mobile Header -->
                <div :class="{'modal-open-blur': modalActive}">
                    @include('layouts.parts.header-mobile')
                </div>

                <!-- Main Content (Unified Spacing) -->
                <main class="flex-1 overflow-y-auto p-6 lg:p-10" :class="{'modal-open-blur': modalActive}">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Mobile Nav -->
                <div :class="{'modal-open-blur': modalActive}">
                    @include('layouts.parts.navigation-mobile')
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('load', () => { lucide.createIcons(); });
            document.addEventListener('alpine:initialized', () => { lucide.createIcons(); });
        </script>
        @stack('scripts')
    </body>
</html>
