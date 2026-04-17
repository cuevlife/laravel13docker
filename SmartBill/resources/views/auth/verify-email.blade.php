<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased" x-data="{ darkMode: localStorage.getItem('theme') === 'dark', toggleDarkMode() { this.darkMode = !this.darkMode; localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); } }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email - {{ config('app.name', 'SmartBill') }}</title>
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
                        'discord-blue': '#5865f2',
                        'discord-main': '#1e1f22',
                        'discord-black': '#111214',
                    }
                }
            }
        }
    </script>
    <style>
        @font-face { font-family: 'Inter'; font-style: normal; font-weight: 400; src: local('Inter'), local('Segoe UI'), local('Helvetica Neue'), Arial, sans-serif; }
        body { font-family: 'Inter', 'Noto Sans Thai', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <script src="{{ asset('vendor/alpine.min.js') }}" defer></script>
    <script>
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
        else if (localStorage.getItem('theme') === 'light') document.documentElement.classList.remove('dark');
    </script>
</head>
<body class="bg-[#fafafa] dark:bg-[#1e1f22] font-sans tracking-tight text-slate-900 dark:text-slate-100">
    <div class="flex flex-col lg:flex-row min-h-screen sm:min-h-[600px] bg-white dark:bg-discord-main border-0 sm:border border-slate-100 dark:border-white/5 overflow-hidden transition-all duration-500">
        
        <!-- SIDE A: Branding -->
        <div class="w-full lg:w-1/2 p-12 lg:p-20 flex flex-col justify-center items-center lg:items-start bg-slate-50 dark:bg-discord-black border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-white/5 relative group">
            <div class="relative z-10 text-center lg:text-left space-y-6">
                <h1 class="text-6xl lg:text-8xl font-black leading-[1.1] tracking-tighter uppercase pr-4">
                    Smart<br class="hidden lg:block"/>Bill
                </h1>
                <div class="h-1.5 w-16 bg-discord-red rounded-full mx-auto lg:mx-0 shadow-lg shadow-rose-500/20"></div>
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] ">Intelligence System</p>
            </div>
        </div>

        <!-- SIDE B: Form Content -->
        <div class="w-full lg:w-1/2 p-10 sm:p-16 lg:p-24 flex flex-col justify-between bg-white dark:bg-transparent">
            
            <div class="flex-1 flex flex-col justify-center">
                <div class="w-full max-w-sm mx-auto">
                    <div class="mb-12 text-center lg:text-left">
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter  leading-none">{{ __('Verify Email') }}</h2>
                        <p class="mt-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em]  opacity-60">Authorize Secure Pipeline</p>
                    </div>

                    <div class="mb-10 text-xs font-medium text-slate-500 dark:text-slate-400 leading-relaxed uppercase tracking-wider">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-10 p-4 bg-discord-green/10 border border-discord-green/20 rounded-xl">
                            <p class="text-[10px] font-bold text-discord-green uppercase tracking-widest text-center">
                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                            </p>
                        </div>
                    @endif

                    <div class="space-y-6">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="w-full py-5 bg-discord-green hover:bg-[#1a8348] text-white font-black rounded-2xl shadow-xl shadow-emerald-900/30 transition transform active:scale-[0.96] text-sm uppercase tracking-[0.3em]">
                                {{ __('Resend Verification Email') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}" class="text-center">
                            @csrf
                            <button type="submit" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] hover:text-discord-red transition-colors">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
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
