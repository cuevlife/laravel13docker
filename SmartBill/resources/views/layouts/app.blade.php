<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill</title>

        <!-- Zero-Flash Theme Script -->
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

            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #4e5058; border-radius: 10px; }
        </style>
    </head>
    <body x-data="{ 
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
                if (window.innerWidth < 1024) {
                    this.sidebarOpen = !this.sidebarOpen;
                } else {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                }
            }
          }">
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-[60] lg:hidden" x-cloak></div>

            <!-- Sidebar (No Icons) -->
            <aside :class="{ 
                        'w-64': !sidebarCollapsed || sidebarOpen, 
                        'w-20': sidebarCollapsed && !sidebarOpen,
                        'translate-x-0': sidebarOpen,
                        '-translate-x-full': !sidebarOpen && window.innerWidth < 1024,
                        'lg:translate-x-0': true
                   }" 
                   class="fixed inset-y-0 left-0 lg:static lg:flex flex-col bg-[#f2f3f5] dark:bg-discord-darker border-r border-slate-200 dark:border-white/5 transition-all duration-300 z-[70]">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                
                <!-- Topbar -->
                <header class="h-14 shrink-0 bg-white dark:bg-discord-black border-b border-slate-200 dark:border-white/5 flex items-center justify-between px-6 z-40">
                    <div class="flex items-center space-x-4">
                        <button @click="toggleSidebar()" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest hover:text-indigo-500 transition-colors">
                            <span x-show="!sidebarCollapsed || sidebarOpen">Close</span>
                            <span x-show="sidebarCollapsed && !sidebarOpen">Menu</span>
                        </button>
                        <div class="h-4 w-px bg-slate-100 dark:bg-white/5 hidden sm:block"></div>
                        <div class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest hidden sm:block italic">SmartBill Node</div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Language Switcher (Text-Only) -->
                        <div class="flex items-center space-x-3 text-[10px] font-black uppercase tracking-widest">
                            <a href="{{ route('lang.switch', 'th') }}" class="transition-all {{ app()->getLocale() == 'th' ? 'text-discord-green underline underline-offset-4 decoration-2' : 'text-slate-400 hover:text-slate-200' }}">TH</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="transition-all {{ app()->getLocale() == 'en' ? 'text-discord-green underline underline-offset-4 decoration-2' : 'text-slate-400 hover:text-slate-200' }}">EN</a>
                        </div>

                        <!-- Theme Toggle (Text-Only) -->
                        <button @click="toggleDarkMode()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-amber-500 transition-colors">
                            <span x-show="!darkMode">Dark</span>
                            <span x-show="darkMode" class="text-amber-400">Light</span>
                        </button>

                        <div class="h-4 w-px bg-slate-200 dark:bg-white/5"></div>

                        <!-- User Profile -->
                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="flex items-center space-x-2 group">
                                <div class="w-7 h-7 rounded-md bg-discord-red flex items-center justify-center text-white text-[10px] font-black shadow-lg group-hover:rotate-12 transition-transform">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:block text-xs font-bold dark:text-white italic tracking-tight">{{ Auth::user()->name }}</span>
                            </button>
                            <div x-show="userDropdown" x-cloak class="absolute right-0 mt-3 w-48 bg-white dark:bg-discord-darker rounded-lg shadow-2xl border border-slate-200 dark:border-white/5 py-1 overflow-hidden">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-indigo-500">{{ __('Settings') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-[10px] font-black uppercase tracking-widest text-discord-red hover:bg-rose-50 dark:hover:bg-rose-500/10">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Content Area -->
                <main class="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar relative">
                    <div class="max-w-6xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Floating Text Button (Mobile Only) -->
                <a href="{{ route('admin.slip-reader') }}" 
                   class="lg:hidden fixed bottom-8 right-6 px-6 py-4 bg-discord-green text-white rounded-2xl flex items-center justify-center shadow-2xl shadow-emerald-900/40 z-[55] active:scale-95 transition-all">
                    <span class="text-[11px] font-black uppercase tracking-[0.3em]">Scan</span>
                </a>
            </div>
        </div>
    </body>
</html>
