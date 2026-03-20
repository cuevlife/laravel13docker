<x-app-layout>
    <div class="space-y-10">
        <!-- Hero Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Intelligence Dashboard</h1>
                <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 uppercase tracking-widest font-medium">Neural Status: Optimal</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="flex h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                <span class="text-xs font-bold text-emerald-600/80 uppercase tracking-tighter">System Live</span>
            </div>
        </div>

        <!-- Stats Grid (Minimalist Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat 1 -->
            <div class="bg-white dark:bg-[#0b0f1a] p-6 rounded-2xl border border-slate-200/60 dark:border-white/5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-slate-50 dark:bg-white/5 rounded-lg text-slate-400">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 rounded-full uppercase">Global Entities</span>
                </div>
                <div class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['users_count'] }}</div>
                <p class="text-xs text-slate-400 mt-1">Registered authentication links</p>
            </div>

            <!-- Stat 2 -->
            <div class="bg-white dark:bg-[#0b0f1a] p-6 rounded-2xl border border-slate-200/60 dark:border-white/5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-slate-50 dark:bg-white/5 rounded-lg text-slate-400">
                        <i data-lucide="store" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded-full uppercase">Neural Nodes</span>
                </div>
                <div class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['merchants_count'] }}</div>
                <p class="text-xs text-slate-400 mt-1">Active merchant mapping protocols</p>
            </div>

            <!-- Stat 3 -->
            <div class="bg-white dark:bg-[#0b0f1a] p-6 rounded-2xl border border-slate-200/60 dark:border-white/5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-slate-50 dark:bg-white/5 rounded-lg text-slate-400">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <span class="text-[10px] font-bold text-amber-500 bg-amber-50 dark:bg-amber-500/10 px-2 py-0.5 rounded-full uppercase">Data Units</span>
                </div>
                <div class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['slips_count'] }}</div>
                <p class="text-xs text-slate-400 mt-1">Total successfully extracted slips</p>
            </div>
        </div>

        <!-- Command Module -->
        <div class="bg-white dark:bg-[#0b0f1a] rounded-3xl border border-slate-200/60 dark:border-white/5 overflow-hidden shadow-sm">
            <div class="p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-lg text-center md:text-left">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Ready for Operation?</h2>
                    <p class="text-slate-400 dark:text-slate-500 mt-2 text-sm leading-relaxed">
                        The AI neural link is calibrated and ready. You can begin scanning new documents or configure the mapping registry.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.slip-reader') }}" class="w-full sm:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs uppercase tracking-widest transition shadow-lg shadow-indigo-500/20 flex items-center justify-center">
                        <i data-lucide="scan" class="w-4 h-4 mr-2"></i> Open Scanner
                    </a>
                    <a href="{{ route('admin.merchants') }}" class="w-full sm:w-auto px-8 py-3 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-600 dark:text-slate-300 rounded-xl font-bold text-xs uppercase tracking-widest transition flex items-center justify-center border border-slate-200/60 dark:border-white/5">
                        <i data-lucide="settings-2" class="w-4 h-4 mr-2"></i> Manage Nodes
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
