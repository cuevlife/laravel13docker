<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-black italic tracking-tightest uppercase dark:text-white">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="space-y-12">
        <!-- Fluid Stats Grid: Responsive from 1 to 3 columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-12">
            
            <!-- Stat Card: Users -->
            <div class="group relative bg-white dark:bg-discord-main p-8 md:p-10 rounded-[2.5rem] border border-slate-100 dark:border-white/5 transition-all hover:scale-[1.03] shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-discord-red/5 rounded-full blur-3xl group-hover:bg-discord-red/10 transition-all"></div>
                <div class="relative z-10">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic mb-6 block">{{ __('Active Users') }}</span>
                    <div class="text-6xl md:text-7xl font-black italic tracking-tightest dark:text-white animate-text-neural">
                        {{ $stats['users_count'] }}
                    </div>
                    <div class="mt-6 flex items-center space-x-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-discord-red animate-pulse"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">Live Authorized Nodes</span>
                    </div>
                </div>
            </div>

            <!-- Stat Card: Merchants -->
            <div class="group relative bg-white dark:bg-discord-main p-8 md:p-10 rounded-[2.5rem] border border-slate-100 dark:border-white/5 transition-all hover:scale-[1.03] shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-discord-green/5 rounded-full blur-3xl group-hover:bg-discord-green/10 transition-all"></div>
                <div class="relative z-10">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic mb-6 block">{{ __('Merchant Stores') }}</span>
                    <div class="text-6xl md:text-7xl font-black italic tracking-tightest dark:text-white animate-text-green">
                        {{ $stats['merchants_count'] }}
                    </div>
                    <div class="mt-6 flex items-center space-x-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-discord-green animate-pulse"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">Neural Mapping Synced</span>
                    </div>
                </div>
            </div>

            <!-- Stat Card: Slips (Span full on smaller screens if needed) -->
            <div class="group relative bg-white dark:bg-discord-main p-8 md:p-10 rounded-[2.5rem] border border-slate-100 dark:border-white/5 transition-all hover:scale-[1.03] shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden sm:col-span-2 lg:col-span-1">
                <div class="relative z-10">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic mb-6 block">{{ __('Total Slips') }}</span>
                    <div class="text-6xl md:text-7xl font-black italic tracking-tightest text-slate-900 dark:text-white opacity-90">
                        {{ $stats['slips_count'] }}
                    </div>
                    <div class="mt-6 flex items-center space-x-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">Data Units Processed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Module: Hybrid Flow -->
        <div class="relative bg-slate-900 dark:bg-discord-black rounded-[3rem] p-10 md:p-20 overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.4)]">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_100%_0%,rgba(35,165,90,0.1),transparent_50%)]"></div>
            
            <div class="relative z-10 flex flex-col xl:flex-row items-center justify-between gap-12 text-center xl:text-left">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-black text-white uppercase italic tracking-tighter leading-tight">{{ __('System Ready') }}</h2>
                    <p class="mt-6 text-slate-400 font-medium leading-relaxed italic text-sm md:text-base opacity-80">
                        The neural data extraction interface is operational. Deploy your document nodes to begin automated mapping and intelligence gathering across all synchronized merchant gateways.
                    </p>
                </div>
                
                <div class="shrink-0">
                    <a href="{{ route('admin.slip-reader') }}" class="group relative inline-flex items-center justify-center px-16 py-6 bg-discord-green hover:bg-[#1a8348] text-white font-black text-xs uppercase tracking-[0.3em] rounded-2xl transition-all shadow-2xl shadow-emerald-950/50 hover:-translate-y-1 active:scale-95">
                        <i data-lucide="zap" class="w-4 h-4 mr-3 fill-current icon-pulse-slow"></i>
                        {{ __('Start Scanning') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
