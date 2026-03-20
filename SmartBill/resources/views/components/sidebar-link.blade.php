@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2 text-[10px] font-black bg-[#313338] text-discord-green rounded transition-all duration-200 uppercase tracking-widest'
            : 'flex items-center px-4 py-2 text-[10px] font-bold text-slate-400 hover:bg-[#35373c] hover:text-slate-200 rounded transition-all duration-200 uppercase tracking-widest';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span x-show="!sidebarCollapsed" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-x-[-10px]"
          x-transition:enter-end="opacity-100 translate-x-0"
          class="whitespace-nowrap overflow-hidden">
        {{ $slot }}
    </span>
    <!-- เมื่อหุบ Sidebar จะใช้ตัวอักษรตัวแรกแทนไอคอน -->
    <span x-show="sidebarCollapsed" class="w-full text-center">
        {{ substr($slot, 0, 1) }}
    </span>
</a>
