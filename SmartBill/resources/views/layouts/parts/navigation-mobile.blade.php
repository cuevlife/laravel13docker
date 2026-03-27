<nav class="sm:hidden h-[68px] bg-[#f2f3f5] dark:bg-[#1e1f22] border-t border-[#e3e5e8] dark:border-[#313338] flex items-center justify-around px-2 pb-safe shadow-lg z-50">
    <a href="{{ route('admin.slip.index') }}" class="flex flex-col items-center justify-center gap-1 w-16 {{ request()->routeIs('admin.slip.*') || request()->routeIs('dashboard') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
        <i data-lucide="layers" class="w-[22px] h-[22px] {{ request()->routeIs('admin.slip.*') || request()->routeIs('dashboard') ? 'text-discord-green' : '' }}"></i>
        <span class="text-[9px] font-black uppercase tracking-tighter">Registry</span>
    </a>
    <a href="{{ route('admin.stores.index') }}" class="flex flex-col items-center justify-center gap-1 w-16 {{ request()->routeIs('admin.stores.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
        <i data-lucide="store" class="w-[22px] h-[22px] {{ request()->routeIs('admin.stores.*') ? 'text-discord-green' : '' }}"></i>
        <span class="text-[9px] font-black uppercase tracking-tighter">Brands</span>
    </a>
    <a href="{{ route('admin.templates.index') }}" class="flex flex-col items-center justify-center gap-1 w-16 {{ request()->routeIs('admin.templates.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
        <i data-lucide="file-json" class="w-[22px] h-[22px] {{ request()->routeIs('admin.templates.*') ? 'text-discord-green' : '' }}"></i>
        <span class="text-[9px] font-black uppercase tracking-tighter">Profiles</span>
    </a>
    <button @click="profileOpen = true" class="flex flex-col items-center justify-center gap-1 w-16 {{ request()->routeIs('profile.edit') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors focus:outline-none">
        <div class="w-6 h-6 rounded-[8px] flex items-center justify-center text-[10px] font-black shadow-sm mt-0.5 mb-px {{ request()->routeIs('profile.edit') ? 'bg-discord-green text-white' : 'bg-discord-green/20 text-[#1e1f22] dark:text-[#f2f3f5]' }}">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
        <span class="text-[9px] font-black uppercase tracking-tighter">Account</span>
    </button>
</nav>

<!-- Mobile Profile Bottom Sheet Popup -->
<div x-show="profileOpen" x-cloak class="md:hidden relative z-[60]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                    <!-- Drawer handle indicator -->
                    <div class="absolute top-3 left-1/2 -translate-x-1/2 w-12 h-1.5 bg-[#d5d6d9] dark:bg-[#1e1f22] rounded-full"></div>
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-[16px] bg-discord-green flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-discord-green/20">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[#1e1f22] dark:text-[#f2f3f5] tracking-tight">{{ auth()->user()->name }}</h3>
                            <p class="text-[11px] text-[#5c5e66] dark:text-[#b5bac1] font-bold">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2 pb-safe-bottom">
                        <!-- Settings Form Route -->
                        <a href="{{ route('profile.edit') }}" class="w-full flex items-center gap-4 px-4 py-4 bg-white dark:bg-[#1e1f22] hover:bg-[#f2f3f5] dark:hover:bg-[#313338] rounded-2xl text-[13px] font-bold text-[#1e1f22] dark:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                            <div class="w-9 h-9 rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center">
                                <i data-lucide="settings" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1]"></i>
                            </div>
                            Account Settings
                            <i data-lucide="chevron-right" class="w-4 h-4 text-[#80848e] ml-auto"></i>
                        </a>
                        
                        <!-- Dark Mode Setup -->
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
