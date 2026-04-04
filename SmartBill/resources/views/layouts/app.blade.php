<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Slips - {{ config('app.name', 'SmartBill') }}</title>

        <!-- Favicon (Receipt Icon) -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2323a559' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z'/><path d='M16 8h-6'/><path d='M16 12H8'/><path d='M13 16H8'/></svg>">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
        
        @livewireStyles
        @livewireScripts
        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                <main class="flex-1 overflow-y-auto p-4 pb-24 md:p-5 md:pb-24 lg:p-8 lg:pb-10" :class="{'modal-open-blur': modalActive}">
                    <div class="max-w-7xl mx-auto">
                        @if (session('status'))
                            <div class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>

                <!-- Mobile Nav -->
                <div :class="{'modal-open-blur': modalActive}">
                    @include('layouts.parts.navigation-mobile')
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
    </body>
</html>
