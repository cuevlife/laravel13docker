@props(['variant' => 'default'])
<div {{ $attributes->merge(['class' => 'bg-white dark:bg-[#2b2d31] rounded-xl shadow-sm border border-black/[0.03] dark:border-white/[0.03] overflow-hidden']) }}>
    {{ $slot }}
</div>