@props([
    'id' => 'date-picker',
    'placeholder' => __('Select Date...'),
    'model' => null,
])

<div class="relative w-full">
    <i class="bi bi-calendar absolute left-3 top-1/2 z-10 -translate-y-1/2 text-sm leading-none text-[#80848e]"></i>
    <input type="text" id="{{ $id }}" placeholder="{{ $placeholder }}" 
           {{ $attributes->merge(['class' => 'h-10 w-full rounded-xl border border-black/5 bg-white pl-10 pr-3 text-[11px] font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all focus:border-discord-green/30']) }}>
</div>

@once
@push('scripts')
<style>
    .flatpickr-calendar.flatpickr-buddhist-year .flatpickr-current-month .numInput.cur-year {
        min-width: 72px;
    }
    /* Premium Green Flatpickr Theme */
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange,
    .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange {
        background: #23a559 !important;
        border-color: #23a559 !important;
        color: #fff !important;
    }
    .flatpickr-day.inRange, 
    .flatpickr-day.prevMonthDay.inRange, 
    .flatpickr-day.nextMonthDay.inRange,
    .flatpickr-day.today.inRange,
    .flatpickr-day.inRange:hover {
        background: #f2f3f5 !important;
        border-color: transparent !important;
        box-shadow: none !important;
        color: #313338 !important;
    }
    .dark .flatpickr-day.inRange,
    .dark .flatpickr-day.inRange:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        box-shadow: none !important;
        color: #fff !important;
    }
    .flatpickr-day.today { border-color: #23a559 !important; }
    .flatpickr-day.today:hover { background: #23a559 !important; color: #fff !important; }
    .flatpickr-day.inRange:hover { background: rgba(35, 165, 89, 0.2) !important; }
    
    .dark .flatpickr-calendar {
        background: #2b2d31 !important;
        border-color: rgba(255,255,255,0.05) !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3) !important;
    }
    .dark .flatpickr-day { color: #b5bac1 !important; }
    .dark .flatpickr-current-month, .dark .flatpickr-weekday { color: #fff !important; fill: #fff !important; }
</style>
@endpush
@endonce
