<x-guest-layout>
    <div class="flex flex-col lg:flex-row min-h-screen sm:min-h-[600px] bg-white dark:bg-discord-main sm:rounded-[3rem] border-0 sm:border border-slate-100 dark:border-white/5 shadow-2xl overflow-hidden transition-all duration-500">
        
        <!-- SIDE A: Branding (Centered Symmetry Left) -->
        <div class="w-full lg:w-1/2 p-12 lg:p-20 flex flex-col justify-center items-center lg:items-start bg-slate-50 dark:bg-discord-black border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-white/5 relative overflow-hidden group">
            <!-- Subtle Glow Effect -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-discord-red/5 rounded-full blur-[100px] transition-all group-hover:bg-discord-red/10"></div>
            
            <div class="relative z-10 text-center lg:text-left space-y-6">
                <h1 class="text-6xl lg:text-8xl font-black leading-none tracking-tightest uppercase italic animate-smartbill-pro">
                    Smart<br class="hidden lg:block"/>Bill
                </h1>
                <div class="h-1.5 w-16 bg-discord-red rounded-full mx-auto lg:mx-0 shadow-lg shadow-rose-500/20"></div>
                <p class="text-[10px] lg:text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] italic">Intelligence System</p>
            </div>
        </div>

        <!-- SIDE B: Authentication Form (Centered Symmetry Right) -->
        <div class="w-full lg:w-1/2 p-10 sm:p-16 lg:p-24 flex flex-col justify-center bg-white dark:bg-transparent relative">
            
            <!-- Floating Settings (Integrated Top Right) -->
            <div class="absolute top-8 right-8 flex items-center space-x-4 z-50">
                <div class="flex items-center bg-slate-100 dark:bg-black/20 p-1 rounded-xl">
                    <a href="{{ route('lang.switch', 'th') }}" class="px-2.5 py-1 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">TH</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-discord-green text-slate-900 dark:text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
                </div>
                <button @click="toggleDarkMode()" class="p-2 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 hover:text-amber-500 transition-all border border-slate-200 dark:border-white/5">
                    <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4 text-indigo-500"></i>
                    <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                </button>
            </div>

            <div class="w-full max-w-sm mx-auto">
                <div class="mb-12 text-center lg:text-left">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter italic">{{ __('Welcome Back') }}</h2>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-2 font-bold uppercase tracking-widest italic opacity-60">Authorize connection to proceed</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-10">
                    @csrf

                    <!-- Identity -->
                    <div class="space-y-3 group">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/5 text-lg font-black text-slate-900 dark:text-white placeholder-slate-200 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                               placeholder="">
                        <x-input-error :messages="$errors->get('username')" class="mt-1 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <!-- Passkey -->
                    <div class="space-y-3 group">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required 
                               class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/5 text-lg font-black text-slate-900 dark:text-white placeholder-slate-200 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                               placeholder="">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <!-- Actions -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-2xl shadow-emerald-900/30 transition transform active:scale-[0.98] text-[11px] uppercase tracking-[0.3em]">
                            {{ __('Login') }}
                        </button>
                    </div>

                    <div class="flex items-center justify-center lg:justify-start">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="h-5 w-5 rounded border-slate-300 dark:border-white/10 bg-slate-50 dark:bg-discord-black text-discord-green focus:ring-0 transition-all">
                            <span class="ml-3 text-[10px] font-black text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors uppercase tracking-widest">{{ __('Remember Me') }}</span>
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-guest-layout>
