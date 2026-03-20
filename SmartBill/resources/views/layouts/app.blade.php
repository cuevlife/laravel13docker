<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartBill AI') }}</title>

        <!-- Fonts: Kanit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Icons & UI Libraries -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Kanit', sans-serif !important; 
                background-color: #f8fafc;
            }
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            .sidebar-gradient {
                background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            }
            ::-webkit-scrollbar { width: 5px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        </style>
    </head>
    <body class="antialiased text-slate-900 overflow-x-hidden" x-data="{ sidebarOpen: false }">
        
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
                 class="fixed inset-0 bg-slate-900/60 z-40 md:hidden" x-cloak></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
                   class="fixed inset-y-0 left-0 w-72 sidebar-gradient text-white transform transition-transform duration-300 ease-in-out z-50 md:translate-x-0 md:static md:inset-0 shadow-2xl">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                
                <!-- High-Tech Topbar -->
                <header class="h-20 glass sticky top-0 z-30 flex items-center justify-between px-6 md:px-10 flex-shrink-0">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="md:hidden p-2 -ml-2 text-slate-600 hover:text-indigo-600 focus:outline-none">
                            <i class="fas fa-bars-staggered text-xl"></i>
                        </button>
                        
                        <div class="ml-4 md:ml-0">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- User Dropdown (Simple for now) -->
                        <div class="hidden sm:flex flex-col items-end mr-2">
                            <span class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] text-slate-500 uppercase tracking-widest">{{ Auth::user()->username }}</span>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-all duration-300 shadow-sm border border-slate-200">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Page Content (Scrollable with Smooth Fade) -->
                <main class="flex-1 overflow-y-auto overflow-x-hidden p-6 md:p-10 bg-slate-50/50">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Floating Gradient Decor -->
                <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-500/5 rounded-full blur-[120px] pointer-events-none"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-[30%] h-[30%] bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none"></div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
