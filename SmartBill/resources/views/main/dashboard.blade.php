<x-app-layout>
    <div class="space-y-8 animate-in fade-in duration-700 pb-20">
        <!-- KPI Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="premium-card p-5 border-l-4 border-l-rose-500">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Your Token Balance</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($billingSummary['tokens']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Ready for new scans</div>
            </div>
            <div class="premium-card p-5 border-l-4 border-l-emerald-500">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Workspace Access</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $billingSummary['workspaceAccess'] }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Permissions inside this workspace</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Store Extraction Profiles</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($billingSummary['profiles']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Local extraction rulesets</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Store Processed Slips</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($billingSummary['slips']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Historical registry</div>
            </div>
        </div>

        <div class="premium-card overflow-hidden transition-all shadow-lg rounded-[24px]">
            <div class="p-6 md:p-8 bg-[#f2f3f5] dark:bg-[#1e1f22] flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#e3e5e8] dark:border-[#313338]">
                <div>
                    <h3 class="text-xl font-black text-[#1e1f22] dark:text-white tracking-tight uppercase">Extraction Profiles</h3>
                    <p class="text-xs font-bold text-[#80848e] uppercase tracking-wider mt-1">Manage AI Rules for {{ $tenant->name }}</p>
                </div>
                <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'templates') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[16px] transition-all shadow-md active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i> Create Profile
                </a>
            </div>

            <div class="p-6 md:p-8 bg-white dark:bg-[#2b2d31]">
                @if($tenant->templates->isEmpty())
                    <div class="text-center py-12 bg-[#f2f3f5] dark:bg-[#1e1f22] rounded-[24px] border-2 border-dashed border-[#e3e5e8] dark:border-[#313338]">
                        <div class="w-16 h-16 mx-auto bg-white dark:bg-[#2b2d31] rounded-2xl flex items-center justify-center mb-4 shadow-sm border border-gray-100 dark:border-transparent">
                            <i data-lucide="bot" class="w-8 h-8 text-slate-400"></i>
                        </div>
                        <h4 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-tight mb-2">No Profiles Set Up Yet</h4>
                        <p class="text-xs font-bold text-[#80848e] uppercase tracking-wider mb-6 max-w-sm mx-auto">Create an intelligence profile to teach the system how to extract data from slips specific to this store.</p>
                        <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'templates') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-500 hover:bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                            <i data-lucide="settings-2" class="w-4 h-4"></i> Setup First Profile
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($tenant->templates as $template)
                            <div class="p-5 bg-white dark:bg-[#1e1f22] border border-[#e3e5e8] dark:border-[#313338] rounded-[20px] hover:border-discord-green/30 hover:shadow-md transition-all group relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-discord-green/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-[14px] bg-[#f2f3f5] dark:bg-[#2b2d31] flex items-center justify-center text-indigo-500 shadow-inner shrink-0">
                                            <i data-lucide="cpu" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'templates/' . $template->id . '/edit') }}" class="text-lg font-black text-[#1e1f22] dark:text-white hover:text-indigo-500 transition-colors leading-tight">
                                                {{ $template->name }}
                                            </a>
                                            <div class="text-[10px] font-bold text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest mt-1">
                                                {{ count($template->ai_fields ?? []) }} Data Points
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 pt-4 border-t border-[#e3e5e8] dark:border-[#313338]">
                                        <div class="flex-1 flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-[#2b2d31] text-xs font-black text-slate-700 dark:text-slate-300">
                                {{ $template->slips()->count() }}
                            </span>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-[#80848e]">Extracted Slips</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips') }}?template={{ $template->id }}" class="flex items-center justify-center px-4 py-2 bg-[#f2f3f5] hover:bg-[#e3e5e8] dark:bg-[#2b2d31] dark:hover:bg-[#313338] text-[#5c5e66] dark:text-[#b5bac1] text-[10px] font-black uppercase tracking-widest rounded-[12px] transition-all">
                                                View Data
                                            </a>
                                            <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips') }}" title="Process New Slip" class="flex items-center justify-center w-9 h-9 bg-discord-green hover:bg-[#1f8b4c] text-white rounded-[12px] transition-all shadow-sm active:scale-95">
                                                <i data-lucide="scan-line" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
