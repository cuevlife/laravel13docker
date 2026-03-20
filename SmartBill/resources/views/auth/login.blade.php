<x-guest-layout>
    <div class="relative group">
        <!-- Background Layer (The "Original" Solid Block) -->
        <div class="absolute inset-0 bg-discord-darker rounded-[2.5rem] shadow-2xl transition-all duration-500 group-hover:scale-[1.01]"></div>
        
        <!-- Top Accent (The "แหวก" Part: Asymmetric Red Stripe) -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-discord-red/10 rounded-tr-[2.5rem] rounded-bl-full pointer-events-none"></div>
        <div class="absolute top-4 right-4 flex space-x-1.5 z-50">
            <a href="{{ route('lang.switch', 'th') }}" class="px-2.5 py-1 rounded-lg text-[9px] font-black transition-all {{ app()->getLocale() == 'th' ? 'bg-discord-green text-white shadow-lg' : 'bg-white/5 text-slate-500 hover:bg-white/10' }}">TH</a>
            <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 rounded-lg text-[9px] font-black transition-all {{ app()->getLocale() == 'en' ? 'bg-discord-green text-white shadow-lg' : 'bg-white/5 text-slate-500 hover:bg-white/10' }}">EN</a>
        </div>

        <!-- Main Content (The Glass Layer) -->
        <div class="relative bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 sm:p-12 overflow-hidden transition-all duration-700">
            
            <!-- Logo Section -->
            <div class="mb-10 flex items-center space-x-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-discord-red blur-lg opacity-20 animate-pulse"></div>
                    <div class="relative w-12 h-12 bg-discord-red rounded-2xl flex items-center justify-center shadow-lg shadow-rose-900/40 transform rotate-3 hover:rotate-12 transition-transform">
                        <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl font-black text-white tracking-tighter uppercase italic">Smart<span class="text-discord-red">Bill</span></h1>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-[0.3em]">Auth.Protocol_v3</p>
                </div>
            </div>

            <!-- Form Section -->
            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <!-- Username Input (Minimal & Clean) -->
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('Username') }}</label>
                        <i data-lucide="fingerprint" class="w-3 h-3 text-slate-600"></i>
                    </div>
                    <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                           class="block w-full px-5 py-4 bg-discord-black/50 border border-white/[0.03] rounded-2xl text-sm font-bold text-slate-200 placeholder-slate-700 focus:ring-4 focus:ring-discord-green/10 focus:border-discord-green/50 transition-all outline-none"
                           placeholder="User Identifier">
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>

                <!-- Password Input -->
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('Password') }}</label>
                        <i data-lucide="shield-check" class="w-3 h-3 text-slate-600"></i>
                    </div>
                    <input id="password" type="password" name="password" required 
                           class="block w-full px-5 py-4 bg-discord-black/50 border border-white/[0.03] rounded-2xl text-sm font-bold text-slate-200 placeholder-slate-700 focus:ring-4 focus:ring-discord-green/10 focus:border-discord-green/50 transition-all outline-none"
                           placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Action Section -->
                <div class="pt-2">
                    <button type="submit" class="group relative w-full py-4.5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-xl shadow-emerald-900/20 transition-all duration-300 transform active:scale-[0.98] overflow-hidden">
                        <span class="relative z-10 flex items-center justify-center uppercase text-[11px] tracking-[0.2em]">
                            {{ __('Login') }}
                            <i data-lucide="arrow-right" class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </button>
                </div>

                <div class="flex items-center justify-center pt-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/10 bg-white/5 text-discord-green focus:ring-0 transition-all">
                        <span class="ml-3 text-[9px] font-black text-slate-500 group-hover:text-slate-300 transition-colors uppercase tracking-widest italic">{{ __('Remember Me') }}</span>
                    </label>
                </div>
            </form>
        </div>

        <!-- Floating Decorative Orb (Spectacular Detail) -->
        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-discord-green/5 rounded-full blur-2xl pointer-events-none group-hover:bg-discord-green/10 transition-all"></div>
    </div>

    <!-- Minimalistic Footer -->
    <div class="mt-10 text-center opacity-20">
        <span class="text-[8px] font-black text-slate-500 uppercase tracking-[0.8em]">End-to-End Encryption Mode</span>
    </div>
</x-guest-layout>
