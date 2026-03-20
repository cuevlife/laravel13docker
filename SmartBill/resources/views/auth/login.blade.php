<x-guest-layout>
    <div class="relative w-full transition-all duration-700">
        
        <!-- Main Login Structure (PC: Horizontal | Mobile: Vertical Stack) -->
        <div class="flex flex-col lg:flex-row bg-white dark:bg-[#1e1f22] rounded-[2.5rem] border border-slate-200 dark:border-white/5 overflow-hidden shadow-2xl shadow-slate-200/50 dark:shadow-none">
            
            <!-- Side A: Animated Branding -->
            <div class="w-full lg:w-5/12 p-12 lg:p-16 flex flex-col justify-between bg-slate-50 dark:bg-discord-black border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-white/5 relative group">
                <div class="relative z-10 text-center lg:text-left">
                    <!-- Text Logo with Shimmer Animation -->
                    <h1 class="text-5xl lg:text-6xl font-black leading-none tracking-tightest uppercase italic animate-text-gradient">
                        Smart<br class="hidden lg:block"/>Bill
                    </h1>
                    <div class="h-1 w-12 bg-discord-red mt-6 rounded-full mx-auto lg:mx-0"></div>
                </div>

                <div class="relative z-10 mt-12 lg:mt-0 text-center lg:text-left">
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] leading-relaxed italic">
                        Processing Node<br/>Link Established
                    </p>
                </div>
            </div>

            <!-- Side B: Clean Minimalist Form (No Icons) -->
            <div class="w-full lg:w-7/12 p-10 lg:p-20 flex flex-col justify-center">
                <div class="max-w-xs mx-auto lg:mx-0 w-full">
                    <div class="mb-12">
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tighter uppercase italic">{{ __('Login') }}</h2>
                        <div class="h-1 w-8 bg-discord-green mt-2 rounded-full"></div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-10">
                        @csrf

                        <!-- Username (Icon-free) -->
                        <div class="relative group">
                            <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                            <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                                   class="block w-full bg-transparent border-0 border-b border-slate-200 dark:border-white/10 py-3 text-sm font-bold text-slate-700 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                                   placeholder="Admin ID">
                            <x-input-error :messages="$errors->get('username')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                        </div>

                        <!-- Password (Icon-free) -->
                        <div class="relative group">
                            <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                            <input id="password" type="password" name="password" required 
                                   class="block w-full bg-transparent border-0 border-b border-slate-200 dark:border-white/10 py-3 text-sm font-bold text-slate-700 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                                   placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                        </div>

                        <!-- Login Button -->
                        <div class="pt-4">
                            <button type="submit" class="group relative w-full py-4 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-lg shadow-xl shadow-emerald-900/20 transition-all duration-300 transform active:scale-[0.98]">
                                <span class="relative z-10 flex items-center justify-center uppercase text-[11px] tracking-[0.3em]">
                                    {{ __('Login') }}
                                </span>
                            </button>
                        </div>

                        <div class="flex items-center justify-center lg:justify-start">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded-sm border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-discord-green focus:ring-0 transition-all">
                                <span class="ml-3 text-[10px] font-black text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors uppercase tracking-widest italic">{{ __('Remember Me') }}</span>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Floating Language Selector -->
        <div class="mt-10 flex justify-center space-x-6 text-slate-400 font-black text-[10px] uppercase tracking-[0.4em]">
            <a href="{{ route('lang.switch', 'th') }}" class="hover:text-discord-green transition-colors {{ app()->getLocale() == 'th' ? 'text-discord-green font-black' : '' }}">THAI</a>
            <span class="opacity-20">/</span>
            <a href="{{ route('lang.switch', 'en') }}" class="hover:text-discord-green transition-colors {{ app()->getLocale() == 'en' ? 'text-discord-green font-black' : '' }}">ENGLISH</a>
        </div>
    </div>
</x-guest-layout>
