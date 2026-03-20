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

        <title>SmartBill Premium</title>

        <!-- Premium Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&family=Inter:wght@100..900&display=swap" rel="stylesheet">
        
        <!-- Zero-Build Stack -->
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
                        },
                        animation: {
                            'pulse-slow': 'pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        }
                    }
                }
            }
        </script>

        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Plus Jakarta Sans', 'sans-serif'; 
                transition: background-color 0.5s ease, color 0.5s ease;
            }
            
            /* อลังการเอฟเฟกต์: Glassmorphism & Glow */
            .glass {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            .light .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar { width: 5px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.2); border-radius: 10px; }
            
            .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        </style>
    </head>
    <body class="antialiased bg-slate-50 dark:bg-discord-black text-slate-600 dark:text-slate-400 overflow-hidden">
        
        <!-- Background Effects (ความอลังการแบบมินิมอล) -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
            <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-emerald-500/5 dark:bg-emerald-500/10 rounded-full blur-[120px] animate-pulse-slow"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-rose-500/5 dark:bg-rose-500/10 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: 2s;"></div>
        </div>

        <div class="flex h-screen overflow-hidden">
            
            <!-- Sidebar -->
            <aside :class="{ 'w-64': !sidebarCollapsed, 'w-20': sidebarCollapsed }" 
                   class="sidebar-transition z-[70] hidden md:flex flex-col bg-white dark:bg-discord-darker border-r border-slate-200 dark:border-white/5 shadow-xl">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                
                <!-- Topbar -->
                <header class="h-16 bg-white/80 dark:bg-discord-black/80 backdrop-blur-md flex items-center justify-between px-6 border-b border-slate-200 dark:border-white/5 z-50">
                    <div class="flex items-center space-x-4">
                        <button @click="toggleSidebar()" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 text-slate-400 transition-all">
                            <i x-show="!sidebarCollapsed" data-lucide="align-left"></i>
                            <i x-show="sidebarCollapsed" data-lucide="align-justify"></i>
                        </button>
                        <div class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em]">
                            @isset($header) {{ $header }} @endisset
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Theme Toggle (ความอลังการตอนกด) -->
                        <button @click="toggleDarkMode()" 
                                class="p-2.5 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-amber-400 hover:scale-110 active:scale-90 transition-all duration-500">
                            <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                            <i x-show="darkMode" data-lucide="sun" class="w-5 h-5"></i>
                        </button>

                        <div class="h-6 w-px bg-slate-200 dark:bg-white/5 mx-2"></div>

                        <!-- User Profile -->
                        <div class="relative" @click.away="userDropdown = false">
                            <button @click="userDropdown = !userDropdown" class="flex items-center space-x-3 group">
                                <div class="w-9 h-9 rounded-2xl bg-rose-500 flex items-center justify-center text-white text-xs font-black shadow-lg shadow-rose-500/20 group-hover:rotate-6 transition-all">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-xs font-black text-slate-700 dark:text-slate-200 leading-none">{{ Auth::user()->name }}</p>
                                    <p class="text-[9px] text-slate-400 uppercase font-bold mt-1 tracking-tighter italic">Admin Node</p>
                                </div>
                            </button>

                            <div x-show="userDropdown" x-cloak 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 class="absolute right-0 mt-3 w-56 bg-white dark:bg-discord-darker rounded-2xl shadow-2xl border border-slate-200 dark:border-white/5 py-2 z-50 overflow-hidden">
                                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors uppercase tracking-widest">
                                    <i data-lucide="settings" class="w-4 h-4 text-indigo-500"></i>
                                    <span>Settings</span>
                                </a>
                                <div class="h-px bg-slate-100 dark:bg-white/5 my-1 mx-2"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-xs font-bold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors uppercase tracking-widest">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        <span>Terminate Link</span>
                                    </button>
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
            </div>
        </div>

        <script>
            lucide.createIcons();
            document.addEventListener('alpine:initialized', () => { lucide.createIcons(); });
        </script>
    </body>
</html>
