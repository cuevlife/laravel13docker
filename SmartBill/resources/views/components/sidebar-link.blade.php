@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 text-sm font-bold bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 border-r-4 border-indigo-500 rounded-lg transition-all duration-300'
            : 'flex items-center px-4 py-3 text-sm font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-white rounded-lg transition-all duration-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center min-w-[1.25rem] justify-center">
        <i class="{{ $icon }} text-lg {{ ($active ?? false) ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-indigo-500' }}"></i>
    </div>
    <span x-show="!sidebarCollapsed" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-x-[-10px]"
          x-transition:enter-end="opacity-100 translate-x-0"
          class="ml-4 whitespace-nowrap overflow-hidden">
        {{ $slot }}
    </span>
</a>
