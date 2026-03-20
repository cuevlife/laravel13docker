@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2.5 text-xs font-black bg-slate-100 dark:bg-discord-dark text-indigo-600 dark:text-emerald-400 rounded-xl transition-all duration-300 shadow-sm dark:shadow-none'
            : 'flex items-center px-4 py-2.5 text-xs font-bold text-slate-500 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-slate-300 rounded-xl transition-all duration-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center min-w-[1.25rem] justify-center">
        <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ ($active ?? false) ? 'stroke-[2.5px]' : 'stroke-[2px]' }}"></i>
    </div>
    <span x-show="!sidebarCollapsed" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-x-[-10px]"
          x-transition:enter-end="opacity-100 translate-x-0"
          class="ml-3 whitespace-nowrap overflow-hidden uppercase tracking-widest text-[10px]">
        {{ $slot }}
    </span>
</a>
