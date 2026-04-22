@props(['title', 'subtitle' => null, 'icon' => null, 'variant' => 'primary'])
@php
    $iconBg = [
        'primary' => 'bg-indigo-500/10 text-indigo-500',
        'success' => 'bg-discord-green/10 text-discord-green',
        'danger' => 'bg-rose-500/10 text-rose-500',
        'warning' => 'bg-amber-500/10 text-amber-500',
    ][$variant] ?? 'bg-indigo-500/10 text-indigo-500';
@endphp
<div {{ $attributes->merge(['class' => 'flex flex-col md:flex-row md:items-center justify-between gap-6']) }}>
    <div class="flex items-center gap-4">
        {{-- ส่วน Prefix: ถ้ามี slot icon ให้ใช้ slot ถ้าไม่มีให้เช็คตัวแปร icon --}}
        @if(isset($icon_slot))
            {{ $icon_slot }}
        @elseif($icon)
            <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $iconBg }} text-2xl shadow-sm">
                <i class="bi {{ $icon }}"></i>
            </div>
        @endif
        
        <div>
            <h1 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest leading-none">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1.5">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    @if(isset($actions))
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>