<x-guest-layout>
    <div class="relative bg-white dark:bg-discord-main rounded-[2rem] shadow-2xl border border-slate-200 dark:border-white/5 overflow-hidden transition-all duration-500">
        
        <!-- Action Toolbar -->
        <div class="absolute top-6 right-6 flex items-center space-x-4 z-50">
            <div class="flex items-center bg-slate-100 dark:bg-black/20 p-1 rounded-xl border border-black/5">
                <a href="{{ route('lang.switch', 'th') }}" class="px-2.5 py-1.5 rounded-lg text-[9px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">TH</a>
                <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1.5 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">EN</a>
            </div>
            
            <button @click="toggleDarkMode()" class="p-2 rounded-xl bg-slate-100 dark:bg-black/20 text-slate-400 hover:text-amber-500 transition-all">
                <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4 text-indigo-500"></i>
                <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
            </button>
        </div>

        <div class="p-10 sm:p-12">
            <!-- Branding -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-black tracking-tightest uppercase italic animate-shimmer leading-none">
                    SmartBill
                </h1>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] mt-4">Auth.Protocol_v3.9</p>
            </div>

            <!-- Simple Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <div class="space-y-2.5 group">
                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                    <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                           class="block w-full px-5 py-4 bg-slate-50 dark:bg-discord-darker border border-slate-200 dark:border-black/20 rounded-2xl text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-discord-green/10 focus:border-discord-green transition-all outline-none"
                           placeholder="">
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>

                <div class="space-y-2.5 group">
                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required 
                           class="block w-full px-5 py-4 bg-slate-50 dark:bg-discord-darker border border-slate-200 dark:border-black/20 rounded-2xl text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-discord-green/10 focus:border-discord-green transition-all outline-none"
                           placeholder="">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="h-5 w-5 rounded border-slate-300 dark:border-white/10 bg-slate-50 dark:bg-discord-black text-discord-green focus:ring-0 transition-all">
                        <span class="ml-3 text-[10px] font-black text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors uppercase tracking-widest">{{ __('Remember Me') }}</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4.5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-xl shadow-emerald-900/30 transition transform active:scale-[0.98] text-[11px] uppercase tracking-[0.3em]">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="p-6 bg-slate-50 dark:bg-black/20 text-center border-t border-slate-100 dark:border-white/5">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-[0.6em]">System Established</p>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-guest-layout>
