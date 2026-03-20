<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
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
      }"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill Premium</title>

        <script>
            if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
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
                            discord: { dark: '#313338', darker: '#1e1f22', black: '#0f172a', green: '#23a55a', red: '#f23f43' }
                        }
                    }
                }
            }
        </script>

        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Plus Jakarta Sans', 'sans-serif'; 
                transition: background-color 0.5s ease;
                letter-spacing: -0.02em;
            }
            
            /* Fluid Spacing & Sizes */
            :root {
                --fluid-padding: clamp(1rem, 5vw, 3rem);
            }

            /* Keyframes: Neural Shimmer */
            @keyframes neuralShimmer {
                0% { background-position: -200% center; }
                100% { background-position: 200% center; }
            }
            .animate-text-neural {
                background: linear-gradient(90deg, #f23f43, #fb7185, #ffffff, #fb7185, #f23f43);
                background-size: 200% auto;
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: neuralShimmer 6s linear infinite;
            }
            .animate-text-green {
                background: linear-gradient(90deg, #23a55a, #4ade80, #ffffff, #4ade80, #23a55a);
                background-size: 200% auto;
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: neuralShimmer 6s linear infinite;
            }

            /* Automatic Icon Actions */
            .icon-spin-slow { animation: spin 8s linear infinite; }
            .icon-pulse-slow { animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
            .icon-float { 
                animation: float 3s ease-in-out infinite; 
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }

            /* Staggered Content Loading */
            .stagger-in > * {
                opacity: 0;
                transform: translateY(10px);
                animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            .stagger-in > *:nth-child(1) { animation-delay: 0.1s; }
            .stagger-in > *:nth-child(2) { animation-delay: 0.2s; }
            .stagger-in > *:nth-child(3) { animation-delay: 0.3s; }
            
            @keyframes fadeIn {
                to { opacity: 1; transform: translateY(0); }
            }

            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.2); border-radius: 10px; }
        </style>
    </head>
    <body class="antialiased bg-slate-50 dark:bg-discord-black text-slate-800 dark:text-slate-300">
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- Sidebar (Responsive Auto-Hide) -->
            <aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" 
                   class="hidden lg:flex flex-col bg-white dark:bg-discord-darker border-r border-slate-200 dark:border-white/5 transition-all duration-500 z-50">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Workspace -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                
                <!-- Top Header -->
                <header class="h-16 shrink-0 bg-white/80 dark:bg-discord-black/80 backdrop-blur-md flex items-center justify-between px-6 border-b border-slate-200 dark:border-white/5 z-40">
                    <div class="flex items-center space-x-4">
                        <button @click="toggleSidebar()" class="hidden lg:block text-slate-400 hover:text-indigo-500 transition-all">
                            <i x-show="!sidebarCollapsed" data-lucide="chevron-left" class="w-5 h-5"></i>
                            <i x-show="sidebarCollapsed" data-lucide="chevron-right" class="w-5 h-5"></i>
                        </button>
                        <div class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em]">System Link Live</div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Language -->
                        <div class="flex items-center bg-slate-100 dark:bg-white/5 p-1 rounded-xl">
                            <a href="{{ route('lang.switch', 'th') }}" class="px-2.5 py-1 rounded-lg text-[9px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400' }}">TH</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 rounded-lg text-[9px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400' }}">EN</a>
                        </div>

                        <button @click="toggleDarkMode()" class="text-slate-400 hover:text-amber-400 transition-all active:rotate-90">
                            <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-4 h-4"></i>
                        </button>

                        <div class="h-4 w-px bg-slate-200 dark:bg-white/5"></div>

                        <!-- User -->
                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl bg-discord-red flex items-center justify-center text-white text-[10px] font-black shadow-lg icon-pulse-slow">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block text-xs font-black dark:text-white italic">{{ Auth::user()->name }}</span>
                            </button>
                            <div x-show="userDropdown" x-cloak class="absolute right-0 mt-3 w-48 bg-white dark:bg-discord-darker rounded-2xl shadow-2xl border border-slate-200 dark:border-white/5 py-2">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-indigo-500">{{ __('Settings') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-[10px] font-black uppercase tracking-widest text-discord-red hover:bg-rose-50 dark:hover:bg-rose-500/10">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-[var(--fluid-padding)] custom-scrollbar">
                    <div class="max-w-[1600px] mx-auto stagger-in">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Bottom Navigation (Mobile Only) -->
                <nav class="lg:hidden h-16 shrink-0 bg-white dark:bg-discord-darker border-t border-slate-200 dark:border-white/5 flex items-center justify-around px-6 pb-safe">
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center">
                        <i data-lucide="layout-grid" class="w-5 h-5 mb-1 {{ request()->routeIs('dashboard') ? 'text-discord-green' : 'text-slate-400' }}"></i>
                        <span class="text-[8px] font-black uppercase tracking-tighter">{{ __('Dashboard') }}</span>
                    </a>
                    <a href="{{ route('admin.slip-reader') }}" class="flex flex-col items-center -mt-8">
                        <div class="w-14 h-14 rounded-full bg-discord-green flex items-center justify-center text-white shadow-xl icon-float">
                            <i data-lucide="scan" class="w-6 h-6"></i>
                        </div>
                    </a>
                    <a href="{{ route('admin.merchants') }}" class="flex flex-col items-center">
                        <i data-lucide="store" class="w-5 h-5 mb-1 {{ request()->routeIs('admin.merchants') ? 'text-discord-green' : 'text-slate-400' }}"></i>
                        <span class="text-[8px] font-black uppercase tracking-tighter">{{ __('Merchants') }}</span>
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
