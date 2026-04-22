@props(['disabled' => false, 'error' => false])

<input {{ $disabled ? 'disabled' : '' }} 
    {!! $attributes->merge(['class' => 'h-11 w-full rounded-xl border ' . 
    ($error ? 'border-rose-500 ring-rose-500/10' : 'border-black/[0.05] dark:border-white/10 focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10') . 
    ' bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white outline-none transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed placeholder-[#80848e]']) !!}>