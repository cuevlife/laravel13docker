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
                this.$nextTick(() => { lucide.createIcons(); });
            },
            toggleSidebar() {
                if (window.innerWidth < 1024) {
                    this.sidebarOpen = !this.sidebarOpen;
                } else {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                }
                this.$nextTick(() => { lucide.createIcons(); });
            }
          }">
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- Sidebar Overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-[60] lg:hidden" x-cloak></div>

            <!-- Sidebar -->
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
                        <button @click="toggleSidebar()" class="text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>
                        <!-- Branding Text Only -->
                        <span class="font-black text-sm uppercase tracking-tighter italic dark:text-white">Smart<span class="text-discord-red">Bill</span></span>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center bg-slate-100 dark:bg-black/20 p-1 rounded-md">
                            <a href="{{ route('lang.switch', 'th') }}" class="px-2 py-1 rounded text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400' }}">TH</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 rounded text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400' }}">EN</a>
                        </div>

                        <button @click="toggleDarkMode()" class="text-slate-500 hover:text-amber-500 transition-colors">
                            <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                        </button>

                        <div class="h-4 w-px bg-slate-200 dark:bg-white/5"></div>

                        <!-- User Profile -->
                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-md bg-discord-red flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:block text-xs font-bold dark:text-white italic">{{ Auth::user()->name }}</span>
                            </button>
                            <div x-show="userDropdown" x-cloak class="absolute right-0 mt-3 w-48 bg-white dark:bg-discord-darker rounded-lg shadow-2xl border border-slate-200 dark:border-white/5 py-1 overflow-hidden">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 italic">{{ __('Settings') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-discord-red hover:bg-rose-50 dark:hover:bg-rose-500/10 italic">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar relative">
                    <div class="max-w-6xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Floating Scan Action -->
                <a href="{{ route('admin.slip-reader') }}" 
                   class="lg:hidden fixed bottom-8 right-6 w-14 h-14 bg-discord-green text-white rounded-full flex items-center justify-center shadow-2xl z-[55] active:scale-90 transition-transform">
                    <i data-lucide="scan" class="w-6 h-6 stroke-[2.5px]"></i>
                </a>
            </div>
        </div>

        <script>
            lucide.createIcons();
            document.addEventListener('alpine:initialized', () => { lucide.createIcons(); });
        </script>
    </body>
</html>
