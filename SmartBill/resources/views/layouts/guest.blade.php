<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartBill') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://unpkg.com/lucide@latest"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Kanit', sans-serif !important; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased bg-slate-50 dark:bg-[#020617] transition-colors duration-500">
        <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-hidden">
            <!-- Background Orbs -->
            <div class="absolute top-0 left-0 w-full h-full pointer-events-none -z-10">
                <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/10 dark:bg-emerald-500/20 rounded-full blur-[100px]"></div>
            </div>

            <div class="w-full max-w-[440px] transition-all duration-500">
                {{ $slot }}
            </div>
        </div>
        <script>lucide.createIcons();</script>
    </body>
</html>
