<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased" x-data="{ darkMode: localStorage.getItem('theme') === 'dark', toggleDarkMode() { this.darkMode = !this.darkMode; localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); } }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'SmartBill') }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2323a559' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z'/><path d='M16 8h-6'/><path d='M16 12H8'/><path d='M13 16H8'/></svg>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'Noto Sans Thai', 'sans-serif'] },
                    colors: {
                        'discord-gray': '#313338',
                        'discord-green': '#23a559',
                        'discord-red': '#da373c',
                        'discord-main': '#1e1f22',
                        'discord-black': '#111214',
                    },
                    animation: {
                        'smartbill': 'smartbill 3s ease-in-out infinite',
                    },
                    keyframes: {
                        smartbill: {
                            '0%, 100%': { filter: 'brightness(1)', transform: 'scale(1)' },
                            '50%': { filter: 'brightness(1.2)', transform: 'scale(1.02)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @font-face { font-family: 'Inter'; font-style: normal; font-weight: 400; src: local('Inter'), local('Segoe UI'), local('Helvetica Neue'), Arial, sans-serif; }
        body { font-family: 'Inter', 'Noto Sans Thai', sans-serif; }
        [x-cloak] { display: none !important; }
        
        .animate-gradient-green-red {
            background: linear-gradient(to right, #23a559, #da373c, #23a559);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: textShine 4s ease-in-out infinite;
        }
        @keyframes textShine {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <script src="{{ asset('vendor/alpine.min.js') }}" defer></script>
    <script>
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
        else if (localStorage.getItem('theme') === 'light') document.documentElement.classList.remove('dark');
    </script>
</head>
<body class="bg-[#fafafa] dark:bg-[#111214] font-sans tracking-tight text-slate-900 dark:text-slate-100 flex items-center justify-center min-h-screen p-4 sm:p-6 lg:p-8">
    
    <div class="flex flex-col lg:flex-row w-full max-w-5xl bg-white dark:bg-discord-main rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-[0_20px_70px_-10px_rgba(35,165,89,0.15)] overflow-hidden transition-all duration-500">
        
        <!-- SIDE A: Branding (Green-Red Tone) -->
        <div class="w-full lg:w-1/2 p-12 lg:p-20 flex flex-col justify-center items-center lg:items-start bg-slate-50 dark:bg-discord-black border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-white/5 relative group overflow-hidden">
            <!-- Animated Background shapes (Green & Red) -->
            <div class="absolute top-[-10%] right-[-10%] w-64 h-64 bg-discord-green/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-64 h-64 bg-discord-red/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 text-center lg:text-left space-y-6">
                <h1 class="text-6xl lg:text-8xl font-black leading-[1] tracking-tighter uppercase animate-smartbill animate-gradient-green-red">
                    Smart<br class="hidden lg:block"/>Bill
                </h1>
                <div class="h-1.5 w-16 bg-discord-red rounded-full mx-auto lg:mx-0 shadow-lg shadow-rose-500/20"></div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] ">Intelligence System</p>
            </div>
        </div>

        <!-- SIDE B: Login Form -->
        <div class="w-full lg:w-1/2 p-10 sm:p-16 flex flex-col justify-between bg-white dark:bg-transparent">
            
            <div class="flex-1 flex flex-col justify-center">
                <div class="w-full max-w-sm mx-auto">
                    <div class="mb-12 text-center lg:text-left">
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter leading-none">{{ __('Login') }}</h2>
                        <p class="mt-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] opacity-60">Authorize Secure Pipeline</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-10">
                        @csrf

                        <!-- Username -->
                        <div class="relative group">
                            <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Username') }}</label>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus 
                                   class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/10 text-xl font-black text-slate-900 dark:text-white focus:ring-0 focus:border-discord-green transition-all outline-none placeholder-slate-200 dark:placeholder-slate-800">
                            @if($errors->has('username'))
                                <p class="mt-2 text-[10px] font-bold text-discord-red uppercase">{{ $errors->first('username') }}</p>
                            @endif
                        </div>

                        <!-- Password -->
                        <div class="relative group">
                            <label class="absolute -top-6 left-0 text-[10px] font-black text-slate-400 uppercase tracking-widest group-focus-within:text-discord-green transition-colors">{{ __('Password') }}</label>
                            <input id="password" type="password" name="password" required 
                                   class="block w-full px-0 py-4 bg-transparent border-0 border-b-2 border-slate-100 dark:border-white/10 text-xl font-black text-slate-900 dark:text-white focus:ring-0 focus:border-discord-green transition-all outline-none placeholder-slate-200 dark:placeholder-slate-800">
                            @if($errors->has('password'))
                                <p class="mt-2 text-[10px] font-bold text-discord-red uppercase">{{ $errors->first('password') }}</p>
                            @endif
                        </div>

                        <!-- Options -->
                        <div class="flex items-center justify-between px-1 -mt-4">
                            <label class="flex items-center cursor-pointer group select-none">
                                <div class="relative">
                                    <input type="checkbox" name="remember" value="1" class="peer sr-only">
                                    <div class="w-9 h-5 bg-slate-100 dark:bg-black/40 rounded-full border border-slate-200 dark:border-white/5 transition-all peer-checked:bg-discord-green text-xs"></div>
                                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-slate-400 dark:bg-slate-600 rounded-full transition-all peer-checked:translate-x-4 peer-checked:bg-white shadow-sm"></div>
                                </div>
                                <span class="ml-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest transition-colors peer-checked:text-discord-green group-hover:text-slate-600 dark:group-hover:text-slate-300">
                                    {{ __('Remember Me') }}
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-xl shadow-emerald-900/30 transition transform active:scale-[0.96] text-sm uppercase tracking-[0.3em]">
                            {{ __('Login') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Integrated Controls -->
            <div class="mt-12 flex flex-col items-center space-y-6 opacity-40 hover:opacity-100 transition-opacity duration-500">
                <div class="flex items-center space-x-8 text-[9px] font-black tracking-[0.3em] uppercase">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('lang.switch', 'th') }}" class="hover:text-discord-red transition-colors {{ app()->getLocale() == 'th' ? 'text-discord-red' : 'text-slate-500' }}">TH</a>
                        <span class="text-slate-800">/</span>
                        <a href="{{ route('lang.switch', 'en') }}" class="hover:text-discord-red transition-colors {{ app()->getLocale() == 'en' ? 'text-discord-red' : 'text-slate-500' }}">EN</a>
                    </div>
                    <span class="h-3 w-px bg-slate-200 dark:bg-white/10"></span>
                    <button @click="toggleDarkMode()" class="hover:text-amber-500 transition-colors uppercase" :class="darkMode ? 'text-amber-400' : 'text-slate-500'">
                        <span x-show="!darkMode">Dark Mode</span>
                        <span x-show="darkMode" x-cloak>Light Mode</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>