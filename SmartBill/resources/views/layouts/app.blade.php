<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'smartbill') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2323a559' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z'/><path d='M16 8h-6'/><path d='M16 12H8'/><path d='M13 16H8'/></svg>">

        <!-- Tailwind Play CDN (No Node.js Required) -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Noto Sans Thai', 'Inter', 'sans-serif'],
                        },
                        colors: {
                            'discord-gray': '#313338',
                            'discord-green': '#23a559',
                            'discord-red': '#da373c',
                            'discord-blue': '#5865f2',
                            'discord-main': '#1e1f22',
                        }
                    }
                }
            }
        </script>

        <style>
            @font-face { font-family: 'Inter'; font-style: normal; font-weight: 400; src: local('Inter'), local('Segoe UI'), local('Helvetica Neue'), Arial, sans-serif; }
            body { font-family: 'Noto Sans Thai', 'Inter', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>

        <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
        
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('vendor/flatpickr/flatpickr.js') }}"></script>
        <script src="{{ asset('vendor/flatpickr/th.js') }}"></script>
        <script src="{{ asset('vendor/alpine.min.js') }}" defer></script>

        <script>
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body x-data="{ modalActive: false, profileOpen: false, sidebarOpen: false }" :class="{'overflow-hidden': modalActive || sidebarOpen}" class="bg-[#fafafa] dark:bg-[#1e1f22] font-sans tracking-tight">
        <div class="flex h-screen overflow-hidden relative">
            
            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-40 md:hidden" @click="sidebarOpen = false" x-cloak></div>

            <!-- Sidebar (Drawer on Mobile, Fixed on PC) -->
            @php
                $isAdminMode = request()->is('admin*') || request()->routeIs('admin.*');
                $isTenant = isset($activeTenant);
                $sidebarBgClass = $isAdminMode ? 'bg-rose-50/30' : ($isTenant || request()->routeIs('dashboard') ? 'bg-emerald-50/20' : 'bg-[#fafafa]');
            @endphp
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
                   class="fixed md:relative inset-y-0 left-0 flex flex-col w-20 shrink-0 {{ $sidebarBgClass }} dark:bg-[#1e1f22] border-r border-[#e3e5e8]/50 dark:border-[#313338]/50 z-50 transition-transform duration-300 ease-in-out">
                @include('layouts.parts.sidebar-desktop')
            </aside>

            <div class="flex-1 flex flex-col min-w-0 relative h-full">
                <!-- Top Navbar -->
                @include('layouts.parts.header-desktop')

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto p-2 md:p-4 lg:p-8" :class="{'modal-open-blur': modalActive}">
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

                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
