<header class="h-14 md:h-16 bg-[#fafafa] dark:bg-[#1e1f22] items-center justify-between px-4 md:px-8 border-b border-[#e3e5e8]/50 dark:border-[#313338]/50 z-20 flex transition-colors">
    @php
        $workspaceBaseUrl = \App\Support\WorkspaceUrl::centralBase(request());
        $isTenant = isset($activeTenant);
        $isAdminMode = request()->is('admin*') || request()->routeIs('admin.*');

        if ($isAdminMode) {
            $headerTitle = 'Control Plane';
            $headerSubtitle = 'System Management';
        } elseif ($isTenant) {
            $headerTitle = 'Slip Registry';
            $headerSubtitle = $activeTenant->name ?? null;
        } else {
            $headerTitle = 'Central Hub';
            $headerSubtitle = 'Select Workspace';
        }

        if (request()->routeIs('dashboard')) {
            $headerTitle = 'Folder Hub';
            $headerSubtitle = 'Overview';
        }
        if (request()->routeIs('billing')) {
            $headerTitle = 'Token Wallet';
            $headerSubtitle = auth()->user()->name;
        }
    @endphp
    <div class="flex items-center gap-3 min-w-0">
        <button @click="sidebarOpen = true" class="md:hidden flex items-center justify-center text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors mr-2 outline-none">
            <i class="bi bi-list text-2xl"></i>
        </button>
        <div class="w-1.5 h-5 md:h-6 bg-discord-green rounded-full shadow-[0_0_10px_rgba(35,165,89,0.3)] shrink-0 hidden md:block"></div>
        <div class="min-w-0">
            <span class="text-xs md:text-sm font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-widest leading-none block truncate">{{ $headerTitle }}</span>
            @if($headerSubtitle)
                <span class="text-[8px] md:text-[10px] font-bold text-[#80848e] uppercase tracking-widest mt-0.5 block truncate">{{ $headerSubtitle }}</span>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-2 md:gap-3">
        @include('layouts.parts.header-token')

        <a href="{{ route('lang.switch', app()->getLocale() == 'th' ? 'en' : 'th') }}" class="flex items-center justify-center w-8 h-8 md:w-auto md:h-auto md:px-3 md:py-1.5 bg-[#e3e5e8] dark:bg-[#2b2d31] rounded-xl text-[10px] font-black uppercase tracking-widest text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
            <i class="bi bi-translate text-xs"></i>
            <span class="hidden md:inline ml-1">{{ app()->getLocale() == 'th' ? 'TH' : 'EN' }}</span>
        </a>

        <div class="h-4 md:h-6 w-px bg-[#e3e5e8] dark:bg-[#1e1f22]"></div>

        <div class="relative" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen" class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-[#2b2d31] dark:bg-[#1e1f22] flex items-center justify-center text-white text-[10px] md:text-xs font-bold shadow-xl transition-transform active:scale-95 group relative">
                {{ substr(auth()->user()->name, 0, 1) }}
                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 md:w-3 md:h-3 bg-discord-green border-2 border-white dark:border-[#313338] rounded-full"></div>
            </button>
            <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak
                 class="absolute right-0 mt-4 w-60 bg-white dark:bg-[#2b2d31] border border-[#e3e5e8] dark:border-[#1e1f22] rounded-xl shadow-2xl overflow-hidden py-2 animate-in fade-in slide-in-from-top-2">
                <div class="px-5 py-3 border-b border-[#e3e5e8] dark:border-[#1e1f22] mb-1">
                    <p class="text-xs font-black text-[#1e1f22] dark:text-[#f2f3f5]">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-[#5c5e66] dark:text-[#b5bac1] truncate">{{ auth()->user()->email }}</p>
                </div>

                @if(auth()->user()->isSuperAdmin() && !request()->is('admin*'))
                    <div class="px-5 py-2">
                        <div class="text-[9px] font-black uppercase tracking-[0.24em] text-slate-400">Control Plane</div>
                    </div>
                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="flex items-center gap-3 px-5 py-2.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors">
                        <i class="bi bi-shield-lock-fill text-sm"></i> System Admin
                    </a>
                    <div class="mx-5 my-2 h-px bg-[#e3e5e8] dark:bg-[#1e1f22]"></div>
                @endif

                {{-- Only show Folder Hub if NOT in Admin mode and not already on Dashboard --}}
                @if(!request()->is('admin*') && $workspaceSwitcherStores->isNotEmpty() && !request()->routeIs('dashboard'))
                    <div class="px-5 py-2">
                        <div class="text-[9px] font-black uppercase tracking-[0.24em] text-slate-400">Folders</div>
                    </div>
                    <a href="{{ $workspaceBaseUrl . '/dashboard' }}" class="flex items-center gap-3 px-5 py-2.5 text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1] hover:bg-black/5 dark:hover:bg-white/5 hover:text-[#1e1f22] dark:hover:text-white transition-colors">
                        <i class="bi bi-window-stack text-sm"></i> Folder Hub
                    </a>
                    <div class="mx-5 my-2 h-px bg-[#e3e5e8] dark:bg-[#1e1f22]"></div>
                @endif

                <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="w-full flex items-center justify-between px-5 py-2.5 text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1] hover:bg-black/5 dark:hover:bg-white/5 hover:text-[#1e1f22] dark:hover:text-white transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-moon-stars-fill text-sm hidden dark:block"></i>
                        <i class="bi bi-sun-fill text-sm block dark:hidden"></i>
                        Dark Mode
                    </div>
                    <div class="w-8 h-4 rounded-full transition-colors relative" :class="darkMode ? 'bg-discord-green' : 'bg-[#80848e]'">
                        <div class="w-3 h-3 bg-white rounded-full absolute top-0.5 transition-transform" :class="darkMode ? 'right-0.5' : 'left-0.5'"></div>
                    </div>
                </button>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-5 py-2.5 text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1] hover:bg-black/5 dark:hover:bg-white/5 hover:text-[#1e1f22] dark:hover:text-white transition-colors">
                    <i class="bi bi-person-fill text-sm"></i> Profile Settings
                </a>

                <form method="POST" action="{{ route('logout') }}" class="border-t border-[#e3e5e8] dark:border-[#1e1f22] mt-1 pt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-5 py-2.5 text-xs font-bold text-discord-red hover:bg-discord-red/10 transition-colors text-left">
                        <i class="bi bi-box-arrow-right text-sm"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
