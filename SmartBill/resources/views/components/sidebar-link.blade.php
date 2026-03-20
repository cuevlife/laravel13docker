@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2 text-[10px] font-black bg-slate-100 dark:bg-discord-dark text-indigo-600 dark:text-emerald-400 rounded transition-all duration-200 uppercase tracking-widest'
            : 'flex items-center px-4 py-2 text-[10px] font-bold text-slate-500 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-[#35373c] hover:text-slate-900 dark:hover:text-slate-200 rounded transition-all duration-200 uppercase tracking-widest';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center min-w-[1.25rem] justify-center">
        <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ ($active ?? false) ? 'text-indigo-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}"></i>
    </div>
    <span x-show="!sidebarCollapsed" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-x-[-10px]"
          x-transition:enter-end="opacity-100 translate-x-0"
          class="ml-3 whitespace-nowrap overflow-hidden">
        {{ $slot }}
    </span>
</a>
