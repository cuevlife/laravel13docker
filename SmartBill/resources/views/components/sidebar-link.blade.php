@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2.5 text-xs font-bold bg-[#313338] text-emerald-400 rounded-md transition-all duration-200'
            : 'flex items-center px-4 py-2.5 text-xs font-medium text-slate-400 hover:bg-[#35373c] hover:text-slate-200 rounded-md transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center min-w-[1.25rem] justify-center">
        <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ ($active ?? false) ? 'text-emerald-400' : 'text-slate-500' }}"></i>
    </div>
    <span x-show="!sidebarCollapsed" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-x-[-10px]"
          x-transition:enter-end="opacity-100 translate-x-0"
          class="ml-3 whitespace-nowrap overflow-hidden">
        {{ $slot }}
    </span>
</a>
