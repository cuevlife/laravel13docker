@props(['label', 'value', 'icon' => null, 'variant' => 'primary'])
@php
    $colors = [
        'primary' => 'text-indigo-500 bg-indigo-500/5',
        'success' => 'text-emerald-500 bg-emerald-500/5',
        'danger'  => 'text-rose-500 bg-rose-500/5',
        'warning' => 'text-amber-500 bg-amber-500/5',
    ][$variant] ?? $colors['primary'];
@endphp
<div {{ $attributes->merge(['class' => 'rounded-xl bg-white p-5 shadow-sm border border-black/[0.03] dark:bg-[#2b2d31] dark:border-white/[0.03] flex items-center justify-between']) }}>
    <div>
        <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e]">{{ __($label) }}</p>
        <h3 class="mt-1 text-2xl font-black text-[#1e1f22] dark:text-white leading-none">{{ $value }}</h3>
    </div>
    @if($icon)
        <div class="h-12 w-12 rounded-xl flex items-center justify-center text-2xl {{ $colors }}">
            <i class="bi {{ $icon }}"></i>
        </div>
    @endif
</div>