<x-guest-layout>
    <!-- Top Bar Controls -->
    <div class="fixed top-8 right-8 flex items-center space-x-8 z-50">
        <div class="flex items-center space-x-4 text-[10px] font-black tracking-[0.3em] text-slate-300 dark:text-slate-700">
            <a href="{{ route('lang.switch', 'th') }}" class="hover:text-discord-green transition-colors {{ app()->getLocale() == 'th' ? 'text-discord-green underline underline-offset-8 decoration-2' : '' }}">TH</a>
            <a href="{{ route('lang.switch', 'en') }}" class="hover:text-discord-green transition-colors {{ app()->getLocale() == 'en' ? 'text-discord-green underline underline-offset-8 decoration-2' : '' }}">EN</a>
        </div>
        <button @click="toggleDarkMode()" class="text-[10px] font-black tracking-[0.3em] uppercase transition-colors hover:text-amber-500" :class="darkMode ? 'text-slate-700' : 'text-slate-300'">
            <span x-show="!darkMode">Dark Mode</span>
            <span x-show="darkMode" class="text-amber-400">Light Mode</span>
        </button>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row bg-white dark:bg-discord-main sm:rounded-[3rem] border-0 sm:border border-slate-100 dark:border-white/5 shadow-2xl overflow-hidden transition-all duration-700">
        
        <!-- Left Side: Large Branding -->
        <div class="w-full lg:w-1/2 p-12 lg:p-24 flex flex-col justify-center items-center lg:items-start bg-slate-50 dark:bg-discord-black border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-white/5 relative group">
            <div class="relative z-10 text-center lg:text-left">
                <h1 class="text-6xl lg:text-8xl font-black leading-none tracking-tightest uppercase italic animate-shimmer">
                    Smart<br class="hidden lg:block"/>Bill
                </h1>
                <div class="h-1.5 w-12 bg-discord-red mt-6 rounded-full mx-auto lg:mx-0 shadow-lg shadow-rose-500/20"></div>
            </div>
        </div>

        <!-- Right Side: Clean Form -->
        <div class="w-full lg:w-1/2 p-10 sm:p-16 lg:p-20 flex flex-col justify-center bg-white dark:bg-transparent">
            <div class="w-full max-w-sm mx-auto">
                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter italic leading-none">{{ __('Login') }}</h2>
                    <p class="mt-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] italic opacity-60">Authorize Secure Pipeline</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-8">
                    @csrf

                    <!-- Field: Username -->
                    <div class="relative group">
                        <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/10 text-xl font-black text-slate-900 dark:text-white focus:ring-0 focus:border-discord-green transition-all outline-none placeholder-slate-200 dark:placeholder-slate-800"
                               placeholder="">
                        <x-input-error :messages="$errors->get('username')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <!-- Field: Password -->
                    <div class="relative group">
                        <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required 
                               class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/10 text-xl font-black text-slate-900 dark:text-white focus:ring-0 focus:border-discord-green transition-all outline-none placeholder-slate-200 dark:placeholder-slate-800"
                               placeholder="">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <!-- Remember Me Toggle (New Symmetrical Position) -->
                    <div class="flex items-center justify-between px-1">
                        <label class="flex items-center cursor-pointer group select-none">
                            <div class="relative">
                                <input type="checkbox" name="remember" class="peer sr-only">
                                <div class="w-9 h-5 bg-slate-100 dark:bg-black/40 rounded-full border border-slate-200 dark:border-white/5 transition-all peer-checked:bg-discord-green peer-checked:border-discord-green"></div>
                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-slate-400 dark:bg-slate-600 rounded-full transition-all peer-checked:translate-x-4 peer-checked:bg-white shadow-sm"></div>
                            </div>
                            <span class="ml-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest transition-colors peer-checked:text-discord-green group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                {{ __('Remember Me') }}
                            </span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-2xl shadow-emerald-900/30 transition transform active:scale-[0.96] text-sm uppercase tracking-[0.3em]">
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Symmetrical Decor Bottom -->
    <div class="mt-12 text-center opacity-20 hidden sm:block">
        <span class="text-[9px] font-black text-slate-500 uppercase tracking-[1em]">Link Encryption Established</span>
    </div>
</x-guest-layout>
