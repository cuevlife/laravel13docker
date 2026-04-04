<nav class="lg:hidden h-[68px] bg-[#f2f3f5] dark:bg-[#1e1f22] border-t border-[#e3e5e8] dark:border-[#313338] flex items-center justify-around px-2 pb-safe shadow-lg z-50">
    @php
        $workspaceBaseUrl = \App\Support\WorkspaceUrl::centralBase(request());
        $isOwnerMode = auth()->user()->isSuperAdmin() && (
            request()->routeIs('owner.*')
            || request()->routeIs('admin.dashboard')
            || request()->routeIs('admin.projects.*')
            || request()->routeIs('admin.users.*')
            || request()->routeIs('admin.topups')
            || request()->routeIs('admin.topups.*')
        );
    @endphp
    @if(isset($activeTenant))
        @php
            $slipIndexActive = request()->routeIs('tenant.dashboard')
                || request()->routeIs('workspace.dashboard')
                || request()->routeIs('admin.slip.index')
                || request()->routeIs('workspace.slip.index')
                || request()->routeIs('admin.slip.edit')
                || request()->routeIs('workspace.slip.edit');
            $archivedActive = request()->routeIs('admin.slip.archived') || request()->routeIs('workspace.slip.archived');
            $exportsActive = request()->routeIs('admin.exports.*') || request()->routeIs('workspace.exports.*');
        @endphp
        <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ $slipIndexActive ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="scan-line" class="w-5 h-5 {{ $slipIndexActive ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Slip</span>
        </a>
        <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips/archived') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ $archivedActive ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="archive" class="w-5 h-5 {{ $archivedActive ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Archive</span>
        </a>
        <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'exports') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ $exportsActive ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="download" class="w-5 h-5 {{ $exportsActive ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Export</span>
        </a>
    @elseif($isOwnerMode)
        <a href="{{ \App\Support\OwnerUrl::path(request(), 'dashboard') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('admin.dashboard') || request()->routeIs('owner.dashboard') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="chart-column-big" class="w-5 h-5 {{ request()->routeIs('admin.dashboard') || request()->routeIs('owner.dashboard') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">SaaS</span>
        </a>
        <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('admin.projects.*') || request()->routeIs('owner.projects.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="briefcase-business" class="w-5 h-5 {{ request()->routeIs('admin.projects.*') || request()->routeIs('owner.projects.*') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Projects</span>
        </a>
        <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('admin.users.*') || request()->routeIs('owner.users.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="users" class="w-5 h-5 {{ request()->routeIs('admin.users.*') || request()->routeIs('owner.users.*') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Users</span>
        </a>
        <a href="{{ \App\Support\OwnerUrl::path(request(), 'topups') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('admin.topups') || request()->routeIs('admin.topups.*') || request()->routeIs('owner.topups') || request()->routeIs('owner.topups.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="badge-dollar-sign" class="w-5 h-5 {{ request()->routeIs('admin.topups') || request()->routeIs('admin.topups.*') || request()->routeIs('owner.topups') || request()->routeIs('owner.topups.*') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Topups</span>
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('dashboard') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="grid" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Home</span>
        </a>
        <a href="{{ route('admin.stores.index') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('admin.stores.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="store" class="w-5 h-5 {{ request()->routeIs('admin.stores.*') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Stores</span>
        </a>
        <a href="{{ route('billing') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('billing') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i data-lucide="wallet" class="w-5 h-5 {{ request()->routeIs('billing') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Tokens</span>
        </a>
    @endif
    <button @click="profileOpen = true" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('profile.edit') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors focus:outline-none">
        <div class="w-6 h-6 rounded-[8px] flex items-center justify-center text-[10px] font-black shadow-sm mt-0.5 mb-px {{ request()->routeIs('profile.edit') ? 'bg-discord-green text-white' : 'bg-discord-green/20 text-[#1e1f22] dark:text-[#f2f3f5]' }}">
            {{ substr(auth()->user()->name ?? 'User', 0, 1) }}
        </div>
        <span class="text-[8px] font-black uppercase tracking-tight">Account</span>
    </button>
</nav>

<div x-show="profileOpen" x-cloak class="lg:hidden relative z-[60]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="profileOpen" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="profileOpen = false"></div>

    <div class="fixed inset-0 z-[60] w-screen overflow-y-auto pointer-events-none">
        <div class="flex min-h-full items-end justify-center p-0 text-center">
            <div x-show="profileOpen"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transform transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full"
                 class="relative transform overflow-hidden rounded-t-[32px] bg-[#fafafa] dark:bg-[#2b2d31] text-left shadow-2xl transition-all w-full border-t border-[#e3e5e8] dark:border-[#1e1f22] pointer-events-auto">

                <div class="px-6 py-6 pt-8">
                    <div class="absolute top-3 left-1/2 -translate-x-1/2 w-12 h-1.5 bg-[#d5d6d9] dark:bg-[#1e1f22] rounded-full"></div>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-[16px] bg-discord-green flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-discord-green/20">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[#1e1f22] dark:text-[#f2f3f5] tracking-tight">{{ auth()->user()->name }}</h3>
                            <p class="text-[11px] text-[#5c5e66] dark:text-[#b5bac1] font-bold mb-1.5">{{ auth()->user()->email }}</p>
                            <div class="flex items-center gap-1.5 text-amber-500 bg-amber-500/10 dark:bg-amber-500/20 px-2 py-1 rounded-md w-max">
                                <i data-lucide="coins" class="w-3 h-3"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ number_format(auth()->user()->tokens) }} Tokens</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pb-safe-bottom">
                        @if($workspaceSwitcherStores->isNotEmpty())
                            <div class="px-1 pb-2">
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Project Switcher</div>
                                <a href="{{ $workspaceBaseUrl . '/dashboard' }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)] mb-2">
                                    <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                        <i data-lucide="panels-top-left" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                                    </div>
                                    Project Hub
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                                </a>
                            </div>
                        @endif

                        @if(isset($activeTenant))
                            @php
                                $mobileExportsRoute = \App\Support\WorkspaceUrl::current(request(), 'exports');
                                $mobileTemplatesRoute = \App\Support\WorkspaceUrl::current(request(), 'templates');
                            @endphp
                            <div class="px-1 pb-2">
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Workspace</div>
                                <a href="{{ $mobileExportsRoute }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)] mb-2">
                                    <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                        <i data-lucide="download" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                                    </div>
                                    Export Center
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                                </a>
                                <a href="{{ $mobileTemplatesRoute }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                                    <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                        <i data-lucide="settings-2" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                                    </div>
                                    Scan Settings
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                                </a>
                            </div>
                        @endif

                        <a href="{{ route('billing') }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                            <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                <i data-lucide="wallet" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                            </div>
                            Token Wallet
                            <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                        </a>

                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ \App\Support\OwnerUrl::path(request(), 'dashboard') }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                                <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                    <i data-lucide="chart-column-big" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                                </div>
                                SaaS Dashboard
                                <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                            </a>

                            <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                                <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                    <i data-lucide="briefcase-business" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                                </div>
                                Manage Projects
                                <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                            </a>

                            <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                                <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                    <i data-lucide="users" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                                </div>
                                Manage Users
                                <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                            </a>

                            <a href="{{ \App\Support\OwnerUrl::path(request(), 'topups') }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                                <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                    <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                                </div>
                                Review Topups
                                <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                            <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                <i data-lucide="settings" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                            </div>
                            Account Settings
                            <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                        </a>

                        <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                            <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                <i data-lucide="moon" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1] hidden dark:block"></i>
                                <i data-lucide="sun" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1] block dark:hidden"></i>
                            </div>
                            <span x-text="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"></span>
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="pt-2 mt-2">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-4 px-4 py-4 bg-[#fdf0f0] dark:bg-discord-red/10 border border-discord-red/20 hover:bg-[#fce4e4] dark:hover:bg-discord-red/20 rounded-2xl text-[13px] font-bold text-discord-red transition-all shadow-[0_2px_10px_rgba(237,66,69,0.05)]">
                                <div class="w-9 h-9 rounded-full bg-white dark:bg-[#2b2d31] shadow-sm flex items-center justify-center">
                                    <i data-lucide="log-out" class="w-4 h-4 text-discord-red"></i>
                                </div>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>