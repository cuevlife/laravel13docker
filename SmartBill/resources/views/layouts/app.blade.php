<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        sidebarOpen: false,
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
        },
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
      }" 
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartBill AI') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        
        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Kanit', sans-serif !important; 
                transition: background-color 0.5s ease;
            }
            .dark body { background-color: #020617; }
            
            /* Custom Transitions */
            .sidebar-transition { transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
            .content-transition { transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
            
            /* High-Tech Glass */
            .glass-header {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }
            .dark .glass-header {
                background: rgba(15, 23, 42, 0.8);
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }
        </style>
    </head>
    <body class="antialiased text-slate-900 dark:text-slate-100 overflow-x-hidden">
        
        <div class="flex h-screen overflow-hidden bg-slate-50 dark:bg-slate-950">
            
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 bg-slate-900/60 z-[60] md:hidden" x-cloak></div>

            <!-- Sidebar -->
            <aside :class="{
                        'translate-x-0': sidebarOpen, 
                        '-translate-x-full': !sidebarOpen,
                        'w-72': !sidebarCollapsed,
                        'w-20': sidebarCollapsed
                   }" 
                   class="fixed inset-y-0 left-0 sidebar-transition transform transition-transform duration-300 ease-in-out z-[70] md:translate-x-0 md:static md:inset-0 shadow-2xl bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-white/5">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                
                <!-- High-Tech Topbar -->
                <header class="h-20 glass-header sticky top-0 z-50 flex items-center justify-between px-6 md:px-10 flex-shrink-0 transition-all duration-500">
                    <div class="flex items-center space-x-4">
                        <!-- Sidebar Toggle (Desktop) -->
                        <button @click="toggleSidebar()" class="hidden md:flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all">
                            <i class="fas" :class="sidebarCollapsed ? 'fa-indent' : 'fa-outdent'"></i>
                        </button>

                        <!-- Hamburger (Mobile) -->
                        <button @click="sidebarOpen = true" class="md:hidden h-10 w-10 flex items-center justify-center text-slate-600 dark:text-slate-400">
                            <i class="fas fa-bars-staggered text-xl"></i>
                        </button>
                        
                        <div class="hidden sm:block">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 md:space-x-6">
                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode()" 
                                class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-amber-400 hover:scale-110 transition-all duration-500">
                            <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                        </button>

                        <!-- Logout Connection -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="group h-10 px-4 md:px-6 flex items-center justify-center rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-[10px] uppercase tracking-widest hover:bg-red-600 dark:hover:bg-red-500 dark:hover:text-white transition-all duration-300 shadow-lg shadow-slate-900/10">
                                <span class="hidden md:inline mr-2">Terminate Link</span>
                                <i class="fas fa-power-off"></i>
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto overflow-x-hidden p-6 md:p-10 custom-scrollbar relative">
                    <!-- Hero Decoration (Floating Orbs) -->
                    <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
