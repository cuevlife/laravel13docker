<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        sidebarOpen: false,
        userDropdown: false,
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

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
        <script src="https://unpkg.com/lucide@latest"></script>
        
        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Kanit', sans-serif !important; }
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; }
            .sidebar-transition { transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        </style>
    </head>
    <body class="antialiased bg-slate-50 dark:bg-[#020617] text-slate-600 dark:text-slate-400">
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[60] md:hidden" x-cloak></div>

            <!-- Sidebar -->
            <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'w-64': !sidebarCollapsed, 'w-20': sidebarCollapsed }" 
                   class="fixed inset-y-0 left-0 sidebar-transition transform transition-transform duration-300 ease-in-out z-[70] md:translate-x-0 md:static md:inset-0 bg-white dark:bg-[#0b0f1a] border-r border-slate-200/60 dark:border-white/5">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                
                <!-- Topbar -->
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
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-200 uppercase tracking-widest">{{ $header }}</div>
                        @endisset
                    </div>

                    <div class="flex items-center space-x-4">
                        <button @click="toggleDarkMode()" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                            <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                        </button>

                        <!-- User Dropdown -->
                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="flex items-center space-x-3 p-1.5 pr-3 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white text-xs font-black shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200 leading-none">{{ Auth::user()->name }}</p>
                                    <p class="text-[9px] text-slate-400 uppercase font-medium mt-1 tracking-tighter">Administrator</p>
                                </div>
                                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform" :class="userDropdown ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="userDropdown" x-cloak 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-[#161b2a] rounded-2xl shadow-2xl border border-slate-200/60 dark:border-white/5 py-2 z-50">
                                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-3 text-xs font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                    <i data-lucide="settings" class="w-4 h-4"></i>
                                    <span>Settings</span>
                                </a>
                                <div class="h-px bg-slate-100 dark:bg-white/5 my-1 mx-2"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-xs font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        <span>Establish Termination</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
                    <div class="max-w-6xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <script>
            lucide.createIcons();
            document.addEventListener('alpine:initialized', () => { lucide.createIcons(); });
        </script>
        @stack('scripts')
    </body>
</html>
