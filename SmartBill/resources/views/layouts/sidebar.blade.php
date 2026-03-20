<div class="flex flex-col h-full transition-all duration-500 ease-in-out" 
     :class="sidebarCollapsed ? 'w-20' : 'w-72'">
    
    <!-- Brand/Logo Area -->
    <div class="flex items-center h-20 px-6 bg-white dark:bg-slate-950 border-b border-slate-100 dark:border-white/5 overflow-hidden transition-all duration-500">
        <div class="flex items-center min-w-max">
            <div class="p-2.5 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-500/40 transform hover:rotate-6 transition-transform">
                <i class="fas fa-bolt text-xl text-white"></i>
            </div>
            <div x-show="!sidebarCollapsed" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-[-20px]"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="ml-4 flex flex-col">
                <span class="text-lg font-black tracking-tighter uppercase dark:text-white leading-none">Smart<span class="text-indigo-500">Bill</span></span>
                <span class="text-[8px] font-bold text-slate-400 dark:text-slate-500 tracking-[0.3em] uppercase">Neural v2.0</span>
            </div>
        </div>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto overflow-x-hidden bg-white dark:bg-slate-900/50">
        <div x-show="!sidebarCollapsed" class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em] px-4 mb-4">Core Systems</div>
        <div x-show="sidebarCollapsed" class="h-px bg-slate-100 dark:bg-white/5 mx-2 mb-6"></div>
        
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fas fa-shapes">
            Intelligence
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.slip-reader')" :active="request()->routeIs('admin.slip-reader')" icon="fas fa-brain">
            Slip Analysis
        </x-sidebar-link>

        <div x-show="!sidebarCollapsed" class="pt-8 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em] px-4 mb-4">Registry</div>
        <div x-show="sidebarCollapsed" class="h-px bg-slate-100 dark:bg-white/5 mx-2 my-6"></div>

        <x-sidebar-link :href="route('admin.merchants')" :active="request()->routeIs('admin.merchants')" icon="fas fa-server">
            Merchants
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" icon="fas fa-fingerprint">
            Access Control
        </x-sidebar-link>

        <div x-show="!sidebarCollapsed" class="pt-8 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em] px-4 mb-4">Account</div>
        <div x-show="sidebarCollapsed" class="h-px bg-slate-100 dark:bg-white/5 mx-2 my-6"></div>

        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" icon="fas fa-sliders-h">
            Environment
        </x-sidebar-link>
    </nav>

    <!-- Sidebar Footer -->
    <div class="p-4 bg-slate-50/50 dark:bg-slate-950/30 border-t border-slate-100 dark:border-white/5 transition-all duration-500">
        <div class="flex items-center p-3 rounded-2xl bg-white dark:bg-white/5 shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden">
            <div class="h-10 w-10 min-w-[2.5rem] rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-black text-white shadow-lg">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div x-show="!sidebarCollapsed" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-[-10px]"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="ml-3 overflow-hidden">
                <p class="text-xs font-black dark:text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold truncate">{{ Auth::user()->username }}</p>
            </div>
        </div>
    </div>
</div>
