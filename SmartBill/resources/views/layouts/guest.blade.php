<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartBill') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://unpkg.com/lucide@latest"></script>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Kanit', sans-serif !important; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased text-slate-600 dark:text-slate-400 bg-white dark:bg-[#020617]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center relative overflow-x-hidden">
            <!-- Subtle Background Decor -->
            <div class="absolute top-0 left-0 w-full h-full pointer-events-none -z-10 overflow-hidden">
                <div class="absolute top-[-5%] right-[-10%] w-[60%] h-[40%] bg-indigo-500/5 dark:bg-indigo-500/10 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[-5%] left-[-10%] w-[50%] h-[30%] bg-emerald-500/5 dark:bg-emerald-500/10 rounded-full blur-[100px]"></div>
            </div>

            <div class="w-full sm:max-w-md">
                {{ $slot }}
            </div>
        </div>
        <script>lucide.createIcons();</script>
    </body>
</html>
