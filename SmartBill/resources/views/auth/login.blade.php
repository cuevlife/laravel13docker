<x-guest-layout>
    <div class="discord-card rounded-lg shadow-2xl p-8 sm:p-10 transition-all duration-500">
        
        <!-- Discord Style Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-rose-600 rounded-2xl shadow-lg shadow-rose-900/40 mb-4 transform hover:scale-110 transition-transform">
                <i data-lucide="flame" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight uppercase tracking-widest">Welcome Back!</h1>
            <p class="text-slate-400 text-sm mt-1 font-medium italic">Establishing Neural Connection...</p>
        </div>

        <x-auth-session-status class="mb-6 text-center text-xs font-bold text-emerald-500" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Discord Style Input Group -->
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-wider ml-0.5 flex items-center">
                    Authentication ID <span class="text-rose-500 ml-1">*</span>
                </label>
                <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                       class="block w-full px-4 py-3 discord-input border-0 rounded-md text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none"
                       placeholder="">
                <x-input-error :messages="$errors->get('username')" class="mt-1 text-rose-500 text-[10px] font-bold uppercase" />
            </div>

            <!-- Discord Style Input Group -->
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-wider ml-0.5 flex items-center">
                    Neural Passkey <span class="text-rose-500 ml-1">*</span>
                </label>
                <input id="password" type="password" name="password" required 
                       class="block w-full px-4 py-3 discord-input border-0 rounded-md text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none"
                       placeholder="">
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-rose-500 text-[10px] font-bold uppercase" />
            </div>

            <!-- Subtle Link -->
            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-emerald-500 focus:ring-0 transition-all">
                    <span class="ml-2 text-[11px] font-bold text-slate-500 group-hover:text-slate-300 transition-colors uppercase tracking-tighter">Stay Linked</span>
                </label>
                <a href="#" class="text-[11px] font-bold text-indigo-400 hover:underline">Need help?</a>
            </div>

            <!-- Discord Vibrant Green Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-md shadow-lg shadow-emerald-900/20 transition-all duration-200 transform active:translate-y-[1px] uppercase text-xs tracking-widest">
                    Log In
                </button>
            </div>

            <!-- Footer Text -->
            <div class="pt-2 text-left">
                <p class="text-[11px] text-slate-500">
                    Protected by <span class="text-rose-500 font-bold">Neural Security</span>. 
                    <a href="#" class="text-indigo-400 hover:underline">System Protocols</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
