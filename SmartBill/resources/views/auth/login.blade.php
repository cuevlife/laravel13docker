<x-guest-layout>
    <!-- Main Structure: Card on PC, Full Screen on Mobile -->
    <div class="flex flex-col lg:flex-row min-h-screen sm:min-h-[600px] bg-transparent sm:bg-white sm:dark:bg-discord-main sm:rounded-[3rem] sm:border sm:border-slate-100 sm:dark:border-white/5 sm:shadow-2xl overflow-hidden transition-all duration-500">
        
        <!-- SIDE A: Branding (Center Animated Text) -->
        <div class="w-full lg:w-1/2 p-12 lg:p-20 flex flex-col justify-center items-center lg:items-start bg-transparent sm:bg-slate-50 sm:dark:bg-discord-black sm:border-r border-slate-100 dark:border-white/5 relative">
            <div class="relative z-10 text-center lg:text-left">
                <h1 class="text-5xl lg:text-8xl font-black leading-none tracking-tightest uppercase italic animate-smartbill-pro">
                    Smart<br class="hidden lg:block"/>Bill
                </h1>
                <div class="h-1.5 w-12 bg-discord-red mt-6 rounded-full mx-auto lg:mx-0 hidden sm:block"></div>
            </div>
        </div>

        <!-- SIDE B: Simple Form -->
        <div class="w-full lg:w-1/2 p-8 sm:p-16 lg:p-24 flex flex-col justify-center relative">
            
            <!-- Floating Tools (Hidden on Mobile for Extreme Minimalism, or keep very subtle) -->
            <div class="absolute top-6 right-6 flex items-center space-x-4 z-50">
                <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-300 dark:text-slate-700">
                    <a href="{{ route('lang.switch', 'th') }}" class="{{ app()->getLocale() == 'th' ? 'text-discord-green' : '' }}">TH</a>
                    <span>/</span>
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'text-discord-green' : '' }}">EN</a>
                </div>
                <button @click="toggleDarkMode()" class="text-slate-300 dark:text-slate-700 hover:text-amber-500 transition-all">
                    <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                    <i x-show="darkMode" data-lucide="sun" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="w-full max-w-sm mx-auto">
                <!-- Simple Login Text -->
                <div class="mb-12 text-center lg:text-left">
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">{{ __('Login') }}</h2>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-10">
                    @csrf

                    <!-- Input: Username (Borderless Style) -->
                    <div class="relative">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('Username') }}</label>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full px-1 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/10 text-lg font-black text-slate-800 dark:text-white focus:ring-0 focus:border-discord-green transition-all outline-none placeholder-slate-200 dark:placeholder-slate-800"
                               placeholder="">
                        <x-input-error :messages="$errors->get('username')" class="mt-1" />
                    </div>

                    <!-- Input: Password -->
                    <div class="relative">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required 
                               class="block w-full px-1 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/10 text-lg font-black text-slate-800 dark:text-white focus:ring-0 focus:border-discord-green transition-all outline-none placeholder-slate-200 dark:placeholder-slate-800"
                               placeholder="">
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- CTA Button (Larger & More Prominent) -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-2xl shadow-emerald-900/30 transition transform active:scale-[0.96] text-sm uppercase tracking-[0.3em]">
                            {{ __('Login') }}
                        </button>
                    </div>

                    <div class="flex justify-center">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-200 dark:border-white/10 bg-transparent text-discord-green focus:ring-0">
                            <span class="ml-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('Remember Me') }}</span>
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-guest-layout>
