<div class="flex flex-col h-full py-6 items-center gap-4 bg-[#fafafa] dark:bg-[#1e1f22] w-20 transition-colors">
    <div class="flex flex-col items-center gap-4 w-full flex-1">
        @php
            $isAdminMode = request()->is('admin/*') || request()->is('admin');
            $isTenant = isset($activeTenant) && !$isAdminMode;
            $navs = [];

            if ($isAdminMode) {
                $navs = [
                    ['href' => \App\Support\OwnerUrl::path(request(), 'users'), 'icon' => 'bi-people-fill', 'label' => 'User Management', 'active' => request()->routeIs('admin.users*')],
                    ['href' => \App\Support\OwnerUrl::path(request(), 'projects'), 'icon' => 'bi-folder2-open', 'label' => 'Folder Management', 'active' => request()->routeIs('admin.projects*')],
                    ['href' => \App\Support\OwnerUrl::path(request(), 'topups'), 'icon' => 'bi-cash-coin', 'label' => 'Topup Requests', 'active' => request()->routeIs('admin.topups*')],
                ];
            } elseif ($isTenant) {
                $navs = [
                    ['href' => \App\Support\WorkspaceUrl::current(request(), 'slips'), 'icon' => 'bi-qr-code-scan', 'label' => 'Inbox', 'active' => request()->routeIs('workspace.slip.*')],
                    ['href' => \App\Support\WorkspaceUrl::current(request(), 'exports'), 'icon' => 'bi-file-earmark-arrow-down', 'label' => 'Export', 'active' => request()->routeIs('workspace.exports.*')],
                ];
            } else {
                $navs = [
                    ['href' => route('dashboard'), 'icon' => 'bi-grid-fill', 'label' => 'Folder Hub', 'active' => request()->routeIs('dashboard')],
                    ['href' => route('billing'), 'icon' => 'bi-wallet2', 'label' => 'Tokens', 'active' => request()->routeIs('billing')],
                ];
            }
        @endphp

        @foreach($navs as $nav)
            <div class="relative flex items-center justify-center w-full group">
                <!-- Active Indicator -->
                <div class="absolute left-0 w-1 bg-discord-green rounded-r-full transition-all duration-300 {{ $nav['active'] ? 'h-10 opacity-100' : 'h-0 opacity-0 group-hover:h-5 group-hover:opacity-50' }}"></div>

                <a href="{{ $nav['href'] }}"
                   class="w-12 h-12 flex items-center justify-center transition-all duration-200 rounded-[24px] {{ $nav['active'] ? 'bg-discord-green text-white rounded-[16px] shadow-lg shadow-green-500/20' : 'text-[#80848e] hover:bg-white dark:hover:bg-white/5 hover:text-discord-green hover:rounded-[16px]' }}"
                   title="{{ $nav['label'] }}">
                    <i class="bi {{ $nav['icon'] }} text-xl"></i>
                </a>
            </div>
        @endforeach

        @if(auth()->user()->isSuperAdmin() && !$isAdminMode)
            <div class="w-8 h-[2px] bg-[#d5d6d9] dark:bg-[#2b2d31] my-2 transition-colors"></div>
            <div class="relative flex items-center justify-center w-full group">
                <div class="absolute left-0 w-1 bg-indigo-500 rounded-r-full transition-all duration-300 h-0 opacity-0 group-hover:h-5 group-hover:opacity-50"></div>
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}"
                   class="w-12 h-12 flex items-center justify-center transition-all duration-200 rounded-[24px] text-[#80848e] hover:bg-white dark:hover:bg-white/5 hover:text-indigo-500 hover:rounded-[16px]"
                   title="Switch to Control Plane">
                    <i class="bi bi-shield-lock-fill text-xl"></i>
                </a>
            </div>
        @endif
    </div>

    <div class="mt-auto">
        <button class="w-12 h-12 flex items-center justify-center text-[#80848e] hover:text-discord-green transition-colors group">
            <i class="bi bi-question-circle text-xl group-hover:scale-110 transition-transform"></i>
        </button>
    </div>
</div>
