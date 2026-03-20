<div class="flex flex-col h-full bg-white dark:bg-[#0b0f1a] transition-all duration-500 overflow-hidden">
    
    <!-- Logo Area -->
    <div class="flex items-center h-16 px-6 border-b border-slate-100 dark:border-white/5">
        <div class="flex items-center min-w-max">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center shadow-sm">
                <i data-lucide="zap" class="w-5 h-5 text-white"></i>
            </div>
            <div x-show="!sidebarCollapsed" x-transition class="ml-3">
                <span class="text-sm font-bold tracking-tight text-slate-800 dark:text-slate-100 uppercase">SmartBill</span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-6 space-y-1">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="layout-dashboard">
            Dashboard
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.slip-reader')" :active="request()->routeIs('admin.slip-reader')" icon="scan-line">
            Slip Reader
        </x-sidebar-link>

        <div x-show="!sidebarCollapsed" class="mt-8 mb-2 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Setup</div>
        
        <x-sidebar-link :href="route('admin.merchants')" :active="request()->routeIs('admin.merchants')" icon="store">
            Merchants
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" icon="users">
            User List
        </x-sidebar-link>
    </nav>

    <!-- Sidebar Footer (Profile) -->
    <div class="p-4 border-t border-slate-100 dark:border-white/5">
        <div class="flex items-center p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer overflow-hidden">
            <div class="w-8 h-8 min-w-[2rem] rounded-full bg-slate-100 dark:bg-white/10 flex items-center justify-center border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-600 dark:text-slate-300">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">
                <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-400 truncate">Administrator</p>
            </div>
        </div>
    </div>
</div>
