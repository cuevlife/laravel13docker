<x-guest-layout>
    <div class="flex flex-col lg:flex-row bg-white/80 dark:bg-slate-900/50 backdrop-blur-2xl rounded-[3rem] shadow-[0_30px_80px_-20px_rgba(0,0,0,0.08)] dark:shadow-none border border-white dark:border-white/5 overflow-hidden transition-all duration-700 min-h-[500px]">
        
        <!-- Left Section: Branding (Hidden on very small, but good for Tablet/Desktop) -->
        <div class="w-full lg:w-5/12 bg-slate-900 dark:bg-black/40 p-10 lg:p-16 flex flex-col justify-between relative overflow-hidden group">
            <!-- Decorative Inner Glow -->
            <div class="absolute -top-20 -left-20 w-64 h-64 bg-indigo-500/20 rounded-full blur-[80px] group-hover:bg-indigo-500/30 transition-all duration-1000"></div>
            
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-500/40">
                    <i data-lucide="cpu" class="w-6 h-6 text-white animate-pulse"></i>
                </div>
                <h2 class="mt-8 text-4xl font-black text-white leading-tight tracking-tighter italic">
                    Smart<br/><span class="text-indigo-500">Bill</span> AI
                </h2>
                <div class="h-1 w-12 bg-indigo-500 mt-6 rounded-full"></div>
            </div>

            <div class="relative z-10 mt-12 lg:mt-0">
                <p class="text-slate-400 text-sm font-light leading-relaxed max-w-[200px]">
                    Intelligence system for automated document extraction and neural mapping.
                </p>
                <div class="mt-8 flex items-center space-x-2 text-slate-500 uppercase tracking-[0.3em] text-[9px] font-black">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span>System Online</span>
                </div>
            </div>
        </div>

        <!-- Right Section: Login Form -->
        <div class="w-full lg:w-7/12 p-10 lg:p-16 flex flex-col justify-center">
            <div class="max-w-sm mx-auto w-full">
                <div class="mb-10 lg:hidden text-center">
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter">Login</h3>
                </div>

                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-8">
                    @csrf

                    <!-- ID Node -->
                    <div class="space-y-3">
                        <label class="px-1 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center justify-between">
                            <span>Identity Link</span>
                            <i data-lucide="fingerprint" class="w-3 h-3 text-slate-300"></i>
                        </label>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full px-6 py-4 bg-slate-50/50 dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all outline-none"
                               placeholder="Admin Identifier">
                        <x-input-error :messages="$errors->get('username')" class="mt-1" />
                    </div>

                    <!-- Passkey Node -->
                    <div class="space-y-3">
                        <label class="px-1 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center justify-between">
                            <span>Neural Passkey</span>
                            <i data-lucide="shield-ellipsis" class="w-3 h-3 text-slate-300"></i>
                        </label>
                        <input id="password" type="password" name="password" required 
                               class="block w-full px-6 py-4 bg-slate-50/50 dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all outline-none"
                               placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Action Layer -->
                    <div class="flex items-center justify-between px-1">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-white/5 text-indigo-600 focus:ring-0 transition-all">
                            <span class="ml-3 text-[10px] font-black text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors uppercase tracking-tighter">Stay Linked</span>
                        </label>
                    </div>

                    <button type="submit" class="group relative w-full py-4.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-500/30 transition-all duration-300 transform active:scale-[0.98] overflow-hidden">
                        <span class="relative z-10 flex items-center justify-center uppercase text-[11px] tracking-[0.25em]">
                            Initialize Connection
                            <i data-lucide="arrow-right" class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Minimal Decor Footer -->
    <div class="mt-10 text-center opacity-30 group">
        <span class="text-[8px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.8em]">End-to-End Neural Encryption v2.5</span>
    </div>
</x-guest-layout>
