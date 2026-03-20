<x-guest-layout>
    <!-- Language Switcher (Top Right) -->
    <div class="fixed top-8 right-8 flex items-center space-x-4 z-50">
        <div class="flex bg-white/5 p-1 rounded-lg border border-white/5">
            <a href="{{ route('lang.switch', 'th') }}" class="px-3 py-1 rounded-md text-[10px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-discord-green text-white shadow-lg shadow-emerald-900/40' : 'text-slate-500 hover:text-slate-300' }}">TH</a>
            <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 rounded-md text-[10px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-discord-green text-white shadow-lg shadow-emerald-900/40' : 'text-slate-500 hover:text-slate-300' }}">EN</a>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row bg-[#0b0f1a] cyber-border sm:rounded-[2rem] overflow-hidden transition-all duration-700">
        
        <!-- Left: Branding Side (Industrial Minimalist) -->
        <div class="w-full lg:w-1/3 bg-[#0f172a] p-10 lg:p-16 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-white/5">
            <div>
                <div class="w-10 h-10 bg-discord-red rounded-xl flex items-center justify-center shadow-lg shadow-rose-900/20">
                    <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                </div>
                <h1 class="mt-10 text-5xl font-black text-white tracking-tightest leading-none italic uppercase">
                    Smart<br/><span class="text-discord-red">Bill</span>
                </h1>
            </div>
            
            <div class="mt-12 lg:mt-0">
                <div class="h-px w-8 bg-discord-red mb-4"></div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.4em] leading-relaxed">
                    AI Extraction Node<br/>Verified Protocol
                </p>
            </div>
        </div>

        <!-- Right: Login Form (Pure Minimalist) -->
        <div class="w-full lg:w-2/3 p-10 lg:p-20 bg-[#0b0f1a]">
            <div class="max-w-xs mx-auto lg:mx-0">
                <div class="mb-12">
                    <h2 class="text-2xl font-black text-white tracking-tight uppercase italic">{{ __('Login') }}</h2>
                    <div class="h-1 w-6 bg-discord-green mt-2 rounded-full"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-10">
                    @csrf

                    <!-- Field: Username -->
                    <div class="relative group">
                        <label class="absolute -top-6 left-0 text-[9px] font-black text-slate-600 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                        <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                               class="block w-full bg-transparent border-0 border-b border-white/5 py-3 text-sm font-bold text-white placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                               placeholder="User ID">
                        <x-input-error :messages="$errors->get('username')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <!-- Field: Password -->
                    <div class="relative group">
                        <label class="absolute -top-6 left-0 text-[9px] font-black text-slate-600 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required 
                               class="block w-full bg-transparent border-0 border-b border-white/5 py-3 text-sm font-bold text-white placeholder-slate-800 focus:ring-0 focus:border-discord-green transition-all outline-none"
                               placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-discord-red uppercase italic" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded-sm border-white/10 bg-white/5 text-discord-green focus:ring-0 transition-all">
                            <span class="ml-3 text-[9px] font-black text-slate-500 group-hover:text-slate-300 transition-colors uppercase tracking-widest">{{ __('Remember Me') }}</span>
                        </label>
                    </div>

                    <!-- CTA -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-lg shadow-xl shadow-emerald-950/20 transition-all transform active:scale-[0.98] text-[11px] uppercase tracking-[0.3em]">
                            Initialize Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Smallest Footer -->
    <div class="mt-12 text-center">
        <p class="text-[8px] font-black text-slate-700 uppercase tracking-[1em]">Secure System v3.2.0</p>
    </div>
</x-guest-layout>
