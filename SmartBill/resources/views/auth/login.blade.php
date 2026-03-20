<x-guest-layout>
    <div class="space-y-12">
        <!-- Minimalist Header -->
        <div class="text-center">
            <h1 class="text-3xl font-light tracking-tight text-slate-900 dark:text-white">
                Smart<span class="font-semibold">Bill</span>
            </h1>
            <p class="mt-2 text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em]">Neural Interface</p>
        </div>

        <!-- Pure Content Area -->
        <div class="bg-white dark:bg-[#0b0f1a] rounded-[2.5rem] p-10 sm:p-12 soft-shadow border border-slate-100 dark:border-white/5 transition-all">
            <x-auth-session-status class="mb-8 text-center text-xs font-medium" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-10">
                @csrf

                <!-- ID Input -->
                <div class="relative">
                    <label class="absolute -top-6 left-1 text-[10px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-widest">Authentication ID</label>
                    <div class="group relative border-b border-slate-100 dark:border-white/10 focus-within:border-indigo-500 transition-colors duration-500">
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full px-1 py-4 bg-transparent border-0 text-lg font-medium text-slate-700 dark:text-slate-200 placeholder-slate-200 dark:placeholder-slate-800 focus:ring-0 transition-all outline-none"
                               placeholder="User identifier">
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Passkey Input -->
                <div class="relative">
                    <label class="absolute -top-6 left-1 text-[10px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-widest">Secure Passkey</label>
                    <div class="group relative border-b border-slate-100 dark:border-white/10 focus-within:border-indigo-500 transition-colors duration-500">
                        <input id="password" type="password" name="password" required 
                               class="block w-full px-1 py-4 bg-transparent border-0 text-lg font-medium text-slate-700 dark:text-slate-200 placeholder-slate-200 dark:placeholder-slate-800 focus:ring-0 transition-all outline-none"
                               placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Subtle Actions -->
                <div class="flex items-center justify-between px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded-full border-slate-200 dark:border-white/10 bg-transparent text-slate-900 focus:ring-0 transition-all">
                        <span class="ml-3 text-[10px] font-bold text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors uppercase tracking-tighter">Remember</span>
                    </label>
                </div>

                <!-- Clean Deploy Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-2xl transition shadow-xl shadow-slate-900/10 dark:shadow-none hover:scale-[1.01] active:scale-[0.98] uppercase text-[11px] tracking-[0.2em]">
                        Establish Connection
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Decor (Minimal) -->
        <div class="text-center">
            <span class="text-[9px] font-medium text-slate-300 dark:text-slate-700 uppercase tracking-[0.6em]">v2.5.2 Neural Link</span>
        </div>
    </div>
</x-guest-layout>
