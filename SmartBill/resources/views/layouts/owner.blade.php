<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Control Plane - {{ config('app.name', 'SmartBill') }}</title>

        <!-- Favicon (SaaS Control Icon) -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235865f2' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10'/></svg>">

        <!-- Local Fonts -->
        <style>
            @font-face { font-family: 'Inter'; font-style: normal; font-weight: 400; src: local('Inter'), local('Segoe UI'), local('Helvetica Neue'), Arial, sans-serif; }
            body { font-family: 'Inter', 'Noto Sans Thai', sans-serif; }
        </style>
        
        <!-- Local Assets -->
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/app.css') }}">
        
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('vendor/alpine.min.js') }}" defer></script>
        <script src="{{ asset('vendor/app.js') }}" defer></script>

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
            <!-- Sidebar (Standard Slim Version) -->
            <aside class="flex flex-col w-20 shrink-0 bg-[#fafafa] dark:bg-[#1e1f22] border-r border-[#e3e5e8]/50 dark:border-[#313338]/50 z-30 transition-colors">
                @include('layouts.parts.sidebar-desktop')
            </aside>

            <div class="flex-1 flex flex-col min-w-0 relative">
                <!-- Header -->
                @include('layouts.parts.header-desktop')

                <!-- Main Content (Full Width) -->
                <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8" :class="{'modal-open-blur': modalActive}">
                    <div class="w-full">
                        @if (session('status'))
                            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
