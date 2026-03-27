<x-app-layout>
    <div class="space-y-8 animate-in fade-in duration-700 pb-20">
        
        <!-- Header -->
        <div class="premium-card p-6 md:p-8 border-l-4 border-l-discord-green">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-tight leading-none">Dashboard</h2>
                    <p class="text-[10px] font-bold text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-[0.2em] mt-3">Node Analytics & Operations</p>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-discord-green/10 rounded-[12px] border border-discord-green/20">
                    <span class="w-2 h-2 rounded-full bg-discord-green animate-pulse shadow-[0_0_8px_rgba(35,165,89,0.8)]"></span>
                    <span class="text-[9px] font-black uppercase text-discord-green tracking-widest">Gateway Active</span>
                </div>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $metrics = [
                    ['label' => 'Slips', 'value' => $stats['slips_count'], 'route' => 'admin.slip.index', 'icon' => 'layers', 'color' => 'discord-green'],
                    ['label' => 'Profiles', 'value' => $stats['templates_count'], 'route' => 'admin.templates.index', 'icon' => 'settings-2', 'color' => 'discord-green'],
                    ['label' => 'Stores', 'value' => $stats['stores_count'], 'route' => 'admin.stores.index', 'icon' => 'store', 'color' => 'discord-green'],
                    ['label' => 'Network', 'value' => auth()->user()->isAdmin() ? $stats['users_count'] : 'SECURE', 'route' => '#', 'icon' => 'shield-check', 'color' => '#80848e'],
                ];
            @endphp

            @foreach($metrics as $m)
                <a href="{{ $m['route'] !== '#' ? route($m['route']) : '#' }}" class="premium-card p-6 md:p-8 group relative overflow-hidden transition-all duration-300 hover:-translate-y-1">
                    <div class="relative z-10 space-y-4">
                        <div class="w-12 h-12 rounded-[16px] flex items-center justify-center border group-hover:scale-110 transition-transform shadow-sm"
                             style="background-color: var(--tw-colors-{{ $m['color'] }}, {{ $m['color'] }}15); color: var(--tw-colors-{{ $m['color'] }}, {{ $m['color'] }}); border-color: var(--tw-colors-{{ $m['color'] }}, {{ $m['color'] }}30);"
                             class="{{ $m['color'] === 'discord-green' ? 'bg-discord-green/10 text-discord-green border-discord-green/20' : 'bg-[#f2f3f5] dark:bg-[#1e1f22] text-[#80848e] border-[#e3e5e8] dark:border-[#313338]' }}">
                            <i data-lucide="{{ $m['icon'] }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-[#1e1f22] dark:text-white tracking-tighter">{{ is_numeric($m['value']) ? number_format($m['value']) : $m['value'] }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#5c5e66] dark:text-[#b5bac1] mt-1">{{ $m['label'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Action Hub -->
        <div class="premium-card p-8 md:p-10">
            <h3 class="text-[10px] font-black uppercase tracking-[0.4em] text-[#5c5e66] dark:text-[#b5bac1] mb-8 flex items-center gap-3">
                <i data-lucide="zap" class="w-4 h-4 text-discord-green"></i> Critical Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('admin.slip.index') }}" class="flex items-center gap-4 p-6 bg-[#f2f3f5] dark:bg-[#1e1f22] hover:bg-discord-green hover:text-white dark:hover:bg-discord-green rounded-[24px] transition-all group border border-[#e3e5e8] dark:border-transparent">
                    <div class="w-10 h-10 rounded-[14px] bg-white dark:bg-[#2b2d31] flex items-center justify-center text-discord-green shadow-sm group-hover:bg-white/20 group-hover:text-white transition-all">
                        <i data-lucide="scan" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-[#f2f3f5] group-hover:text-white">Process Slip</span>
                </a>
                <a href="{{ route('admin.templates.index') }}" class="flex items-center gap-4 p-6 bg-[#f2f3f5] dark:bg-[#1e1f22] hover:bg-[#1e1f22] dark:hover:bg-[#f2f3f5] hover:text-white dark:hover:text-[#1e1f22] rounded-[24px] transition-all group border border-[#e3e5e8] dark:border-transparent">
                    <div class="w-10 h-10 rounded-[14px] bg-white dark:bg-[#2b2d31] flex items-center justify-center text-[#5c5e66] dark:text-[#b5bac1] group-hover:text-[#1e1f22] dark:group-hover:text-[#f2f3f5] shadow-sm group-hover:bg-white/20 dark:group-hover:bg-black/10 transition-all">
                        <i data-lucide="settings-2" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-[#f2f3f5] group-hover:text-white dark:group-hover:text-[#1e1f22]">Manage Rules</span>
                </a>
                <a href="{{ route('admin.slip.export') }}" class="flex items-center gap-4 p-6 bg-[#f2f3f5] dark:bg-[#1e1f22] hover:bg-[#1f8b4c] hover:text-white rounded-[24px] transition-all group border border-[#e3e5e8] dark:border-transparent">
                    <div class="w-10 h-10 rounded-[14px] bg-white dark:bg-[#2b2d31] flex items-center justify-center text-[#1f8b4c] shadow-sm group-hover:bg-white/20 group-hover:text-white transition-all">
                        <i data-lucide="download" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-[#f2f3f5] group-hover:text-white">Export Data</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
