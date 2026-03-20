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
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        },
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
      }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill</title>

        <!-- Anti-Flicker Script -->
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
                transition: background-color 0.3s ease;
            }
            .dark body { background-color: #0f172a; }
            .custom-scrollbar::-webkit-scrollbar { width: 5px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.2); border-radius: 10px; }
            .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        </style>
    </head>
    <body class="antialiased bg-slate-50 dark:bg-discord-black text-slate-600 dark:text-slate-400 overflow-hidden">
        
        <div class="flex h-screen overflow-hidden">
            <aside :class="{ 'w-64': !sidebarCollapsed, 'w-20': sidebarCollapsed }" 
                   class="sidebar-transition z-[70] hidden md:flex flex-col bg-white dark:bg-discord-darker border-r border-slate-200 dark:border-white/5 shadow-xl">
                @include('layouts.sidebar')
            </aside>

            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <header class="h-16 bg-white/80 dark:bg-discord-black/80 backdrop-blur-md flex items-center justify-between px-6 border-b border-slate-200 dark:border-white/5 z-50">
                    <div class="flex items-center space-x-4">
                        <button @click="toggleSidebar()" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-slate-400 transition-all">
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>
                        <div class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em]">
                            @isset($header) {{ $header }} @endisset
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 md:space-x-6">
                        <div class="flex items-center bg-slate-100 dark:bg-white/5 p-1 rounded-xl">
                            <a href="{{ route('lang.switch', 'th') }}" class="px-2.5 py-1 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-indigo-600 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">TH</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-indigo-600 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
                        </div>

                        <button @click="toggleDarkMode()" class="text-slate-400 hover:text-amber-400 transition-all">
                            <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-5 h-5 text-amber-400"></i>
                        </button>

                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-discord-red flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-xs font-black text-slate-700 dark:text-slate-200 leading-none">{{ Auth::user()->name }}</p>
                                    <p class="text-[9px] text-slate-400 uppercase font-bold mt-1 tracking-tighter">{{ __('Username') }}</p>
                                </div>
                            </button>

                            <div x-show="userDropdown" x-cloak 
                                 class="absolute right-0 mt-3 w-52 bg-white dark:bg-discord-darker rounded-2xl shadow-2xl border border-slate-200 dark:border-white/5 py-2 z-50 overflow-hidden">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors uppercase tracking-widest">{{ __('Settings') }}</a>
                                <div class="h-px bg-slate-100 dark:bg-white/5 my-1 mx-2"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-xs font-bold text-discord-red hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors uppercase tracking-widest">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar relative">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            lucide.createIcons();
            document.addEventListener('alpine:initialized', () => { lucide.createIcons(); });
        </script>
    </body>
</html>
