<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-6 bg-slate-950 relative overflow-hidden">
        
        <!-- Animated Tech Backdrop -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px]"></div>
        </div>

        <!-- Login Card -->
        <div class="w-full max-w-md relative z-10">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center h-20 w-20 bg-indigo-600 rounded-[2rem] shadow-2xl shadow-indigo-500/50 mb-6 border border-white/20 transform hover:rotate-12 transition-transform duration-500">
                    <i class="fas fa-file-invoice-dollar text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-black text-white tracking-tighter uppercase italic">
                    Smart<span class="text-indigo-500">Bill</span> Intelligence
                </h1>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Secure Access Protocol</p>
            </div>

            <div class="bg-white/5 backdrop-blur-xl rounded-[2.5rem] p-10 border border-white/10 shadow-2xl">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Username -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Authentication ID</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-slate-600 group-focus-within:text-indigo-400 transition"></i>
                            </div>
                            <input id="username" type="text" name="username" :value="old('username')" required autofocus 
                                   class="block w-full pl-11 pr-4 py-4 bg-slate-900/50 border-white/5 rounded-2xl text-white font-bold placeholder-slate-600 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                   placeholder="Admin ID">
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Secure Passkey</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-600 group-focus-within:text-indigo-400 transition"></i>
                            </div>
                            <input id="password" type="password" name="password" required 
                                   class="block w-full pl-11 pr-4 py-4 bg-slate-900/50 border-white/5 rounded-2xl text-white font-bold placeholder-slate-600 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                   placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="rounded-lg bg-slate-900 border-white/10 text-indigo-500 shadow-sm focus:ring-indigo-500 focus:ring-offset-slate-950" name="remember">
                            <span class="ms-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest italic">Maintain Link</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 transition transform active:scale-95 uppercase text-xs tracking-[0.2em]">
                        Establish Connection
                    </button>
                </form>
            </div>

            <p class="text-center mt-8 text-slate-600 text-[10px] font-medium uppercase tracking-widest">
                Protected by AES-128 Neural Encryption
            </p>
        </div>
    </div>
</x-guest-layout>
