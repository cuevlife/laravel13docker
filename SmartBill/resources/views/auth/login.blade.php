<x-guest-layout>
    <div class="bg-white dark:bg-[#0b0f1a] rounded-[2.5rem] border border-slate-200/60 dark:border-white/5 shadow-xl shadow-slate-200/20 dark:shadow-none overflow-hidden transition-all duration-500">
        
        <!-- Top Branding Section -->
        <div class="pt-10 pb-6 text-center border-b border-slate-50 dark:border-white/5">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-500/30 mb-4">
                <i data-lucide="zap" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-xl font-black text-slate-800 dark:text-white tracking-tight uppercase">SmartBill</h1>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-[0.3em] mt-1">Intelligence Portal</p>
        </div>

        <div class="p-8 sm:p-10">
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- ID Node -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Authentication ID</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                               placeholder="Admin ID">
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>

                <!-- Passkey Node -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Secure Passkey</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password" type="password" name="password" required 
                               class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                               placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Link Options -->
                <div class="flex items-center px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-indigo-600 focus:ring-indigo-500/20 transition-all">
                        <span class="ml-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors tracking-tight uppercase">Maintain active link</span>
                    </label>
                </div>

                <!-- Deploy Action -->
                <button type="submit" class="w-full py-4 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-slate-900/10 dark:shadow-indigo-500/20 transition transform active:scale-[0.98] text-xs uppercase tracking-[0.2em]">
                    Establish Connection
                </button>
            </form>
        </div>

        <!-- Footer Decor -->
        <div class="pb-8 text-center">
            <p class="text-[9px] text-slate-300 dark:text-slate-700 font-black uppercase tracking-[0.4em]">Encrypted Layer v2.0</p>
        </div>
    </div>
</x-guest-layout>
