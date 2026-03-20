<x-guest-layout>
    <div class="w-full min-h-screen flex flex-col lg:flex-row">
        
        <!-- Sidebar Branding (Left side on PC, Top on Mobile) -->
        <div class="w-full lg:w-[40%] p-10 lg:p-20 flex flex-col justify-center lg:justify-between relative overflow-hidden bg-slate-50/50 dark:bg-discord-darker/30 border-b lg:border-b-0 lg:border-r border-slate-200 dark:border-white/5">
            <div class="relative z-10 text-center lg:text-left">
                <h1 class="text-5xl lg:text-7xl font-black leading-none tracking-tightest uppercase italic animate-smartbill">
                    Smart<br class="hidden lg:block"/>Bill
                </h1>
                <div class="h-1.5 w-16 bg-discord-red mt-8 rounded-full mx-auto lg:mx-0 shadow-lg shadow-rose-500/20"></div>
            </div>

            <div class="relative z-10 mt-12 lg:mt-0 hidden sm:block text-center lg:text-left">
                <p class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] leading-relaxed italic">
                    AI Data Node<br/>Version 3.8.0
                </p>
            </div>
        </div>

        <!-- Main Form Area (Centered Content) -->
        <div class="w-full lg:w-[60%] flex items-center justify-center p-8 sm:p-12 lg:p-20 bg-white dark:bg-transparent">
            
            <div class="w-full max-w-sm">
                <!-- Top Toolbar (Language & Theme) - Perfectly Integrated -->
                <div class="flex items-center justify-between mb-16">
                    <div class="flex items-center bg-slate-100 dark:bg-white/5 p-1 rounded-xl border border-slate-200 dark:border-white/5">
                        <a href="{{ route('lang.switch', 'th') }}" class="px-3 py-1.5 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">TH</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1.5 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
                    </div>
                    
                    <button @click="toggleDarkMode()" class="p-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 hover:text-amber-500 transition-all border border-slate-200 dark:border-white/5">
                        <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4 text-indigo-500"></i>
                        <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                    </button>
                </div>

                <div class="mb-12">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter italic italic">{{ __('Welcome Back') }}</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 font-bold uppercase tracking-widest italic opacity-70">{{ __('Login') }} to access protocol</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-10">
                    @csrf

                    <!-- Simple & High Contrast Input -->
                    <div class="space-y-3 group">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1 group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/5 text-lg font-black text-slate-900 dark:text-white placeholder-slate-200 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                               placeholder="Admin ID">
                        <x-input-error :messages="$errors->get('username')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <div class="space-y-3 group">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1 group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required 
                               class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/5 text-lg font-black text-slate-900 dark:text-white placeholder-slate-200 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                               placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="h-5 w-5 rounded border-slate-300 dark:border-white/10 bg-slate-50 dark:bg-discord-black text-discord-green focus:ring-0 transition-all">
                            <span class="ml-3 text-[11px] font-black text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors uppercase tracking-widest italic">{{ __('Remember Me') }}</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-2xl shadow-emerald-900/30 transition-all transform active:scale-[0.98] text-xs uppercase tracking-[0.3em]">
                        {{ __('Login') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-guest-layout>
