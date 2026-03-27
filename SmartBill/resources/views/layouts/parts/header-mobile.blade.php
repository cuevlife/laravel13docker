@php
    $isProfile = request()->routeIs('profile.edit');
    $title = 'SmartBill';
    if(request()->routeIs('admin.slip.index')) $title = 'Slip Registry';
    if(request()->routeIs('admin.stores.index')) $title = 'Brands';
    if(request()->routeIs('admin.templates.index')) $title = 'Templates';
    if($isProfile) {
        $title = 'Settings';
    }
@endphp
<div class="lg:hidden sticky top-0 z-40 bg-[#fafafa]/90 dark:bg-[#1e1f22]/90 backdrop-blur-md border-b border-[#e3e5e8] dark:border-[#313338] px-4 py-3 flex items-center justify-between w-full shadow-sm">
    <div class="flex items-center gap-3">
        @if($isProfile)
            <a href="javascript:history.back()" class="flex items-center justify-center w-8 h-8 rounded-full text-[#5c5e66] dark:text-[#b5bac1] hover:bg-[#e3e5e8] dark:hover:bg-[#2b2d31] transition-colors focus:ring-2 focus:ring-discord-green focus:outline-none">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
        @else
            <div class="w-7 h-7 rounded-full bg-discord-green flex items-center justify-center text-white font-black text-[10px] shadow-sm shadow-discord-green/20">
                SB
            </div>
        @endif
        <h1 class="text-xs font-black text-[#1e1f22] dark:text-white uppercase tracking-widest mt-0.5">{{ $title }}</h1>
    </div>
    
    <div class="flex items-center gap-4">
        <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-colors focus:outline-none">
            <i data-lucide="moon" x-show="!darkMode" class="w-4 h-4"></i>
            <i data-lucide="sun" x-show="darkMode" class="w-4 h-4" x-cloak></i>
        </button>
        @if(!$isProfile)
            <a href="{{ route('lang.switch', app()->getLocale() == 'th' ? 'en' : 'th') }}" class="flex items-center justify-center w-7 h-7 rounded-full border border-[#e3e5e8] dark:border-[#313338] bg-white dark:bg-[#2b2d31] text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-all shadow-sm">
                {{ app()->getLocale() == 'th' ? 'TH' : 'EN' }}
            </a>
        @else
            <div class="w-7"></div><!-- Spacer for balanced alignment when back button is present -->
        @endif
    </div>
</div>
