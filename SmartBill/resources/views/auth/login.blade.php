<x-guest-layout>
    <!-- Top Floating Controls (Language & Theme) -->
    <div class="fixed top-6 right-6 flex items-center space-x-4 z-[100]">
        <div class="flex items-center bg-black/5 dark:bg-white/5 p-1 rounded-xl border border-black/5 dark:border-white/5">
            <a href="{{ route('lang.switch', 'th') }}" class="px-2.5 py-1 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-indigo-600 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">TH</a>
            <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-indigo-600 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
        </div>
        
        <button @click="toggleDarkMode()" class="p-2 rounded-xl bg-black/5 dark:bg-white/5 text-slate-400 hover:text-amber-500 transition-all">
            <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
            <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
        </button>
    </div>

    <div class="flex flex-col lg:flex-row min-h-screen sm:min-h-[500px] bg-white dark:bg-discord-darker sm:rounded-[2.5rem] border-0 sm:border border-slate-200 dark:border-white/5 overflow-hidden shadow-2xl shadow-slate-200/50 dark:shadow-none transition-all duration-700">
        
        <!-- Side A: Branding (Pure Text) -->
        <div class="w-full lg:w-5/12 p-12 lg:p-16 flex flex-col justify-between bg-slate-50 dark:bg-discord-black border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-white/5 relative group">
            <div class="relative z-10 text-center lg:text-left">
                <h1 class="text-5xl lg:text-6xl font-black leading-none tracking-tightest uppercase italic animate-smartbill">
                    Smart<br class="hidden lg:block"/>Bill
                </h1>
                <div class="h-1 w-12 bg-discord-red mt-6 rounded-full mx-auto lg:mx-0"></div>
            </div>

            <div class="relative z-10 mt-12 lg:mt-0 text-center lg:text-left hidden sm:block">
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] leading-relaxed italic">
                    AI Processing Node<br/>Link Established
                </p>
            </div>
        </div>

        <!-- Side B: Extreme Minimalist Form -->
        <div class="w-full lg:w-7/12 p-10 lg:p-20 flex flex-col justify-center bg-white dark:bg-transparent">
            <div class="max-w-xs mx-auto lg:mx-0 w-full">
                <div class="mb-12">
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">{{ __('Login') }}</h2>
                    <div class="h-1 w-8 bg-discord-green mt-2 rounded-full"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-10">
                    @csrf

                    <!-- Username (No Icons) -->
                    <div class="relative group">
                        <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full bg-transparent border-0 border-b border-slate-200 dark:border-white/10 py-3 text-sm font-bold text-slate-700 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                               placeholder="">
                        <x-input-error :messages="$errors->get('username')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <!-- Password (No Icons) -->
                    <div class="relative group">
                        <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required 
                               class="block w-full bg-transparent border-0 border-b border-slate-200 dark:border-white/10 py-3 text-sm font-bold text-slate-700 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                               placeholder="">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4.5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-xl shadow-emerald-950/20 transition transform active:scale-[0.98] text-[11px] uppercase tracking-[0.3em]">
                            {{ __('Login') }}
                        </button>
                    </div>

                    <div class="flex items-center justify-center lg:justify-start">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded-sm border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-discord-black text-discord-green focus:ring-0 transition-all">
                            <span class="ml-3 text-[10px] font-black text-slate-500 group-hover:text-slate-300 transition-colors uppercase tracking-widest italic">{{ __('Remember Me') }}</span>
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Small Decor Footer -->
    <div class="mt-10 text-center opacity-30 sm:block hidden text-slate-400 dark:text-slate-600 text-[8px] font-black uppercase tracking-[1em]">
        Verified System Access
    </div>

    <script>
        // Init Lucide only for the toggle icon
        lucide.createIcons();
    </script>
</x-guest-layout>
