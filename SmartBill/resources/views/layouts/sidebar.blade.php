<div class="hidden md:flex flex-col w-64 bg-slate-900 text-white h-screen fixed left-0 top-0 shadow-2xl z-50">
    <!-- Brand/Logo -->
    <div class="flex items-center justify-center h-20 bg-slate-950 border-b border-white/5">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-500/50">
                <i class="fas fa-file-invoice-dollar text-xl text-white"></i>
            </div>
            <span class="text-xl font-black tracking-tighter uppercase">Smart<span class="text-indigo-500">Bill</span></span>
        </a>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4">Main Menu</div>
        
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fas fa-th-large">
            Dashboard
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.slip-reader')" :active="request()->routeIs('admin.slip-reader')" icon="fas fa-robot">
            AI Slip Reader
        </x-sidebar-link>

        <div class="pt-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4">Management</div>

        <x-sidebar-link :href="route('admin.merchants')" :active="request()->routeIs('admin.merchants')" icon="fas fa-store">
            Merchants
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" icon="fas fa-users">
            User List
        </x-sidebar-link>

        <div class="pt-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4">System</div>
        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" icon="fas fa-cog">
            Settings
        </x-sidebar-link>
    </nav>

    <!-- Footer / User Info -->
    <div class="p-4 bg-slate-950/50 border-t border-white/5">
        <div class="flex items-center p-3 bg-white/5 rounded-xl border border-white/5">
            <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white shadow-lg">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-xs font-bold truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->username }}</p>
            </div>
        </div>
    </div>
</div>
