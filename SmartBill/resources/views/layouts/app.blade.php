<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true',
        userDropdown: false,
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        }
      }"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill</title>

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
                -webkit-tap-highlight-color: transparent;
                transition: background-color 0.3s ease;
            }
            /* Safe area for iPhone 14 Notch/Home Indicator */
            .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
            
            /* App-like Transitions */
            .fade-enter { opacity: 0; transform: translateY(10px); }
            .fade-enter-active { opacity: 1; transform: translateY(0); transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1); }
            
            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.2); border-radius: 10px; }
        </style>
    </head>
    <body class="antialiased bg-[#f2f3f5] dark:bg-[#020617] text-slate-800 dark:text-slate-200">
        
        <div class="flex h-screen overflow-hidden flex-col md:flex-row">
            
            <!-- Sidebar (Desktop Only) -->
            <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-[#1e1f22] border-r border-slate-200 dark:border-white/5 shadow-xl z-50">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Application Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                
                <!-- App Top Bar (Fixed) -->
                <header class="h-14 bg-white/80 dark:bg-[#0f172a]/80 backdrop-blur-md flex items-center justify-between px-6 border-b border-slate-200 dark:border-white/5 z-40 shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-7 h-7 bg-discord-red rounded-lg flex items-center justify-center text-white shadow-lg">
                            <i data-lucide="zap" class="w-4 h-4 fill-current"></i>
                        </div>
                        <span class="font-black text-sm uppercase tracking-tighter italic">Smart<span class="text-discord-red">Bill</span></span>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button @click="toggleDarkMode()" class="text-slate-400 hover:text-amber-400 transition-colors">
                            <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-4 h-4"></i>
                        </button>
                        
                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="w-8 h-8 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center font-bold text-[10px]">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </button>
                            <div x-show="userDropdown" x-cloak class="absolute right-0 mt-3 w-48 bg-white dark:bg-[#1e1f22] rounded-2xl shadow-2xl border border-slate-200 dark:border-white/5 py-2 overflow-hidden">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-bold hover:bg-slate-50 dark:hover:bg-white/5">{{ __('Settings') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-discord-red hover:bg-rose-50 dark:hover:bg-rose-500/10">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Scrollable Content -->
                <main class="flex-1 overflow-y-auto p-4 md:p-10 custom-scrollbar fade-enter-active">
                    <div class="max-w-4xl mx-auto pb-24 md:pb-0">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Mobile Bottom Navigation (iPhone 14 Style) -->
                <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/90 dark:bg-[#1e1f22]/90 backdrop-blur-xl border-t border-slate-200 dark:border-white/5 px-6 pt-3 safe-bottom z-50 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)] dark:shadow-none">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="flex flex-col items-center space-y-1 group">
                            <i data-lucide="layout-grid" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-discord-green stroke-[2.5px]' : 'text-slate-400 group-active:scale-90 transition-transform' }}"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('dashboard') ? 'text-discord-green' : 'text-slate-400' }}">{{ __('Dashboard') }}</span>
                        </a>

                        <!-- Primary Action: Scan -->
                        <a href="{{ route('admin.slip-reader') }}" class="flex flex-col items-center -mt-8">
                            <div class="w-14 h-14 rounded-full bg-discord-green flex items-center justify-center text-white shadow-xl shadow-emerald-900/30 border-4 border-[#f2f3f5] dark:border-[#020617] active:scale-90 transition-transform">
                                <i data-lucide="scan" class="w-6 h-6 stroke-[2.5px]"></i>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-discord-green mt-1">Scan</span>
                        </a>

                        <a href="{{ route('admin.merchants') }}" class="flex flex-col items-center space-y-1 group">
                            <i data-lucide="store" class="w-5 h-5 {{ request()->routeIs('admin.merchants') ? 'text-discord-green stroke-[2.5px]' : 'text-slate-400 group-active:scale-90 transition-transform' }}"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.merchants') ? 'text-discord-green' : 'text-slate-400' }}">{{ __('Merchants') }}</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <script>
            lucide.createIcons();
            document.addEventListener('alpine:initialized', () => { lucide.createIcons(); });
        </script>
    </body>
</html>
