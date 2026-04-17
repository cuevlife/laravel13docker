@php
    $isProfile = request()->routeIs('profile.edit');
    $profileExitUrl = isset($activeTenant)
        ? \App\Support\WorkspaceUrl::current(request(), 'dashboard')
        : route('dashboard');
    $title = config('app.name', 'smartbill');
    if(request()->routeIs('admin.slip.index') || request()->routeIs('workspace.slip.index')) $title = 'Slip';
    if(request()->routeIs('admin.settings')) $title = 'AI Settings';
    if(request()->routeIs('admin.stores.index')) $title = 'Brands';
    if(request()->routeIs('billing')) $title = 'Tokens';
    if(request()->routeIs('admin.dashboard') || request()->routeIs('owner.dashboard')) $title = 'SaaS';
    if(request()->routeIs('admin.projects.*') || request()->routeIs('owner.projects.*')) $title = 'Projects';
    if(request()->routeIs('admin.users.*') || request()->routeIs('owner.users.*')) $title = 'Users';
    if(request()->routeIs('admin.topups.*') || request()->routeIs('admin.topups') || request()->routeIs('owner.topups.*') || request()->routeIs('owner.topups')) $title = 'Topups';
    if($isProfile) {
        $title = 'Settings';
    }

    $showMobileScan = isset($activeTenant) && (
        request()->routeIs('admin.slip.index')
        || request()->routeIs('workspace.slip.index')
        || request()->routeIs('tenant.dashboard')
        || request()->routeIs('workspace.dashboard')
    );
@endphp
<div class="lg:hidden sticky top-0 z-40 bg-[#fafafa]/90 dark:bg-[#1e1f22]/90 backdrop-blur-md border-b border-[#e3e5e8] dark:border-[#313338] px-4 py-3 flex items-center justify-between w-full shadow-sm">
    <div class="flex items-center gap-3">
        @if($isProfile)
            <a href="{{ $profileExitUrl }}" class="flex items-center justify-center w-8 h-8 rounded-full text-[#5c5e66] dark:text-[#b5bac1] hover:bg-[#e3e5e8] dark:hover:bg-[#2b2d31] transition-colors focus:ring-2 focus:ring-discord-green focus:outline-none">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
        @else
            <div class="w-7 h-7 rounded-full {{ request()->is('admin*') ? 'bg-rose-500' : 'bg-discord-green' }} flex items-center justify-center text-white font-black text-[10px] shadow-sm shadow-discord-green/20">
                SB
            </div>
        @endif
        <h1 class="text-xs font-black text-[#1e1f22] dark:text-white uppercase tracking-widest mt-0.5">{{ $title }}</h1>
    </div>

    <div class="flex items-center gap-2">
        <div class="flex items-center gap-2">
            @include('layouts.parts.header-token')
        </div>

        <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-colors focus:outline-none ml-1">
            <i class="bi bi-moon-stars-fill text-sm" x-show="!darkMode" x-cloak></i>
            <i class="bi bi-sun-fill text-sm" x-show="darkMode" x-cloak></i>
        </button>
        @if(!$isProfile)
            <a href="{{ route('lang.switch', app()->getLocale() == 'th' ? 'en' : 'th') }}" class="flex items-center justify-center w-7 h-7 rounded-full border border-[#e3e5e8] dark:border-[#313338] bg-white dark:bg-[#2b2d31] text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-all shadow-sm">
                {{ app()->getLocale() == 'th' ? 'TH' : 'EN' }}
            </a>
        @else
            <div class="w-7"></div>
        @endif
    </div>
</div>
