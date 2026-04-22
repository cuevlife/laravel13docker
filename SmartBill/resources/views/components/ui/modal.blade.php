@props([
    'name',
    'show' => false,
    'maxWidth' => 'xl',
    'backdropClose' => true
])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth] ?? 'max-w-xl';
@endphp

<div x-data="{ 
        show: @js($show),
        close() { this.show = false; $dispatch('close-modal', { name: '{{ $name }}' }) }
     }"
     x-show="show"
     x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') show = true"
     x-on:close-modal.window="if ($event.detail.name === '{{ $name }}') show = false"
     x-on:keydown.escape.window="close()"
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-cloak>
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-white/60 backdrop-blur-xl dark:bg-black/60 transition-opacity" 
         @if($backdropClose) @click="close()" @endif></div>
    
    {{-- Modal Content --}}
    <div class="relative z-10 w-full {{ $maxWidthClass }} overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-[#2b2d31] animate-in zoom-in-95 duration-200 border border-black/[0.03] dark:border-white/[0.03]">
        {{ $slot }}
    </div>
</div>
