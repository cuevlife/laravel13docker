<x-guest-layout>
    <div class="relative bg-white dark:bg-discord-main rounded-[2.5rem] shadow-2xl border border-slate-200 dark:border-white/5 overflow-hidden transition-all duration-500">
        
        <!-- Action Toolbar (Pinned Perfectly) -->
        <div class="absolute top-8 right-8 flex items-center space-x-4 z-50 item-fade" style="animation-delay: 0.1s;">
            <div class="flex items-center bg-slate-100 dark:bg-black/20 p-1 rounded-xl border border-black/5">
                <a href="{{ route('lang.switch', 'th') }}" class="px-2.5 py-1.5 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">TH</a>
                <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1.5 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">EN</a>
            </div>
            
            <button @click="toggleDarkMode()" class="p-2 rounded-xl bg-slate-100 dark:bg-black/20 text-slate-400 hover:text-amber-500 transition-all border border-slate-200 dark:border-white/5">
                <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4 text-indigo-500"></i>
                <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
            </button>
        </div>

        <div class="p-10 sm:p-14">
            <!-- Branding with Advanced Animation -->
            <div class="text-center mb-14 item-fade" style="animation-delay: 0.2s;">
                <h1 class="text-5xl font-black tracking-tightest uppercase italic animate-smartbill-pro leading-none">
                    SmartBill
                </h1>
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] mt-5 opacity-80">Verified Access Node</p>
            </div>

            <!-- Balanced Simple Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-10">
                @csrf

                <div class="space-y-3 group item-fade" style="animation-delay: 0.3s;">
                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                    <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                           class="block w-full px-6 py-4 bg-slate-50 dark:bg-discord-darker border border-slate-200 dark:border-black/20 rounded-2xl text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-discord-green/10 focus:border-discord-green transition-all outline-none"
                           placeholder="">
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>

                <div class="space-y-3 group item-fade" style="animation-delay: 0.4s;">
                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required 
                           class="block w-full px-6 py-4 bg-slate-50 dark:bg-discord-darker border border-slate-200 dark:border-black/20 rounded-2xl text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-discord-green/10 focus:border-discord-green transition-all outline-none"
                           placeholder="">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="flex items-center justify-between px-2 item-fade" style="animation-delay: 0.5s;">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="h-5 w-5 rounded border-slate-300 dark:border-white/10 bg-slate-50 dark:bg-discord-darker text-discord-green focus:ring-0 transition-all">
                        <span class="ml-3 text-[10px] font-black text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors uppercase tracking-widest">{{ __('Remember Me') }}</span>
                    </label>
                </div>

                <div class="pt-2 item-fade" style="animation-delay: 0.6s;">
                    <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-xl shadow-emerald-900/30 transition transform active:scale-[0.98] text-[11px] uppercase tracking-[0.3em]">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Symmetrical Footer -->
        <div class="p-8 bg-slate-50 dark:bg-black/20 text-center border-t border-slate-100 dark:border-white/5 item-fade" style="animation-delay: 0.7s;">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-[0.8em]">Secure Pipeline v3.9.5</p>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-guest-layout>
