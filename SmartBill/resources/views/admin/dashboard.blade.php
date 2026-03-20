<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-lg text-slate-800 leading-tight flex items-center">
            <i class="fas fa-radar mr-2 text-indigo-500"></i>{{ __('Neural Overview') }}
        </h2>
    </x-slot>

    <div class="space-y-10 pb-20">
        
        <!-- Stats Cards (Glassmorphism & Glow) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Users Stats -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-3xl blur opacity-10 group-hover:opacity-25 transition duration-1000"></div>
                <div class="relative bg-white rounded-3xl p-8 border border-slate-100 shadow-sm flex items-center">
                    <div class="h-16 w-16 rounded-2xl bg-indigo-50 flex items-center justify-center mr-6 border border-indigo-100 text-indigo-600 shadow-inner">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Entity Count</div>
                        <div class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['users_count'] }}</div>
                        <div class="text-[10px] text-indigo-500 font-bold uppercase mt-1">Active Accounts</div>
                    </div>
                </div>
            </div>

            <!-- Merchants Stats -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-3xl blur opacity-10 group-hover:opacity-25 transition duration-1000"></div>
                <div class="relative bg-white rounded-3xl p-8 border border-slate-100 shadow-sm flex items-center">
                    <div class="h-16 w-16 rounded-2xl bg-emerald-50 flex items-center justify-center mr-6 border border-emerald-100 text-emerald-600 shadow-inner">
                        <i class="fas fa-store text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Neural Nodes</div>
                        <div class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['merchants_count'] }}</div>
                        <div class="text-[10px] text-emerald-500 font-bold uppercase mt-1">Registered Stores</div>
                    </div>
                </div>
            </div>

            <!-- Slips Stats -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl blur opacity-10 group-hover:opacity-25 transition duration-1000"></div>
                <div class="relative bg-white rounded-3xl p-8 border border-slate-100 shadow-sm flex items-center">
                    <div class="h-16 w-16 rounded-2xl bg-amber-50 flex items-center justify-center mr-6 border border-amber-100 text-amber-600 shadow-inner">
                        <i class="fas fa-microchip text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Processing</div>
                        <div class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['slips_count'] }}</div>
                        <div class="text-[10px] text-amber-500 font-bold uppercase mt-1">Extracted Units</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Matrix -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-10 relative overflow-hidden">
            <div class="relative z-10 md:flex items-center justify-between">
                <div class="max-w-xl mb-8 md:mb-0">
                    <h3 class="text-3xl font-black text-slate-900 mb-4 tracking-tighter">Command Center</h3>
                    <p class="text-slate-500 leading-relaxed font-medium">
                        Access the neural network interface to begin data extraction or calibrate merchant mapping protocols. 
                        The system is currently operating at optimal efficiency.
                    </p>
                </div>
                
                <div class="flex flex-col space-y-4 w-full md:w-64">
                    <a href="{{ route('admin.slip-reader') }}" class="group relative py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 transition transform hover:-translate-y-1 overflow-hidden">
                        <span class="relative z-10 flex items-center justify-center uppercase text-xs tracking-widest">
                            <i class="fas fa-scan mr-2"></i> Launch Scanner
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    </a>
                    
                    <a href="{{ route('admin.merchants') }}" class="py-4 bg-slate-50 text-slate-600 font-bold rounded-2xl border border-slate-200 hover:bg-white hover:shadow-lg transition flex items-center justify-center uppercase text-xs tracking-widest">
                        <i class="fas fa-sliders-h mr-2"></i> Configure Nodes
                    </a>
                </div>
            </div>
            
            <!-- Tech Backdrop -->
            <i class="fas fa-brain absolute -right-10 -bottom-10 text-[15rem] text-slate-50 pointer-events-none"></i>
        </div>
    </div>
</x-app-layout>
