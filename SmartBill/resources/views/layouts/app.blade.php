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

        <title>{{ config('app.name', 'SmartBill') }}</title>

        <!-- Fonts & Lucide Icons (Minimalist Choice) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
        <script src="https://unpkg.com/lucide@latest"></script>
        
        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Kanit', sans-serif !important; 
                transition: background-color 0.3s ease, color 0.3s ease;
            }
            /* Custom Scrollbar for Minimalist look */
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; }
            
            .sidebar-transition { transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        </style>
    </head>
    <body class="antialiased text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-[#020617]">
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[60] md:hidden" x-cloak></div>

            <!-- Sidebar -->
            <aside :class="{
                        'translate-x-0': sidebarOpen, 
                        '-translate-x-full': !sidebarOpen,
                        'w-64': !sidebarCollapsed,
                        'w-20': sidebarCollapsed
                   }" 
                   class="fixed inset-y-0 left-0 sidebar-transition transform transition-transform duration-300 ease-in-out z-[70] md:translate-x-0 md:static md:inset-0 bg-white dark:bg-[#0b0f1a] border-r border-slate-200/60 dark:border-white/5 shadow-sm">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                
                <!-- Minimalist Topbar -->
                <header class="h-16 bg-white/80 dark:bg-[#0b0f1a]/80 backdrop-blur-md sticky top-0 z-50 flex items-center justify-between px-6 md:px-8 border-b border-slate-200/60 dark:border-white/5">
                    <div class="flex items-center space-x-4">
                        <button @click="toggleSidebar()" class="hidden md:flex text-slate-400 hover:text-indigo-500 transition-colors">
                            <i x-show="!sidebarCollapsed" data-lucide="menu-fold" class="w-5 h-5"></i>
                            <i x-show="sidebarCollapsed" data-lucide="menu-unfold" class="w-5 h-5"></i>
                        </button>

                        <button @click="sidebarOpen = true" class="md:hidden text-slate-400">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        
                        @isset($header)
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-200">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode()" 
                                class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                            <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                        </button>

                        <div class="h-4 w-px bg-slate-200 dark:bg-white/5 mx-2"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-2 p-2 px-3 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all text-xs font-medium">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span class="hidden md:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
                    <div class="max-w-6xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <script>
            // Initialize Lucide Icons
            lucide.createIcons();
            
            // Re-initialize on Alpine.js updates if needed
            document.addEventListener('alpine:initialized', () => {
                lucide.createIcons();
            });
        </script>
        @stack('scripts')
    </body>
</html>
