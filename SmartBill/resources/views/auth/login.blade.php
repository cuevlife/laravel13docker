<x-guest-layout>
    <div class="relative w-full max-w-[800px] mx-auto transition-all duration-700">
        
        <!-- Spectacle Layout (PC: Horizontal Split | Mobile: Vertical Stack) -->
        <div class="flex flex-col lg:flex-row bg-[#1e1f22]/80 backdrop-blur-3xl sm:rounded-[2.5rem] border border-white/5 overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)]">
            
            <!-- Side A: Minimal Branding -->
            <div class="w-full lg:w-5/12 p-12 lg:p-16 flex flex-col justify-between bg-discord-black/40 border-b lg:border-b-0 lg:border-r border-white/5 relative group">
                <!-- Abstract Glow -->
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-discord-red/10 rounded-full blur-[100px] group-hover:bg-discord-red/20 transition-all duration-1000"></div>
                
                <div class="relative z-10 text-center lg:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-discord-red rounded-xl shadow-lg shadow-rose-900/20 transform hover:rotate-12 transition-transform duration-500">
                        <i data-lucide="zap" class="w-6 h-6 text-white fill-current"></i>
                    </div>
                    <h1 class="mt-8 text-4xl lg:text-5xl font-black text-white leading-none tracking-tightest uppercase italic">
                        Smart<br class="hidden lg:block"/><span class="text-discord-red">Bill</span>
                    </h1>
                </div>

                <div class="relative z-10 mt-12 lg:mt-0 text-center lg:text-left">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] leading-relaxed italic">
                        Processing Node<br/>Link Established
                    </p>
                </div>
            </div>

            <!-- Side B: Extreme Minimalist Form -->
            <div class="w-full lg:w-7/12 p-10 lg:p-20 flex flex-col justify-center">
                <div class="max-w-xs mx-auto lg:mx-0 w-full">
                    <div class="mb-12">
                        <h2 class="text-2xl font-black text-white tracking-tighter uppercase italic">{{ __('Login') }}</h2>
                        <div class="h-1 w-8 bg-discord-green mt-2 rounded-full"></div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-10">
                        @csrf

                        <!-- Username -->
                        <div class="relative group">
                            <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-600 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                            <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                                   class="block w-full bg-transparent border-0 border-b border-white/10 py-3 text-sm font-bold text-white placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                                   placeholder="">
                            <x-input-error :messages="$errors->get('username')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                        </div>

                        <!-- Password -->
                        <div class="relative group">
                            <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-600 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                            <input id="password" type="password" name="password" required 
                                   class="block w-full bg-transparent border-0 border-b border-white/10 py-3 text-sm font-bold text-white placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                                   placeholder="">
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                        </div>

                        <!-- Submit Button (Spectacular Green) -->
                        <div class="pt-4">
                            <button type="submit" class="group relative w-full py-4 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-lg shadow-xl shadow-emerald-950/20 transition-all duration-300 transform active:scale-[0.98]">
                                <span class="relative z-10 flex items-center justify-center uppercase text-[11px] tracking-[0.3em]">
                                    {{ __('Login') }}
                                    <i data-lucide="arrow-right" class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                                </span>
                            </button>
                        </div>

                        <div class="flex items-center justify-center lg:justify-start">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded-sm border-white/10 bg-white/5 text-discord-green focus:ring-0 transition-all">
                                <span class="ml-3 text-[10px] font-black text-slate-500 group-hover:text-slate-300 transition-colors uppercase tracking-widest italic">{{ __('Remember Me') }}</span>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Floating Language Selector (Minimal Style) -->
        <div class="mt-10 flex justify-center space-x-6 text-slate-700 font-black text-[10px] uppercase tracking-[0.4em]">
            <a href="{{ route('lang.switch', 'th') }}" class="hover:text-discord-green transition-colors {{ app()->getLocale() == 'th' ? 'text-discord-green' : '' }}">THAI</a>
            <span class="opacity-20 text-white">/</span>
            <a href="{{ route('lang.switch', 'en') }}" class="hover:text-discord-green transition-colors {{ app()->getLocale() == 'en' ? 'text-discord-green' : '' }}">ENGLISH</a>
        </div>
    </div>
</x-guest-layout>
