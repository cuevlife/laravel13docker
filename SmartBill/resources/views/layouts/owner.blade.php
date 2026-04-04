<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased" x-data="{ darkMode: localStorage.getItem('theme') === 'dark', navOpen: false }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Control Center - {{ config('app.name', 'SmartBill') }}</title>

        <!-- Favicon (Receipt Icon) -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23b74d25' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z'/><path d='M16 8h-6'/><path d='M16 12H8'/><path d='M13 16H8'/></svg>">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">

        @livewireStyles
        @livewireScripts
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans">
        @php
            $ownerTitle = 'Control Center';
            if (request()->routeIs('admin.projects.*') || request()->routeIs('owner.projects.*')) $ownerTitle = 'Project Control';
            if (request()->routeIs('admin.users.*') || request()->routeIs('owner.users.*')) $ownerTitle = 'User Control';
            if (request()->routeIs('admin.topups') || request()->routeIs('admin.topups.*') || request()->routeIs('owner.topups') || request()->routeIs('owner.topups.*')) $ownerTitle = 'Topup Review';

            $ownerNav = [
                [
                    'href' => \App\Support\OwnerUrl::path(request(), 'dashboard'),
                    'label' => 'Overview',
                    'icon' => 'shield-check',
                    'active' => request()->routeIs('admin.dashboard') || request()->routeIs('owner.dashboard'),
                ],
                [
                    'href' => \App\Support\OwnerUrl::path(request(), 'projects'),
                    'label' => 'Projects',
                    'icon' => 'briefcase-business',
                    'active' => request()->routeIs('admin.projects.*') || request()->routeIs('owner.projects.*'),
                ],
                [
                    'href' => \App\Support\OwnerUrl::path(request(), 'users'),
                    'label' => 'Users',
                    'icon' => 'users',
                    'active' => request()->routeIs('admin.users.*') || request()->routeIs('owner.users.*'),
                ],
                [
                    'href' => \App\Support\OwnerUrl::path(request(), 'topups'),
                    'label' => 'Topups',
                    'icon' => 'badge-dollar-sign',
                    'active' => request()->routeIs('admin.topups') || request()->routeIs('admin.topups.*') || request()->routeIs('owner.topups') || request()->routeIs('owner.topups.*'),
                ],
            ];
        @endphp

        <div class="min-h-screen control-shell">
            <div class="mx-auto flex min-h-screen max-w-[1700px]">
                <aside class="hidden w-[320px] shrink-0 border-r border-black/5 px-6 py-6 dark:border-white/10 lg:block">
                    <div class="control-card p-6">
                        <div class="inline-flex items-center gap-2 rounded-full bg-[#11202d] px-3 py-2 text-[10px] font-black uppercase tracking-[0.22em] text-white dark:bg-[#f7f3ea] dark:text-[#11202d]">
                            <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                            Super Admin
                        </div>
                        <h1 class="mt-5 text-3xl font-black uppercase tracking-tight text-[#11202d] dark:text-[#f7f3ea]">SmartBill Control</h1>
                        <p class="mt-3 text-sm font-bold leading-relaxed text-slate-500 dark:text-slate-300">
                            Keep system control separate from day-to-day workspace usage so operators can manage users, projects, tokens, and queues from a true control plane.
                        </p>

                        <div class="mt-8 space-y-2">
                            @foreach($ownerNav as $item)
                                <a href="{{ $item['href'] }}" class="control-nav-link {{ $item['active'] ? 'active' : '' }}">
                                    <i data-lucide="{{ $item['icon'] }}" class="h-4.5 w-4.5"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="control-card mt-6 p-6">
                        <div class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">Operator</div>
                        <div class="mt-3 text-xl font-black text-[#11202d] dark:text-[#f7f3ea]">{{ auth()->user()->name }}</div>
                        <div class="mt-2 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ auth()->user()->email }}</div>

                        <div class="mt-6 grid gap-3">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-[1.1rem] border border-black/10 px-4 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-[#11202d] transition hover:border-[#11202d] hover:bg-black/5 dark:border-white/10 dark:text-[#f7f3ea] dark:hover:bg-white/[0.04]">
                                <i data-lucide="panel-top-open" class="h-4 w-4"></i>
                                Project Hub
                            </a>
                            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="inline-flex items-center justify-center gap-2 rounded-[1.1rem] border border-black/10 px-4 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-[#11202d] transition hover:border-[#11202d] hover:bg-black/5 dark:border-white/10 dark:text-[#f7f3ea] dark:hover:bg-white/[0.04]">
                                <i data-lucide="sun-moon" class="h-4 w-4"></i>
                                Toggle Theme
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-[1.1rem] bg-[#b74d25] px-4 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#9c3f1c]">
                                    <i data-lucide="log-out" class="h-4 w-4"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                <div class="flex min-w-0 flex-1 flex-col">
                    <header class="sticky top-0 z-30 border-b border-black/5 bg-[#f4efe6]/90 px-4 py-4 backdrop-blur-xl dark:border-white/10 dark:bg-[#101a22]/90 lg:px-8">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <button @click="navOpen = true" class="inline-flex h-11 w-11 items-center justify-center rounded-[1rem] border border-black/10 text-[#11202d] dark:border-white/10 dark:text-[#f7f3ea] lg:hidden">
                                    <i data-lucide="menu" class="h-5 w-5"></i>
                                </button>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">Super Admin Control Plane</div>
                                    <div class="mt-1 text-2xl font-black uppercase tracking-tight text-[#11202d] dark:text-[#f7f3ea]">{{ $ownerTitle }}</div>
                                </div>
                            </div>

                            <div class="hidden items-center gap-3 lg:flex">
                                <div class="rounded-full border border-black/10 bg-white/80 px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-slate-500 dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-300">
                                    {{ auth()->user()->username ?: auth()->user()->email }}
                                </div>
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-[1rem] border border-black/10 px-4 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-[#11202d] transition hover:border-[#11202d] hover:bg-black/5 dark:border-white/10 dark:text-[#f7f3ea] dark:hover:bg-white/[0.04]">
                                    <i data-lucide="panel-top-open" class="h-4 w-4"></i>
                                    Project Hub
                                </a>
                            </div>
                        </div>
                    </header>

                    <main class="flex-1 px-4 pb-24 pt-6 lg:px-8 lg:pb-10 lg:pt-8">
                        @if (session('status'))
                            <div class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        {{ $slot }}
                    </main>
                </div>
            </div>

            <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-black/5 bg-[#f4efe6]/95 px-2 py-3 backdrop-blur-xl dark:border-white/10 dark:bg-[#101a22]/95 lg:hidden">
                <div class="flex items-center justify-around">
                    @foreach($ownerNav as $item)
                        <a href="{{ $item['href'] }}" class="control-mobile-link {{ $item['active'] ? 'text-[#b74d25]' : 'text-slate-500 dark:text-slate-300' }}">
                            <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                    <a href="{{ route('dashboard') }}" class="control-mobile-link text-slate-500 dark:text-slate-300">
                        <i data-lucide="panel-top-open" class="h-5 w-5"></i>
                        <span>Hub</span>
                    </a>
                </div>
            </nav>

            <div x-show="navOpen" x-cloak class="fixed inset-0 z-40 lg:hidden" aria-modal="true" role="dialog">
                <div x-show="navOpen" x-transition.opacity class="absolute inset-0 bg-black/60" @click="navOpen = false"></div>
                <div x-show="navOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative h-full w-[88%] max-w-sm bg-[#f7f3ea] p-6 dark:bg-[#101a22]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">Control Plane</div>
                            <div class="mt-2 text-2xl font-black uppercase tracking-tight text-[#11202d] dark:text-[#f7f3ea]">SmartBill Control</div>
                        </div>
                        <button @click="navOpen = false" class="inline-flex h-11 w-11 items-center justify-center rounded-[1rem] border border-black/10 text-[#11202d] dark:border-white/10 dark:text-[#f7f3ea]">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>

                    <div class="mt-8 space-y-2">
                        @foreach($ownerNav as $item)
                            <a href="{{ $item['href'] }}" class="control-nav-link {{ $item['active'] ? 'active' : '' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="h-4.5 w-4.5"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-8 rounded-[1.75rem] border border-black/5 bg-white/80 p-5 dark:border-white/10 dark:bg-white/[0.04]">
                        <div class="text-sm font-black text-[#11202d] dark:text-[#f7f3ea]">{{ auth()->user()->name }}</div>
                        <div class="mt-2 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ auth()->user()->email }}</div>
                        <div class="mt-5 grid gap-3">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-[1rem] border border-black/10 px-4 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-[#11202d] transition hover:border-[#11202d] hover:bg-black/5 dark:border-white/10 dark:text-[#f7f3ea] dark:hover:bg-white/[0.04]">
                                <i data-lucide="panel-top-open" class="h-4 w-4"></i>
                                Project Hub
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-[1rem] bg-[#b74d25] px-4 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#9c3f1c]">
                                    <i data-lucide="log-out" class="h-4 w-4"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
