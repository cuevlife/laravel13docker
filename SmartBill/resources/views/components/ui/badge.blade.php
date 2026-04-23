@props(['variant' => 'success'])
@php
    $classes = [
        'success' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
        'danger'  => 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
        'warning' => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
        'primary' => 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20',
        'ghost'   => 'bg-slate-50 text-slate-500 border-slate-100 dark:bg-white/5 dark:text-slate-400 dark:border-white/10',
    ][$variant] ?? $classes['success'];
@endphp

<span {{ $attributes->merge(['class' => "px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest border shadow-sm $classes"]) }}>
    {{ $slot }}
</span>