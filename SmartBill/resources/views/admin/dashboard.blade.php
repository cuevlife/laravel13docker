<x-app-layout>
    <div class="space-y-10 animate-in fade-in duration-700">
        <!-- Dashboard Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tightest italic">Dashboard</h1>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] mt-2 italic">System.Registry_v3.9</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="flex h-2 w-2 rounded-full bg-discord-green shadow-[0_0_15px_rgba(35,165,90,0.4)]"></span>
                <span class="text-[10px] font-black text-discord-green uppercase tracking-widest">Protocol Active</span>
            </div>
        </div>

        <!-- Stats Grid (Minimalist Solid Blocks) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card: Users -->
            <div class="bg-white dark:bg-discord-main p-8 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none transition-all hover:scale-[1.02] group">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">{{ __('Active Users') }}</span>
                    <div class="w-2 h-2 rounded-full bg-discord-red group-hover:animate-ping"></div>
                </div>
                <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tightest italic">{{ $stats['users_count'] }}</div>
                <p class="text-[9px] font-bold text-slate-300 dark:text-slate-700 uppercase tracking-[0.2em] mt-4">Authorized Nodes</p>
            </div>

            <!-- Card: Merchants -->
            <div class="bg-white dark:bg-discord-main p-8 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none transition-all hover:scale-[1.02] group">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">{{ __('Merchant Stores') }}</span>
                    <div class="w-2 h-2 rounded-full bg-discord-green group-hover:animate-ping"></div>
                </div>
                <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tightest italic">{{ $stats['merchants_count'] }}</div>
                <p class="text-[9px] font-bold text-slate-300 dark:text-slate-700 uppercase tracking-[0.2em] mt-4">Neural Mapping Gates</p>
            </div>

            <!-- Card: Slips -->
            <div class="bg-white dark:bg-discord-main p-8 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none transition-all hover:scale-[1.02] group">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">{{ __('Total Slips') }}</span>
                    <div class="w-2 h-2 rounded-full bg-indigo-500 group-hover:animate-ping"></div>
                </div>
                <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tightest italic">{{ $stats['slips_count'] }}</div>
                <p class="text-[9px] font-bold text-slate-300 dark:text-slate-700 uppercase tracking-[0.2em] mt-4">Extracted Data Units</p>
            </div>
        </div>

        <!-- Call to Action Module -->
        <div class="bg-slate-900 dark:bg-discord-black rounded-[2.5rem] p-10 md:p-16 overflow-hidden relative group">
            <!-- Decorative Accent -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-discord-green/10 rounded-full blur-[80px]"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="max-w-xl text-center md:text-left">
                    <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">{{ __('System Ready') }}</h2>
                    <p class="text-slate-400 mt-3 text-sm font-medium leading-relaxed italic opacity-80">
                        The neural link is synchronized. Ready to extract intelligence from documents.
                    </p>
                </div>
                <div class="shrink-0 w-full md:w-auto">
                    <a href="{{ route('admin.slip-reader') }}" class="flex items-center justify-center px-12 py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-[1.5rem] shadow-xl shadow-emerald-950/40 transition-all transform active:scale-[0.96] text-xs uppercase tracking-[0.3em]">
                        {{ __('Start Scanning') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
