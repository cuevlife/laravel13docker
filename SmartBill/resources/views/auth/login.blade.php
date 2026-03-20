<x-guest-layout>
    <div class="mb-10 text-center">
        <!-- Minimalist Logo -->
        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-500/20 mb-6">
            <i data-lucide="zap" class="w-8 h-8 text-white"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight uppercase tracking-widest">SmartBill</h1>
        <p class="text-sm text-slate-400 dark:text-slate-500 mt-2 font-medium">Access your neural dashboard</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white dark:bg-[#0b0f1a] p-8 rounded-[2rem] border border-slate-200/60 dark:border-white/5 shadow-sm">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Identity -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Authentication ID</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                           class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
                           placeholder="Enter your ID">
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>

            <!-- Passkey -->
            <div class="space-y-2">
                <div class="flex justify-between items-center px-1">
                    <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Secure Passkey</label>
                </div>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <input id="password" type="password" name="password" required 
                           class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
                           placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Options -->
            <div class="flex items-center px-1">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded-md border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-indigo-600 focus:ring-indigo-500/20 transition-all">
                    <span class="ml-2 text-xs font-medium text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors tracking-tight">Remember this session</span>
                </label>
            </div>

            <!-- Action -->
            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/20 transition transform active:scale-[0.98] text-xs uppercase tracking-widest">
                Establish Connection
            </button>
        </form>
    </div>

    <div class="mt-10 text-center">
        <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em]">Protected by Intelligent Security</p>
    </div>
</x-guest-layout>
