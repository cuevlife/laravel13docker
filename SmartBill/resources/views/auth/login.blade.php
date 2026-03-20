<x-guest-layout>
    <div class="relative flex flex-col min-h-screen sm:min-h-0 bg-white dark:bg-discord-main sm:rounded-2xl shadow-2xl transition-all duration-500 overflow-hidden">
        
        <!-- Top Toolbar (Language & Theme) -->
        <div class="absolute top-4 right-4 flex items-center space-x-3 z-50">
            <div class="flex items-center bg-slate-100 dark:bg-black/20 p-1 rounded-lg border border-black/5">
                <a href="{{ route('lang.switch', 'th') }}" class="px-2 py-1 rounded text-[9px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-slate-800 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">TH</a>
                <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 rounded text-[9px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-slate-800 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">EN</a>
            </div>
            
            <button @click="toggleDarkMode()" class="p-1.5 rounded-lg bg-slate-100 dark:bg-black/20 text-slate-400 hover:text-amber-500 transition-all">
                <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
            </button>
        </div>

        <div class="p-8 sm:p-10 flex-1 flex flex-col justify-center">
            <!-- Branding Section -->
            <div class="text-center mb-10 mt-4 sm:mt-0">
                <h1 class="text-4xl font-black leading-none tracking-tightest uppercase italic animate-smartbill">
                    SmartBill
                </h1>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] mt-3">Auth.Protocol_v3.5</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Simple Input Field -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">{{ __('Username') }}</label>
                    <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                           class="block w-full px-4 py-3 bg-slate-50 dark:bg-discord-darker border border-slate-200 dark:border-black/20 rounded-lg text-sm font-bold text-slate-700 dark:text-white focus:ring-2 focus:ring-discord-green transition-all outline-none"
                           placeholder="">
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>

                <!-- Simple Input Field -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required 
                           class="block w-full px-4 py-3 bg-slate-50 dark:bg-discord-darker border border-slate-200 dark:border-black/20 rounded-lg text-sm font-bold text-slate-700 dark:text-white focus:ring-2 focus:ring-discord-green transition-all outline-none"
                           placeholder="">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 dark:border-white/10 bg-slate-50 dark:bg-discord-darker text-discord-green focus:ring-0 transition-all">
                        <span class="ml-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors uppercase tracking-widest">{{ __('Remember Me') }}</span>
                    </label>
                </div>

                <!-- Discord Green Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-lg shadow-xl shadow-emerald-900/20 transition transform active:scale-[0.98] text-[11px] uppercase tracking-[0.2em]">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Layer -->
        <div class="p-6 bg-slate-50 dark:bg-black/20 text-center border-t border-slate-100 dark:border-white/5">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-[0.5em] italic">Link Access Secured</p>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-guest-layout>
