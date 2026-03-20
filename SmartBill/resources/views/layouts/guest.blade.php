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
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <script src="https://unpkg.com/lucide@latest"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Kanit', sans-serif !important;
                letter-spacing: -0.01em;
            }
            .auth-bg {
                background-color: #f8fafc;
            }
            .dark .auth-bg {
                background-color: #020617;
            }
        </style>
    </head>
    <body class="antialiased auth-bg min-h-screen transition-colors duration-700">
        <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 relative overflow-hidden">
            <!-- Dynamic Background -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-indigo-500/[0.03] dark:bg-indigo-500/10 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-emerald-500/[0.02] dark:bg-emerald-500/5 rounded-full blur-[100px]"></div>
            </div>

            <div class="w-full max-w-[900px] relative z-10">
                {{ $slot }}
            </div>
        </div>
        <script>lucide.createIcons();</script>
    </body>
</html>
