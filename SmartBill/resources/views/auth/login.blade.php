<x-guest-layout>
    <!-- Language Switcher (Floating Top Right) -->
    <div class="fixed top-6 right-6 flex space-x-2 z-50">
        <a href="{{ route('lang.switch', 'th') }}" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ app()->getLocale() == 'th' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-white/5 text-slate-500 hover:bg-white/10' }}">TH</a>
        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ app()->getLocale() == 'en' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-white/5 text-slate-500 hover:bg-white/10' }}">EN</a>
    </div>

    <div class="discord-card rounded-lg shadow-2xl p-8 sm:p-10 transition-all duration-500 border border-white/5">
        
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-rose-600 rounded-2xl shadow-lg shadow-rose-900/40 mb-6">
                <i data-lucide="zap" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight uppercase tracking-widest italic">{{ __('Welcome Back') }}</h1>
            <p class="text-slate-500 text-xs mt-2 font-medium uppercase tracking-[0.2em]">SmartBill Admin Panel</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Username -->
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-0.5">{{ __('Username') }}</label>
                <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                       class="block w-full px-4 py-3.5 bg-[#0f172a] border-0 rounded-md text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none"
                       placeholder="">
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-0.5">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required 
                       class="block w-full px-4 py-3.5 bg-[#0f172a] border-0 rounded-md text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none"
                       placeholder="">
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="flex items-center">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-emerald-500 focus:ring-0 transition-all">
                    <span class="ml-2 text-[11px] font-bold text-slate-500 group-hover:text-slate-300 transition-colors uppercase tracking-tighter">{{ __('Remember Me') }}</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-md shadow-lg shadow-emerald-900/20 transition-all duration-200 transform active:translate-y-[1px] uppercase text-xs tracking-[0.2em]">
                    {{ __('Login') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
