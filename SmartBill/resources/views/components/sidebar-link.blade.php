@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2.5 text-[10px] font-black bg-emerald-500/10 dark:bg-discord-green/10 text-emerald-600 dark:text-discord-green rounded-xl transition-all duration-300 uppercase tracking-widest shadow-sm shadow-emerald-500/5'
            : 'flex items-center px-4 py-2.5 text-[10px] font-bold text-slate-500 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-slate-200 rounded-xl transition-all duration-300 uppercase tracking-widest';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center min-w-[1.25rem] justify-center">
        <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ ($active ?? false) ? 'text-emerald-600 dark:text-discord-green' : 'text-slate-400 dark:text-slate-500' }}"></i>
    </div>
    <span x-show="!sidebarCollapsed" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-x-[-10px]"
          x-transition:enter-end="opacity-100 translate-x-0"
          class="ml-3 whitespace-nowrap overflow-hidden">
        {{ $slot }}
    </span>
</a>
