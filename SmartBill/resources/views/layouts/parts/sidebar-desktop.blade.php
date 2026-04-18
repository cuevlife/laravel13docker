<div class="flex flex-col h-full py-6 items-center gap-4 transition-colors">
    <div class="flex flex-col items-center gap-4 w-full flex-1">
        @php
            $isAdmin = auth()->user()->isSuperAdmin();
            $isTenant = isset($activeTenant);
            
            // 1. Admin Core Menus (Always visible for Admin)
            $adminNavs = [
                ['href' => \App\Support\OwnerUrl::path(request(), 'users'), 'icon' => 'bi-people-fill', 'label' => __('Users'), 'active' => request()->is('admin/users*')],
                ['href' => \App\Support\OwnerUrl::path(request(), 'folders'), 'icon' => 'bi-folder2-open', 'label' => __('Folders'), 'active' => request()->is('admin/folders*')],
                ['href' => route('admin.settings'), 'icon' => 'bi-cpu-fill', 'label' => __('AI Settings'), 'active' => request()->routeIs('admin.settings')],
            ];

            // 2. Workspace Menus (Visible for everyone when in a folder)
            $isOwner = $isTenant && ((int)($activeTenant->user_id ?? 0) === (int)auth()->id() || $isAdmin);
            
            $workspaceNavs = [
                ['href' => \App\Support\WorkspaceUrl::current(request(), 'slips'), 'icon' => 'bi-qr-code-scan', 'label' => __('Inbox'), 'active' => request()->is('workspace/slips*')],
            ];


            // 3. Central Hub (Visible for everyone when NOT in Admin mode)
            $hubNavs = [
                ['href' => route('dashboard'), 'icon' => 'bi-grid-fill', 'label' => __('Hub'), 'active' => request()->routeIs('dashboard')],
            ];
        @endphp

        {{-- Show Admin Section if User is Admin --}}
        @if($isAdmin)
            @foreach($adminNavs as $nav)
                <div class="relative flex items-center justify-center w-full group">
                    <div class="absolute left-0 w-1 bg-rose-500 rounded-r-full transition-all duration-300 {{ $nav['active'] ? 'h-10 opacity-100' : 'h-0 opacity-0 group-hover:h-5 group-hover:opacity-50' }}"></div>
                    <a href="{{ $nav['href'] }}" class="w-12 h-12 flex items-center justify-center transition-all duration-200 rounded-[24px] {{ $nav['active'] ? 'bg-rose-500 text-white rounded-[16px] shadow-lg shadow-rose-500/20' : 'text-[#80848e] hover:bg-white dark:hover:bg-white/5 hover:text-rose-500 hover:rounded-[16px]' }}" title="{{ $nav['label'] }}">
                        <i class="bi {{ $nav['icon'] }} text-xl"></i>
                    </a>
                </div>
            @endforeach
            <div class="w-8 h-[2px] bg-[#d5d6d9] dark:bg-[#2b2d31] my-2 transition-colors"></div>
        @endif

        {{-- Show Workspace Section if inside a Folder --}}
        @if($isTenant)
            @foreach($workspaceNavs as $nav)
                <div class="relative flex items-center justify-center w-full group">
                    <div class="absolute left-0 w-1 bg-discord-green rounded-r-full transition-all duration-300 {{ $nav['active'] ? 'h-10 opacity-100' : 'h-0 opacity-0 group-hover:h-5 group-hover:opacity-50' }}"></div>
                    <a href="{{ $nav['href'] }}" class="w-12 h-12 flex items-center justify-center transition-all duration-200 rounded-[24px] {{ $nav['active'] ? 'bg-discord-green text-white rounded-[16px] shadow-lg shadow-green-500/20' : 'text-[#80848e] hover:bg-white dark:hover:bg-white/5 hover:text-discord-green hover:rounded-[16px]' }}" title="{{ $nav['label'] }}">
                        <i class="bi {{ $nav['icon'] }} text-xl"></i>   
                    </a>
                </div>
            @endforeach
        @endif

        {{-- Show Hub/Wallet for regular users or Admin when not in Admin-specific pages and not in tenant mode --}}
        @if(!$isTenant && (!request()->is('admin*') || !$isAdmin))
            @foreach($hubNavs as $nav)
                <div class="relative flex items-center justify-center w-full group">
                    <div class="absolute left-0 w-1 bg-discord-green rounded-r-full transition-all duration-300 {{ $nav['active'] ? 'h-10 opacity-100' : 'h-0 opacity-0 group-hover:h-5 group-hover:opacity-50' }}"></div>
                    <a href="{{ $nav['href'] }}" class="w-12 h-12 flex items-center justify-center transition-all duration-200 rounded-[24px] {{ $nav['active'] ? 'bg-discord-green text-white rounded-[16px] shadow-lg shadow-green-500/20' : 'text-[#80848e] hover:bg-white dark:hover:bg-white/5 hover:text-discord-green hover:rounded-[16px]' }}" title="{{ $nav['label'] }}">
                        <i class="bi {{ $nav['icon'] }} text-xl"></i>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
</div>
