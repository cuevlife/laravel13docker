<x-guest-layout>
    <div class="flex flex-col min-h-screen sm:min-h-0">
        
        <!-- Header Section (Larger on Mobile) -->
        <div class="mt-12 mb-10 text-center px-6 sm:mt-0">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-[1.5rem] shadow-xl shadow-indigo-500/20 mb-6">
                <i data-lucide="zap" class="w-10 h-10 text-white"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tighter uppercase">SmartBill</h1>
            <p class="text-base text-slate-400 dark:text-slate-500 mt-2 font-medium">Intelligence Data Protocol</p>
        </div>

        <!-- Login "Card" (Becomes full screen on mobile) -->
        <div class="flex-1 bg-white dark:bg-[#0b0f1a] px-8 py-10 sm:rounded-[2.5rem] sm:border sm:border-slate-200/60 sm:dark:border-white/5 sm:shadow-sm">
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <!-- Identity -->
                <div class="space-y-3">
                    <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Authentication ID</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-2xl text-base font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                               placeholder="Admin Username">
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Passkey -->
                <div class="space-y-3">
                    <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Secure Passkey</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input id="password" type="password" name="password" required 
                               class="block w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-2xl text-base font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                               placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me (Larger Tap Target) -->
                <div class="flex items-center px-1 pt-2">
                    <label class="flex items-center cursor-pointer group py-2">
                        <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-indigo-600 focus:ring-indigo-500/20 transition-all">
                        <span class="ml-3 text-sm font-bold text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors tracking-tight">Keep link active</span>
                    </label>
                </div>

                <!-- Action Button (Larger for iPhone) -->
                <div class="pt-4">
                    <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 transition transform active:scale-[0.97] text-sm uppercase tracking-[0.2em]">
                        Establish Connection
                    </button>
                </div>
            </form>

            <div class="mt-12 text-center">
                <p class="text-[10px] text-slate-300 dark:text-slate-600 font-black uppercase tracking-[0.3em]">Neural Security Layer v2.0</p>
            </div>
        </div>
    </div>
</x-guest-layout>
