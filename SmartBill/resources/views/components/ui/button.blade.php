@props([
    'variant' => 'success', // success, danger, primary, ghost
    'size' => 'md',      // sm, md, lg
    'icon' => null,
    'href' => null,
])

@php
    $isLink = $href || $attributes->has('href') || $attributes->has(':href') || $attributes->has('x-bind:href');
    
    $baseClasses = "inline-flex items-center justify-center gap-2 rounded-xl font-black uppercase tracking-widest transition-all duration-200 shadow-sm border border-transparent";
    
    $variants = [
        'success' => "bg-discord-green/10 dark:bg-white/5 text-discord-green hover:bg-discord-green hover:text-white dark:hover:bg-discord-green dark:hover:text-white",
        'danger'  => "bg-rose-500/10 dark:bg-white/5 text-rose-500 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-500 dark:hover:text-white",
        'primary' => "bg-indigo-500/10 dark:bg-white/5 text-indigo-500 hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-500 dark:hover:text-white",
        'ghost'   => "bg-slate-500/5 dark:bg-white/5 text-slate-500 dark:text-slate-400 hover:bg-slate-500 hover:text-white dark:hover:bg-slate-400 dark:hover:text-white",
    ];

    $sizes = [
        'xs' => "px-2 py-1 text-[7px]",
        'sm' => "px-3 py-1.5 text-[8px]",
        'md' => "px-5 py-2.5 text-[9px]",
        'lg' => "px-8 py-3.5 text-[11px]",
    ];

    $disabledClasses = "opacity-40 grayscale cursor-not-allowed pointer-events-none active:scale-100";
    $activeClasses = "active:scale-95";

    $classes = $baseClasses . " " . ($variants[$variant] ?? $variants['success']) . " " . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($isLink)
    <a @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes . ' ' . $activeClasses]) }}>
        @if($icon) <i class="{{ $icon }} leading-none"></i> @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}
            :class="{ '{{ $disabledClasses }}': {{ $attributes->get(':disabled') ?? 'false' }}, '{{ $activeClasses }}': !({{ $attributes->get(':disabled') ?? 'false' }}) }"
            @if($attributes->has(':disabled')) :disabled="{{ $attributes->get(':disabled') }}" @endif>
        @if($icon) <i class="{{ $icon }} leading-none"></i> @endif
        {{ $slot }}
    </button>
@endif
