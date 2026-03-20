<x-app-layout>
    <div class="space-y-8 md:space-y-12">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-white/5 pb-6">
            <h1 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tighter">{{ __('Dashboard') }}</h1>
            <div class="flex items-center space-x-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Network Secure</span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Active Users -->
            <div class="bg-white dark:bg-discord-darker p-8 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">{{ __('Active Users') }}</p>
                <div class="text-5xl font-black text-rose-500 italic">{{ $stats['users_count'] }}</div>
                <div class="mt-4 text-[9px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-widest">Verified Nodes</div>
            </div>

            <!-- Merchant Stores -->
            <div class="bg-white dark:bg-discord-darker p-8 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">{{ __('Merchant Stores') }}</p>
                <div class="text-5xl font-black text-emerald-500 italic">{{ $stats['merchants_count'] }}</div>
                <div class="mt-4 text-[9px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-widest">Active Gateways</div>
            </div>

            <!-- Total Slips -->
            <div class="bg-white dark:bg-discord-darker p-8 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm sm:col-span-2 lg:col-span-1">
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">{{ __('Total Slips') }}</p>
                <div class="text-5xl font-black text-slate-800 dark:text-white italic">{{ $stats['slips_count'] }}</div>
                <div class="mt-4 text-[9px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-widest">Extracted Units</div>
            </div>
        </div>

        <!-- System Status CTA -->
        <div class="bg-[#2b2d31] rounded-3xl p-10 md:p-16 overflow-hidden relative border border-white/5">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-left">
                    <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">{{ __('System Ready') }}</h2>
                    <p class="text-slate-400 mt-2 text-sm font-medium italic opacity-80">Neural data extraction is active and operational.</p>
                </div>
                <a href="{{ route('admin.slip-reader') }}" class="px-12 py-4 bg-discord-green hover:bg-[#1a8348] text-white font-black text-xs uppercase tracking-[0.2em] rounded-xl transition-all shadow-lg shadow-emerald-950/20 active:scale-95">
                    {{ __('Start Scanning') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
