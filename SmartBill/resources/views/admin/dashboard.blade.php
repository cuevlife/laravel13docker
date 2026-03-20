<x-app-layout>
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight italic uppercase">{{ __('Dashboard') }}</h1>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">SmartBill Intelligence</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="discord-card p-6 rounded-lg border border-white/5 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-rose-500"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">{{ __('Active Users') }}</span>
                    <i data-lucide="users" class="w-4 h-4 text-rose-500"></i>
                </div>
                <div class="text-4xl font-black text-white tracking-tighter italic">{{ $stats['users_count'] }}</div>
            </div>

            <div class="discord-card p-6 rounded-lg border border-white/5 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">{{ __('Merchant Stores') }}</span>
                    <i data-lucide="store" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <div class="text-4xl font-black text-white tracking-tighter italic">{{ $stats['merchants_count'] }}</div>
            </div>

            <div class="discord-card p-6 rounded-lg border border-white/5 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">{{ __('Total Slips') }}</span>
                    <i data-lucide="file-check" class="w-4 h-4 text-indigo-500"></i>
                </div>
                <div class="text-4xl font-black text-white tracking-tighter italic">{{ $stats['slips_count'] }}</div>
            </div>
        </div>

        <div class="discord-card rounded-lg border border-white/5 overflow-hidden shadow-2xl">
            <div class="p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                <div class="max-w-lg">
                    <h2 class="text-xl font-black text-white uppercase italic tracking-tight">{{ __('System Ready') }}</h2>
                    <p class="text-slate-400 mt-2 text-sm font-medium leading-relaxed italic">
                        Start scanning your payment slips now or configure your merchant list in the settings menu.
                    </p>
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <a href="{{ route('admin.slip-reader') }}" class="w-full md:w-auto px-10 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[11px] uppercase tracking-[0.2em] rounded-md transition-all shadow-lg">
                        {{ __('Start Scanning') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
