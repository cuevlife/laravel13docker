@props(['icon' => 'bi-search', 'title' => 'No records found', 'subtitle' => 'Search using other terms or check back later'])
<div {{ $attributes->merge(['class' => 'py-24 text-center']) }}>
    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-[#f8fafb] border border-black/[0.02] shadow-sm dark:bg-[#1e1f22] dark:border-white/5 text-3xl text-slate-400">
        <i class="bi {{ $icon }}"></i>
    </div>
    <h3 class="text-[13px] font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __($title) }}</h3>
    <p class="mt-2 text-[10px] font-bold text-[#80848e] uppercase tracking-widest">{{ __($subtitle) }}</p>
</div>