<x-guest-layout>
    <!-- Top-Notch Controls -->
    <div class="fixed top-6 right-6 flex items-center space-x-6 z-50">
        <div class="flex items-center space-x-3 text-[10px] font-black tracking-[0.3em]">
            <a href="{{ route('lang.switch', 'th') }}" class="transition-all hover:scale-110 {{ app()->getLocale() == 'th' ? 'text-discord-red underline underline-offset-8 decoration-2' : 'text-slate-300 dark:text-slate-700' }}">TH</a>
            <a href="{{ route('lang.switch', 'en') }}" class="transition-all hover:scale-110 {{ app()->getLocale() == 'en' ? 'text-discord-red underline underline-offset-8 decoration-2' : 'text-slate-300 dark:text-slate-700' }}">EN</a>
        </div>
        <button @click="toggleDarkMode()" class="text-[10px] font-black tracking-[0.3em] uppercase transition-colors hover:text-amber-500 {{ app()->getLocale() == 'th' ? 'text-xs' : '' }}" :class="darkMode ? 'text-slate-700' : 'text-slate-300'">
            <span x-show="!darkMode">Dark</span>
            <span x-show="darkMode" class="text-amber-400">Light</span>
        </button>
    </div>

    <!-- Fluid Container: Total Freedom from S to XXL -->
    <div class="w-full flex flex-col items-center justify-center min-h-screen sm:min-h-0">
        
        <!-- Branding: Minimalist Center-Stage -->
        <div class="mb-12 text-center">
            <h1 class="text-6xl sm:text-7xl md:text-8xl font-black leading-none tracking-tightest uppercase italic animate-smartbill-master select-none">
                SmartBill
            </h1>
            <div class="h-1 w-12 bg-discord-red mt-6 rounded-full mx-auto shadow-lg shadow-rose-500/20"></div>
        </div>

        <!-- The Content Block: Fluid Width based on Viewport -->
        <div class="w-full sm:w-[420px] md:w-[480px] lg:w-[520px] xl:w-[560px] bg-white dark:bg-discord-main/80 backdrop-blur-3xl sm:rounded-[3rem] p-10 sm:p-16 lg:p-20 transition-all duration-700 border-0 sm:border border-slate-100 dark:border-white/5 sm:shadow-[0_40px_100px_-20px_rgba(0,0,0,0.1)] dark:sm:shadow-none">
            
            <div class="mb-14 text-center">
                <h2 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter italic leading-none">{{ __('Login') }}</h2>
                <p class="mt-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] italic opacity-60">Authorize Protocol Link</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-12">
                @csrf

                <!-- High-Symmetry Input Node -->
                <div class="relative group">
                    <label class="absolute -top-7 left-0 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest transition-colors group-focus-within:text-discord-green">{{ __('Username') }}</label>
                    <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                           class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/10 text-xl font-black text-slate-900 dark:text-white focus:ring-0 focus:border-discord-green transition-all outline-none placeholder-slate-200 dark:placeholder-slate-800"
                           placeholder="">
                    <x-input-error :messages="$errors->get('username')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                </div>

                <!-- High-Symmetry Input Node -->
                <div class="relative group">
                    <label class="absolute -top-7 left-0 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest transition-colors group-focus-within:text-discord-green">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required 
                           class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/10 text-xl font-black text-slate-900 dark:text-white focus:ring-0 focus:border-discord-green transition-all outline-none placeholder-slate-200 dark:placeholder-slate-800"
                           placeholder="">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                </div>

                <div class="pt-4 flex flex-col items-center space-y-8">
                    <!-- Action CTA: Larger and Gilded -->
                    <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-2xl shadow-emerald-950/20 transition-all duration-300 transform active:scale-[0.96] text-sm uppercase tracking-[0.3em]">
                        {{ __('Login') }}
                    </button>

                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="h-5 w-5 rounded-sm border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-discord-black text-discord-green focus:ring-0 transition-all">
                        <span class="ml-3 text-[10px] font-black text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors uppercase tracking-widest">{{ __('Remember Me') }}</span>
                    </label>
                </div>
            </form>
        </div>

        <!-- Footnote: Subtle Presence -->
        <div class="mt-16 text-center opacity-20 hidden sm:block">
            <span class="text-[9px] font-black text-slate-500 uppercase tracking-[1em]">Secure End-to-End Encryption</span>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-guest-layout>
