<div class="flex flex-col h-full overflow-hidden transition-colors duration-500">
    
    <!-- Brand -->
    <div class="h-16 flex items-center px-6 border-b border-slate-100 dark:border-white/5 bg-white dark:bg-discord-darker">
        <div class="flex items-center space-x-3 min-w-max">
            <div class="w-8 h-8 bg-rose-500 rounded-xl flex items-center justify-center shadow-lg shadow-rose-500/20 transform hover:rotate-12 transition-all">
                <i data-lucide="zap" class="w-5 h-5 text-white"></i>
            </div>
            <span x-show="!sidebarCollapsed" x-transition class="text-sm font-black tracking-widest text-slate-800 dark:text-white uppercase italic">Smart<span class="text-rose-500">Bill</span></span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto custom-scrollbar bg-white dark:bg-discord-darker/50">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="layout-grid">
            {{ __('Dashboard') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.slip-reader')" :active="request()->routeIs('admin.slip-reader')" icon="scan">
            {{ __('Scan Slips') }}
        </x-sidebar-link>

        <div x-show="!sidebarCollapsed" class="mt-8 mb-2 px-4 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em] italic">{{ __('Settings') }}</div>
        
        <x-sidebar-link :href="route('admin.merchants')" :active="request()->routeIs('admin.merchants')" icon="store">
            {{ __('Merchants') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" icon="users">
            {{ __('User List') }}
        </x-sidebar-link>
    </nav>

    <!-- Footer Status -->
    <div class="p-4 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
        <div class="flex items-center justify-center space-x-2">
            <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span x-show="!sidebarCollapsed" class="text-[9px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">Node Synchronized</span>
        </div>
    </div>
</div>
