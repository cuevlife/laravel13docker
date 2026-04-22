@props([
    'options' => [], // Array of objects [{v: 'value', l: 'label'}]
    'model' => null,  // Parent variable name for x-model
    'placeholder' => __('Select Option'),
    'width' => 'w-full',
    'position' => 'bottom', // 'bottom' or 'top'
])

<div class="relative {{ $width }}" x-data="{ 
    open: false,
    options: {{ json_encode($options) }},
    get currentLabel() {
        const found = this.options.find(o => o.v == {{ $model }});
        return found ? found.l : '{{ $placeholder }}';
    }
}">
    <button @click="open = !open" @click.away="open = false" 
            class="h-10 w-full flex items-center justify-between rounded-xl border border-black/5 bg-white pl-4 pr-3 text-[11px] font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all hover:border-discord-green/30">
        <span x-text="currentLabel"></span>
        <i class="bi bi-chevron-down text-[10px] text-[#80848e] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
    </button>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100"
         class="absolute z-[60] {{ $position === 'top' ? 'bottom-full mb-2' : 'mt-2' }} w-full min-w-[180px] rounded-xl bg-white dark:bg-[#2b2d31] p-1.5 shadow-xl border border-black/5 dark:border-white/5" 
         x-cloak>
        <template x-for="opt in options" :key="opt.v">
            <button @click="{{ $model }} = opt.v; open = false; $dispatch('change', opt.v)" 
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-[11px] font-bold transition-all hover:bg-discord-green/10 hover:text-discord-green dark:hover:bg-white/5"
                    :class="{{ $model }} == opt.v ? 'text-discord-green bg-discord-green/5' : 'text-[#5c5e66] dark:text-[#b5bac1]'">
                <span x-text="opt.l"></span>
                <i x-show="{{ $model }} == opt.v" class="bi bi-check2"></i>
            </button>
        </template>
    </div>
</div>
