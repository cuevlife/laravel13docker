<nav class="flex md:hidden fixed bottom-0 inset-x-0 h-16 bg-[#fafafa] dark:bg-[#1e1f22] border-t border-[#e3e5e8] dark:border-[#313338] items-center justify-around px-2 pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-50 transition-colors">
    @php
        $isAdminMode = request()->is('admin/*') || request()->is('admin');
        $isTenant = isset($activeTenant) && !$isAdminMode;
        $navs = [];

        if ($isAdminMode) {
            $navs = [
                ['href' => \App\Support\OwnerUrl::path(request(), 'users'), 'icon' => 'bi-people-fill', 'label' => 'Users', 'active' => request()->routeIs('admin.users*')],
                ['href' => \App\Support\OwnerUrl::path(request(), 'folders'), 'icon' => 'bi-folder2-open', 'label' => 'Folders', 'active' => request()->routeIs('admin.folders*')],
                ['href' => route('admin.settings'), 'icon' => 'bi-cpu-fill', 'label' => 'AI Settings', 'active' => request()->routeIs('admin.settings')],
            ];
        } elseif ($isTenant) {
            $slipIndexActive = request()->routeIs('tenant.dashboard') || request()->routeIs('workspace.dashboard') || request()->routeIs('workspace.slip.index') || request()->routeIs('workspace.slip.edit');
            $isOwner = ((int)($activeTenant->user_id ?? 0) === (int)auth()->id() || auth()->user()->isSuperAdmin());
            
            $navs = [
                ['href' => \App\Support\WorkspaceUrl::current(request(), 'slips'), 'icon' => 'bi-qr-code-scan', 'label' => 'Inbox', 'active' => $slipIndexActive],
            ];

            if ($isOwner) {
                $navs[] = ['href' => \App\Support\WorkspaceUrl::current(request(), 'settings'), 'icon' => 'bi-gear-fill', 'label' => 'Settings', 'active' => request()->routeIs('workspace.settings')];
            }
        } else {
            $navs = [
                ['href' => route('dashboard'), 'icon' => 'bi-grid-fill', 'label' => 'Hub', 'active' => request()->routeIs('dashboard')],
            ];
        }
    @endphp

    @foreach($navs as $nav)
        <a href="{{ $nav['href'] }}" class="flex flex-col items-center justify-center gap-1 w-16 h-full {{ $nav['active'] ? ($isAdminMode ? 'text-rose-500' : 'text-discord-green') : 'text-[#80848e] hover:text-[#1e1f22] dark:hover:text-white' }} transition-colors">
            <i class="bi {{ $nav['icon'] }} text-xl {{ $nav['active'] ? ($isAdminMode ? 'drop-shadow-[0_0_8px_rgba(244,63,94,0.4)]' : 'drop-shadow-[0_0_8px_rgba(35,165,89,0.4)]') : '' }}"></i>
            <span class="text-[9px] font-black uppercase tracking-tight">{{ $nav['label'] }}</span>
        </a>
    @endforeach

    <!-- Mobile Profile Trigger -->
    <button @click="profileOpen = true" class="flex flex-col items-center justify-center gap-1 w-16 h-full text-[#80848e] hover:text-[#1e1f22] dark:hover:text-white transition-colors focus:outline-none">
        <i class="bi bi-person-circle text-xl"></i>
        <span class="text-[9px] font-black uppercase tracking-tight">Profile</span>
    </button>
</nav>
