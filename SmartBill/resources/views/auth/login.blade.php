<x-guest-layout>
    <!-- Logo & Title Section (Abstract Look) -->
    <div class="mb-12 text-center">
        <div class="inline-flex relative group">
            <div class="absolute -inset-4 bg-indigo-500/20 dark:bg-indigo-500/30 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition duration-700"></div>
            <div class="relative w-16 h-16 bg-white dark:bg-slate-900 rounded-[1.25rem] flex items-center justify-center shadow-2xl shadow-indigo-500/10 border border-slate-100 dark:border-white/5">
                <i data-lucide="shield-check" class="w-8 h-8 text-indigo-600 dark:text-indigo-400"></i>
            </div>
        </div>
        <h1 class="mt-8 text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase italic">
            Neural<span class="text-indigo-600 dark:text-indigo-500">Hub</span>
        </h1>
        <p class="mt-3 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em]">Establish Secure Link</p>
    </div>

    <!-- Premium Floating Glass Card -->
    <div class="bg-white/70 dark:bg-slate-900/40 backdrop-blur-2xl rounded-[3rem] p-10 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.1)] dark:shadow-none border border-white dark:border-white/5 relative overflow-hidden transition-all duration-500">
        
        <!-- Subtle Inner Glow Decor -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-emerald-500/5 rounded-full blur-3xl"></div>

        <x-auth-session-status class="mb-6 text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-8 relative z-10">
            @csrf

            <!-- Identity Input Group -->
            <div class="space-y-3">
                <label class="px-1 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Protocol Identifier</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i data-lucide="fingerprint" class="w-4 h-4 text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                           class="block w-full pl-12 pr-6 py-4 bg-slate-50/50 dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all outline-none"
                           placeholder="Admin ID">
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>

            <!-- Passkey Input Group -->
            <div class="space-y-3">
                <label class="px-1 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Neural Passkey</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i data-lucide="key-round" class="w-4 h-4 text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <input id="password" type="password" name="password" required 
                           class="block w-full pl-12 pr-6 py-4 bg-slate-50/50 dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all outline-none"
                           placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Session Options -->
            <div class="flex items-center px-1">
                <label class="flex items-center cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="remember" class="peer h-5 w-5 rounded-lg border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-white/5 text-indigo-600 focus:ring-0 transition-all">
                        <i data-lucide="check" class="absolute w-3 h-3 left-1 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></i>
                    </div>
                    <span class="ml-3 text-[11px] font-black text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors tracking-tight uppercase">Maintain active link</span>
                </label>
            </div>

            <!-- Deploy Connection Button -->
            <div class="pt-2">
                <button type="submit" class="group relative w-full py-4.5 bg-slate-900 dark:bg-indigo-600 hover:bg-black dark:hover:bg-indigo-700 text-white font-black rounded-2xl shadow-2xl shadow-indigo-500/20 transition-all duration-300 transform active:scale-[0.97] overflow-hidden">
                    <span class="relative z-10 flex items-center justify-center uppercase text-[11px] tracking-[0.25em]">
                        Connect to Node
                        <i data-lucide="arrow-right" class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </button>
            </div>
        </form>
    </div>

    <!-- Extra Abstract Decor -->
    <div class="mt-12 text-center">
        <p class="text-[9px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-[0.5em] animate-pulse">Encrypted v2.4.1</p>
    </div>
</x-guest-layout>
