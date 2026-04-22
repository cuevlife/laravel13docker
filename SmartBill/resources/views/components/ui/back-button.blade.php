@props(['href', 'title' => 'Back'])

<a href="{{ $href }}" 
   {{ $attributes->merge(['class' => 'flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-500/10 dark:bg-white/5 text-indigo-500 hover:bg-indigo-500 hover:text-white border border-transparent transition-all shadow-sm group/back']) }} 
   title="{{ __($title) }}">
    <i class="bi bi-arrow-left text-xl transition-colors group-hover/back:text-white"></i>
</a>