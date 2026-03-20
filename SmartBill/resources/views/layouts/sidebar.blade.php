<div class="flex flex-col h-full overflow-hidden">
    
    <!-- Brand -->
    <div class="h-14 flex items-center px-6 border-b border-white/5">
        <div class="flex items-center space-x-3 min-w-max">
            <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center shadow-lg shadow-rose-500/20">
                <i data-lucide="zap" class="w-5 h-5 text-white"></i>
            </div>
            <span x-show="!sidebarCollapsed" class="text-sm font-black tracking-widest text-white uppercase italic">Smart<span class="text-rose-500">Bill</span></span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto custom-scrollbar">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="layout-grid">
            Dashboard
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.slip-reader')" :active="request()->routeIs('admin.slip-reader')" icon="image-upscale">
            Scan Slips
        </x-sidebar-link>

        <div x-show="!sidebarCollapsed" class="mt-8 mb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Settings</div>
        
        <x-sidebar-link :href="route('admin.merchants')" :active="request()->routeIs('admin.merchants')" icon="store">
            Merchants
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" icon="users">
            User List
        </x-sidebar-link>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-white/5 bg-black/10">
        <div class="text-[9px] font-bold text-slate-600 uppercase tracking-[0.3em] text-center">Version 3.0.0</div>
    </div>
</div>
