<div class="flex flex-col h-full py-6 items-center gap-4 bg-white dark:bg-[#1e1f22] w-20 transition-colors">
    <!-- Navigation Nodes -->
    <div class="flex flex-col items-center gap-4 w-full flex-1">
        
        @php
            $navs = [
                ['route' => 'admin.slip.index', 'icon' => 'layers', 'label' => 'Registry', 'active' => request()->routeIs('admin.slip.*') || request()->routeIs('dashboard')],
                ['route' => 'admin.stores.index', 'icon' => 'store', 'label' => 'Stores', 'active' => request()->routeIs('admin.stores.*')],
                ['route' => 'admin.templates.index', 'icon' => 'file-json', 'label' => 'Profiles', 'active' => request()->routeIs('admin.templates.*')],
            ];
        @endphp

        @foreach($navs as $nav)
            <div class="relative flex items-center justify-center w-full group">
                <!-- Discord-style Indicator -->
                <div class="absolute left-0 w-1 bg-discord-green rounded-r-full transition-all duration-300 {{ $nav['active'] ? 'h-10 opacity-100' : 'h-0 opacity-0 group-hover:h-5 group-hover:opacity-50' }}"></div>
                
                <!-- Icon Button -->
                <a href="{{ route($nav['route']) }}" 
                   class="w-12 h-12 flex items-center justify-center transition-all duration-200 rounded-[24px] {{ $nav['active'] ? 'bg-discord-green text-white rounded-[16px] shadow-lg shadow-green-500/20' : 'text-[#80848e] hover:bg-white dark:hover:bg-white/5 hover:text-discord-green hover:rounded-[16px]' }}"
                   title="{{ $nav['label'] }}">
                    <i data-lucide="{{ $nav['icon'] }}" class="w-6 h-6"></i>
                </a>
            </div>
        @endforeach

        @if(auth()->user()->isAdmin())
            <div class="w-8 h-[2px] bg-[#d5d6d9] dark:bg-[#2b2d31] my-2 transition-colors"></div>
            <div class="relative flex items-center justify-center w-full group">
                <div class="absolute left-0 w-1 bg-discord-green rounded-r-full transition-all duration-300 {{ request()->routeIs('admin.users') ? 'h-10 opacity-100' : 'h-0 opacity-0 group-hover:h-5 group-hover:opacity-50' }}"></div>
                <a href="{{ route('admin.users') }}" 
                   class="w-12 h-12 flex items-center justify-center transition-all duration-200 rounded-[24px] {{ request()->routeIs('admin.users') ? 'bg-discord-green text-white rounded-[16px] shadow-lg shadow-green-500/20' : 'text-[#80848e] hover:bg-white dark:hover:bg-white/5 hover:text-discord-green hover:rounded-[16px]' }}"
                   title="Users">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </a>
            </div>
        @endif
    </div>

    <div class="mt-auto">
        <button class="w-12 h-12 flex items-center justify-center text-[#80848e] hover:text-discord-green transition-colors group">
            <i data-lucide="help-circle" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
        </button>
    </div>
</div>
