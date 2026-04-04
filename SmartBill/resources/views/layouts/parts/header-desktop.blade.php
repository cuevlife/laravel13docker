<header class="h-16 bg-[#fafafa] dark:bg-[#1e1f22] items-center justify-between px-8 border-b border-[#e3e5e8]/50 dark:border-[#313338]/50 z-20 hidden lg:flex transition-colors">
    @php
        $workspaceBaseUrl = \App\Support\WorkspaceUrl::centralBase(request());
        $isTenant = isset($activeTenant);

        // Dynamic page title
        $headerTitle = $isTenant ? ($activeTenant->name ?? 'Workspace') : 'Central Hub';
        $headerSubtitle = null;
        if (request()->routeIs('admin.slip.index') || request()->routeIs('workspace.slip.index')) {
            $headerTitle = 'Slip Registry';
            $headerSubtitle = $activeTenant->name ?? null;
        }
        if (request()->routeIs('admin.slip.archived') || request()->routeIs('workspace.slip.archived')) {
            $headerTitle = 'Archived Slips';
            $headerSubtitle = $activeTenant->name ?? null;
        }

        if (request()->routeIs('admin.templates.*') || request()->routeIs('workspace.templates.*')) {
            $headerTitle = 'Scan Settings';
            $headerSubtitle = $activeTenant->name ?? null;
        }
        if (request()->routeIs('admin.stores.*')) {
            $headerTitle = 'Brands';
        }
        if (request()->routeIs('billing')) {
            $headerTitle = 'Tokens';
        }
        if (request()->routeIs('dashboard')) {
            $headerTitle = 'Project Hub';
        }

        // Determine if we show Scan Receipt in navbar
        $showNavScan = $isTenant && (
            request()->routeIs('admin.slip.index')
            || request()->routeIs('workspace.slip.index')
            || request()->routeIs('tenant.dashboard')
            || request()->routeIs('workspace.dashboard')
        );

        // Determine workspace action URLs for profile dropdown
        $wsSlipRoute = $isTenant ? \App\Support\WorkspaceUrl::current(request(), 'slips') : null;

        $wsTemplatesRoute = $isTenant ? \App\Support\WorkspaceUrl::current(request(), 'templates') : null;
    @endphp
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-1.5 h-6 bg-discord-green rounded-full shadow-[0_0_10px_rgba(35,165,89,0.3)] shrink-0"></div>
        <div class="min-w-0">
            <span class="text-sm font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-widest leading-none block truncate">{{ $headerTitle }}</span>
            @if($headerSubtitle)
                <span class="text-[10px] font-bold text-[#80848e] uppercase tracking-widest mt-0.5 block truncate">{{ $headerSubtitle }}</span>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-3">
        {{-- Scan Receipt (Slip pages only) --}}
        {{-- Scan Receipt (Moved to Slips page) --}}

        {{-- Token Balance --}}
        @livewire('token-balance')

        {{-- Language Toggle --}}
        <a href="{{ route('lang.switch', app()->getLocale() == 'th' ? 'en' : 'th') }}" class="flex items-center gap-2 px-3 py-1.5 bg-[#e3e5e8] dark:bg-[#2b2d31] rounded-[8px] text-[10px] font-black uppercase tracking-widest text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
            <i data-lucide="languages" class="w-3.5 h-3.5"></i>
            <span>{{ app()->getLocale() == 'th' ? 'TH' : 'EN' }}</span>
            <i data-lucide="refresh-cw" class="w-2.5 h-2.5 ml-1 opacity-50"></i>
        </a>

        <div class="h-6 w-px bg-[#e3e5e8] dark:bg-[#1e1f22]"></div>

        {{-- Profile Dropdown --}}
        <div class="relative" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen" class="w-10 h-10 rounded-[14px] bg-[#2b2d31] dark:bg-[#1e1f22] flex items-center justify-center text-white text-xs font-bold shadow-xl transition-transform active:scale-95 group relative">
                {{ substr(auth()->user()->name, 0, 1) }}
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-discord-green border-2 border-white dark:border-[#313338] rounded-full"></div>
            </button>
            <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak
                 class="absolute right-0 mt-4 w-60 bg-white dark:bg-[#2b2d31] border border-[#e3e5e8] dark:border-[#1e1f22] rounded-[8px] shadow-2xl overflow-hidden py-2 animate-in fade-in slide-in-from-top-2">
                <div class="px-5 py-3 border-b border-[#e3e5e8] dark:border-[#1e1f22] mb-1">
                    <p class="text-xs font-black text-[#1e1f22] dark:text-[#f2f3f5]">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-[#5c5e66] dark:text-[#b5bac1] truncate">{{ auth()->user()->email }}</p>
                </div>

                @if($workspaceSwitcherStores->isNotEmpty())
                    <div class="px-5 py-2">
                        <div class="text-[9px] font-black uppercase tracking-[0.24em] text-slate-400">Projects</div>
                    </div>
                    <a href="{{ $workspaceBaseUrl . '/dashboard' }}" class="flex items-center gap-3 px-5 py-2.5 text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1] hover:bg-black/5 dark:hover:bg-white/5 hover:text-[#1e1f22] dark:hover:text-white transition-colors">
                        <i data-lucide="panels-top-left" class="w-4 h-4"></i> Project Hub
                    </a>
                    <div class="mx-5 my-2 h-px bg-[#e3e5e8] dark:bg-[#1e1f22]"></div>
                @endif

                {{-- Workspace Actions (when inside a project) --}}
                @if($isTenant)
                    <div class="px-5 py-2">
                        <div class="text-[9px] font-black uppercase tracking-[0.24em] text-slate-400">Workspace</div>
                    </div>

                    <a href="{{ $wsTemplatesRoute }}" class="flex items-center gap-3 px-5 py-2.5 text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1] hover:bg-black/5 dark:hover:bg-white/5 hover:text-[#1e1f22] dark:hover:text-white transition-colors">
                        <i data-lucide="settings-2" class="w-4 h-4"></i> Scan Settings
                    </a>
                    <div class="mx-5 my-2 h-px bg-[#e3e5e8] dark:bg-[#1e1f22]"></div>
                @endif

                {{-- Dark Mode Toggle --}}
                <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="w-full flex items-center justify-between px-5 py-2.5 text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1] hover:bg-black/5 dark:hover:bg-white/5 hover:text-[#1e1f22] dark:hover:text-white transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <i data-lucide="moon" class="w-4 h-4 hidden dark:block"></i>
                        <i data-lucide="sun" class="w-4 h-4 block dark:hidden"></i>
                        Dark Mode
                    </div>
                    <div class="w-8 h-4 rounded-full transition-colors relative" :class="darkMode ? 'bg-discord-green' : 'bg-[#80848e]'">
                        <div class="w-3 h-3 bg-white rounded-full absolute top-0.5 transition-transform" :class="darkMode ? 'right-0.5' : 'left-0.5'"></div>
                    </div>
                </button>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-5 py-2.5 text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1] hover:bg-black/5 dark:hover:bg-white/5 hover:text-[#1e1f22] dark:hover:text-white transition-colors">
                    <i data-lucide="user" class="w-4 h-4"></i> Profile Settings
                </a>

                <form method="POST" action="{{ route('logout') }}" class="border-t border-[#e3e5e8] dark:border-[#1e1f22] mt-1 pt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-5 py-2.5 text-xs font-bold text-discord-red hover:bg-discord-red/10 transition-colors text-left">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
