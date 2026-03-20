<x-guest-layout>
    <!-- Language Switcher (Top Right on All Screens) -->
    <div class="fixed top-6 right-6 flex space-x-2 z-[100]">
        <a href="{{ route('lang.switch', 'th') }}" class="px-3 py-1.5 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-discord-green text-white shadow-lg' : 'bg-white/5 text-slate-500 hover:bg-white/10' }}">TH</a>
        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1.5 rounded-lg text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-discord-green text-white shadow-lg' : 'bg-white/5 text-slate-500 hover:bg-white/10' }}">EN</a>
    </div>

    <div class="flex flex-col lg:flex-row min-h-screen sm:min-h-[500px] bg-white/5 dark:bg-discord-darker/40 backdrop-blur-3xl sm:rounded-[3rem] border-0 sm:border sm:border-white/5 overflow-hidden shadow-2xl transition-all duration-700">
        
        <!-- Left: Branding (Horizontal on PC, Header on Mobile) -->
        <div class="w-full lg:w-5/12 bg-discord-darker dark:bg-black/40 p-10 lg:p-16 flex flex-col justify-between relative group overflow-hidden">
            <div class="absolute -top-20 -left-20 w-64 h-64 bg-discord-red/10 rounded-full blur-[80px]"></div>
            
            <div class="relative z-10 text-center lg:text-left">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-discord-red rounded-2xl shadow-xl shadow-rose-900/40 transform hover:rotate-12 transition-all">
                    <i data-lucide="zap" class="w-7 h-7 text-white"></i>
                </div>
                <h2 class="mt-8 text-4xl lg:text-5xl font-black text-white leading-none tracking-tightest italic">
                    Smart<br class="hidden lg:block"/><span class="text-discord-red">Bill</span>
                </h2>
                <div class="h-1 w-12 bg-discord-red mt-6 rounded-full mx-auto lg:mx-0"></div>
            </div>

            <div class="relative z-10 mt-12 lg:mt-0 hidden sm:block">
                <p class="text-slate-400 text-sm font-medium leading-relaxed max-w-[220px] mx-auto lg:mx-0 italic uppercase tracking-wider">
                    Automated Data Extraction & Intelligent Mapping System.
                </p>
                <div class="mt-8 flex items-center justify-center lg:justify-start space-x-2 text-[9px] font-black text-slate-500 uppercase tracking-[0.4em]">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-discord-green animate-pulse"></span>
                    <span>Link Synchronized</span>
                </div>
            </div>
        </div>

        <!-- Right: Login Form -->
        <div class="w-full lg:w-7/12 p-8 sm:p-12 lg:p-20 flex flex-col justify-center bg-white dark:bg-transparent">
            <div class="max-w-sm mx-auto w-full">
                <div class="mb-10 text-center lg:text-left">
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">{{ __('Welcome Back') }}</h3>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-1 font-bold uppercase tracking-widest">{{ __('Login') }} to proceed</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-8">
                    @csrf

                    <!-- Username -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('Username') }}</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-300 group-focus-within:text-discord-green transition-colors">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                                   class="block w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-discord-black border border-slate-100 dark:border-white/5 rounded-2xl text-base font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-discord-green/10 focus:border-discord-green transition-all outline-none"
                                   placeholder="">
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('Password') }}</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-300 group-focus-within:text-discord-green transition-colors">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <input id="password" type="password" name="password" required 
                                   class="block w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-discord-black border border-slate-100 dark:border-white/5 rounded-2xl text-base font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-discord-green/10 focus:border-discord-green transition-all outline-none"
                                   placeholder="">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-5 h-5 rounded border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-discord-black text-discord-green focus:ring-0 transition-all">
                            <span class="ml-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('Remember Me') }}</span>
                        </label>
                    </div>

                    <!-- Login Button (Discord Green) -->
                    <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-xl shadow-emerald-900/20 transition transform active:scale-[0.97] text-xs uppercase tracking-[0.3em]">
                        {{ __('Login') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
