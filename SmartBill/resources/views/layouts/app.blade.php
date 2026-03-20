<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        sidebarOpen: false,
        userDropdown: false,
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
      }" 
      class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill Admin (No-Node)</title>

        <!-- Premium Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">
        
        <!-- Tailwind Play CDN (No Build Step Required) -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Lucide Icons & Alpine.js CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <script>
            // Tailwind Configuration
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        },
                        letterSpacing: {
                            tightest: '-.05em',
                        }
                    }
                }
            }
        </script>

        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Plus Jakarta Sans', 'Inter', sans-serif !important; 
                background-color: #0f172a;
                color: #cbd5e1;
                letter-spacing: -0.02em;
            }
            .discord-sidebar { background-color: #1e293b; }
            .discord-card { background-color: #1e293b; }
            .discord-topbar { background-color: #0f172a; border-bottom: 1px solid rgba(255,255,255,0.05); }
            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        </style>
    </head>
    <body class="antialiased overflow-hidden">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside :class="{ 'w-64': !sidebarCollapsed, 'w-20': sidebarCollapsed }" 
                   class="discord-sidebar transition-all duration-300 ease-in-out z-[70] hidden md:flex flex-col border-r border-white/5">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                <header class="h-14 discord-topbar flex items-center justify-between px-6">
                    <div class="flex items-center space-x-4">
                        <button @click="toggleSidebar()" class="text-slate-400 hover:text-white transition-colors">
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>
                        <div class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">
                            @isset($header) {{ $header }} @endisset
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Language Switcher -->
                        <div class="flex items-center bg-black/20 rounded-lg p-1">
                            <a href="{{ route('lang.switch', 'th') }}" class="px-2 py-1 rounded text-[10px] font-black uppercase transition-all {{ app()->getLocale() == 'th' ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-300' }}">TH</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 rounded text-[10px] font-black uppercase transition-all {{ app()->getLocale() == 'en' ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-300' }}">EN</a>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="flex items-center space-x-2 group">
                                <div class="w-7 h-7 rounded-lg bg-rose-500 flex items-center justify-center text-white text-[10px] font-black">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="text-xs font-bold text-slate-300 group-hover:text-white transition-colors">{{ Auth::user()->name }}</span>
                                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-500 transition-transform" :class="userDropdown ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="userDropdown" x-cloak 
                                 class="absolute right-0 mt-2 w-48 bg-[#18191c] rounded-md shadow-2xl border border-white/5 py-1 z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-slate-300 hover:bg-emerald-600 hover:text-white mx-1 rounded transition-colors uppercase font-bold tracking-widest">{{ __('Settings') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-400 hover:bg-rose-500 hover:text-white mx-1 rounded transition-colors uppercase font-bold tracking-widest">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            lucide.createIcons();
            // Re-initialize icons when sidebar collapses
            window.addEventListener('resize', () => lucide.createIcons());
        </script>
    </body>
</html>
