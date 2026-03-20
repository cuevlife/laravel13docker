<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill</title>

        <!-- Ultra-Stable Theme Logic -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&family=Inter:wght@100..900&display=swap" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: { sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'] },
                        colors: {
                            discord: {
                                dark: '#313338',
                                darker: '#1e1f22',
                                black: '#0f172a',
                                green: '#23a55a',
                                red: '#f23f43'
                            }
                        }
                    }
                }
            }
        </script>

        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Plus Jakarta Sans', 'sans-serif'; 
                transition: background-color 0.2s ease;
                letter-spacing: -0.02em;
            }
            .dark body { background-color: #0f172a; color: #f2f3f5; }
            body { background-color: #f2f3f5; color: #2e3338; }

            /* Simple Entrance */
            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
            .fade-in { animation: fadeIn 0.3s ease-in-out forwards; }

            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #4e5058; border-radius: 10px; }
        </style>
    </head>
    <body x-data="{ 
            darkMode: localStorage.getItem('darkMode') === 'true',
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            userDropdown: false,
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('darkMode', this.darkMode);
                if (this.darkMode) document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
            },
            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
            }
          }">
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- Sidebar -->
            <aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" 
                   class="hidden lg:flex flex-col bg-[#f2f3f5] dark:bg-discord-darker border-r border-slate-200 dark:border-white/5 transition-all duration-200 z-50">
                @include('layouts.sidebar')
            </aside>

            <!-- Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                
                <!-- Topbar -->
                <header class="h-14 shrink-0 bg-white dark:bg-discord-black border-b border-slate-200 dark:border-white/5 flex items-center justify-between px-6 z-40">
                    <div class="flex items-center space-x-4">
                        <button @click="toggleSidebar()" class="hidden lg:block text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">
                            <i x-show="!sidebarCollapsed" data-lucide="panel-left-close" class="w-5 h-5"></i>
                            <i x-show="sidebarCollapsed" data-lucide="panel-left-open" class="w-5 h-5"></i>
                        </button>
                        <div class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">SmartBill Node</div>
                    </div>

                    <div class="flex items-center space-x-4 md:space-x-6">
                        <!-- Language Switcher -->
                        <div class="flex items-center bg-slate-100 dark:bg-black/20 p-1 rounded-md">
                            <a href="{{ route('lang.switch', 'th') }}" class="px-2 py-1 rounded text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400' }}">TH</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 rounded text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400' }}">EN</a>
                        </div>

                        <button @click="toggleDarkMode()" class="text-slate-500 hover:text-amber-500 transition-colors">
                            <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-4 h-4"></i>
                        </button>

                        <div class="h-4 w-px bg-slate-200 dark:bg-white/5"></div>

                        <!-- Profile -->
                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-md bg-discord-red flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block text-xs font-bold dark:text-white">{{ Auth::user()->name }}</span>
                            </button>
                            <div x-show="userDropdown" x-cloak class="absolute right-0 mt-3 w-48 bg-white dark:bg-discord-darker rounded-lg shadow-2xl border border-slate-200 dark:border-white/5 py-1 overflow-hidden">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">{{ __('Settings') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-discord-red hover:bg-rose-50 dark:hover:bg-rose-500/10">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar fade-in">
                    <div class="max-w-6xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Bottom Nav (Mobile) -->
                <nav class="lg:hidden h-14 bg-white dark:bg-discord-darker border-t border-slate-200 dark:border-white/5 flex items-center justify-around px-6 pb-safe">
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center">
                        <i data-lucide="layout-grid" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-discord-green' : 'text-slate-400' }}"></i>
                    </a>
                    <a href="{{ route('admin.slip-reader') }}" class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-discord-green flex items-center justify-center text-white shadow-lg">
                            <i data-lucide="scan" class="w-5 h-5"></i>
                        </div>
                    </a>
                    <a href="{{ route('admin.merchants') }}" class="flex flex-col items-center">
                        <i data-lucide="store" class="w-5 h-5 {{ request()->routeIs('admin.merchants') ? 'text-discord-green' : 'text-slate-400' }}"></i>
                    </a>
                </nav>
            </div>
        </div>

        <script>
            lucide.createIcons();
            document.addEventListener('alpine:initialized', () => { lucide.createIcons(); });
        </script>
    </body>
</html>
