<x-guest-layout>
    <div class="relative w-full transition-all duration-700">
        
        <div class="flex flex-col lg:flex-row bg-white dark:bg-[#1e1f22] rounded-[2.5rem] border border-slate-200 dark:border-white/5 overflow-hidden shadow-2xl shadow-slate-200/50 dark:shadow-none min-h-[500px]">
            
            <!-- Side A: Branding with Shimmer Animation -->
            <div class="w-full lg:w-5/12 p-12 lg:p-16 flex flex-col justify-between bg-slate-50 dark:bg-discord-black border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-white/5 relative">
                <div class="relative z-10 text-center lg:text-left">
                    <h1 class="text-5xl lg:text-6xl font-black leading-none tracking-tightest uppercase italic animate-smartbill">
                        Smart<br class="hidden lg:block"/>Bill
                    </h1>
                    <div class="h-1 w-12 bg-discord-red mt-6 rounded-full mx-auto lg:mx-0"></div>
                </div>

                <div class="relative z-10 mt-12 lg:mt-0 text-center lg:text-left">
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] leading-relaxed italic">
                        Link Synchronized<br/>Verified Protocol
                    </p>
                </div>
            </div>

            <!-- Side B: Clean Form -->
            <div class="w-full lg:w-7/12 p-10 lg:p-20 flex flex-col justify-center">
                <div class="max-w-xs mx-auto lg:mx-0 w-full">
                    <div class="mb-12">
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">{{ __('Login') }}</h2>
                        <div class="h-1 w-8 bg-discord-green mt-2 rounded-full"></div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-10">
                        @csrf

                        <div class="relative">
                            <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">{{ __('Username') }}</label>
                            <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                                   class="block w-full bg-transparent border-0 border-b border-slate-200 dark:border-white/10 py-3 text-sm font-bold text-slate-700 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                                   placeholder="Admin ID">
                            <x-input-error :messages="$errors->get('username')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                        </div>

                        <div class="relative">
                            <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">{{ __('Password') }}</label>
                            <input id="password" type="password" name="password" required 
                                   class="block w-full bg-transparent border-0 border-b border-slate-200 dark:border-white/10 py-3 text-sm font-bold text-slate-700 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                                   placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                        </div>

                        <div>
                            <button type="submit" class="w-full py-4 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-xl shadow-emerald-900/20 transition transform active:scale-[0.98] text-[11px] uppercase tracking-[0.3em]">
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

        <!-- Floating Language Selector (Minimal) -->
        <div class="mt-10 flex justify-center space-x-6 text-slate-400 font-black text-[10px] uppercase tracking-[0.4em]">
            <a href="{{ route('lang.switch', 'th') }}" class="hover:text-discord-green transition-colors {{ app()->getLocale() == 'th' ? 'text-discord-green font-black scale-110' : '' }}">THAI</a>
            <span class="opacity-20">/</span>
            <a href="{{ route('lang.switch', 'en') }}" class="hover:text-discord-green transition-colors {{ app()->getLocale() == 'en' ? 'text-discord-green font-black scale-110' : '' }}">ENGLISH</a>
        </div>
    </div>
</x-guest-layout>
