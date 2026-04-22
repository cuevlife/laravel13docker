@props([
    'links' => [],
    'total' => 0,
    'count' => 0,
    'label' => 'Items',
    'perPage' => 20,
])

<div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-black/[0.04] pt-6 dark:border-white/[0.04]">
    <!-- Left: Summary & PerPage -->
    <div class="flex items-center gap-4">
        <x-ui.dropdown 
            width="w-24" 
            :model="$attributes->get('x-model')" 
            position="top"
            :options="[
                ['v' => 20, 'l' => '20 / ' . __('Page')],
                ['v' => 50, 'l' => '50 / ' . __('Page')],
                ['v' => 100, 'l' => '100 / ' . __('Page')]
            ]" 
            @change="$dispatch('per-page-change', $event.detail)"
        />

        <div class="text-[11px] font-bold text-[#80848e]">
            {{ __('Showing') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="{{ $count }}"></span> 
            {{ __('of') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="{{ $total }}"></span> 
            {{ __($label) }}
        </div>
    </div>

    <!-- Right: Pagination Buttons -->
    <div class="flex items-center gap-2">
        <template x-for="link in {{ $links }}" :key="link.label">
            <button @click="$dispatch('page-change', link.url || link.page)" 
                    :disabled="(!link.url && !link.page) || link.active"
                    class="h-8 min-w-[32px] rounded-xl px-3 text-[10px] font-black uppercase transition-all border border-transparent"
                    :class="{
                        'bg-discord-green text-white shadow-lg shadow-green-500/20': link.active,
                        'bg-[#f8fafb] text-[#5c5e66] hover:bg-black/5 dark:bg-white/5 dark:text-[#949ba4] dark:hover:bg-white/10': !link.active && (link.url || link.page),
                        'opacity-20 cursor-not-allowed dark:text-[#4f545c]': !link.url && !link.page
                    }"
                    x-html="formatPaginationLabel(link.label)">
            </button>
        </template>
    </div>
</div>
