@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 text-sm font-bold bg-indigo-600/10 text-indigo-400 border-r-4 border-indigo-500 rounded-lg transition duration-150'
            : 'flex items-center px-4 py-3 text-sm font-medium text-slate-400 hover:bg-white/5 hover:text-white rounded-lg transition duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <i class="{{ $icon }} mr-3 w-5 text-center {{ ($active ?? false) ? 'text-indigo-400' : 'text-slate-500' }}"></i>
    {{ $slot }}
</a>
