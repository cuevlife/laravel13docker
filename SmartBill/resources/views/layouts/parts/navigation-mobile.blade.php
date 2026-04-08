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
            <i class="bi bi-qr-code-scan text-xl {{ $slipIndexActive ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Inbox</span>
        </a>
        <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips/archived') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ $archivedActive ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i class="bi bi-archive-fill text-xl {{ $archivedActive ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Archive</span>
        </a>
        <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'exports') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ $exportsActive ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i class="bi bi-file-earmark-arrow-down-fill text-xl {{ $exportsActive ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Export</span>
        </a>
    @elseif($isOwnerMode)
        <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('admin.users.*') || request()->routeIs('owner.users.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i class="bi bi-people-fill text-xl {{ request()->routeIs('admin.users.*') || request()->routeIs('owner.users.*') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Users</span>
        </a>
        <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('admin.projects.*') || request()->routeIs('owner.projects.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i class="bi bi-folder2-open text-xl {{ request()->routeIs('admin.projects.*') || request()->routeIs('owner.projects.*') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Folders</span>
        </a>
        <a href="{{ \App\Support\OwnerUrl::path(request(), 'topups') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('admin.topups') || request()->routeIs('admin.topups.*') || request()->routeIs('owner.topups') || request()->routeIs('owner.topups.*') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i class="bi bi-cash-coin text-xl {{ request()->routeIs('admin.topups') || request()->routeIs('admin.topups.*') || request()->routeIs('owner.topups') || request()->routeIs('owner.topups.*') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Topups</span>
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('dashboard') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i class="bi bi-grid-fill text-xl {{ request()->routeIs('dashboard') ? 'text-discord-green' : '' }}"></i>
            <span class="text-[8px] font-black uppercase tracking-tight">Hub</span>
        </a>
        <a href="{{ route('billing') }}" class="flex flex-col items-center justify-center gap-1 w-12 {{ request()->routeIs('billing') ? 'text-discord-green' : 'text-[#80848e] hover:text-[#5c5e66] dark:hover:text-[#b5bac1]' }} transition-colors">
            <i class="bi bi-wallet2 text-xl {{ request()->routeIs('billing') ? 'text-discord-green' : '' }}"></i>
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
