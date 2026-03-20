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
            .mesh-gradient {
                background-color: #ffffff;
                background-image: 
                    radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(16, 185, 129, 0.05) 0px, transparent 50%);
            }
            .dark .mesh-gradient {
                background-color: #020617;
                background-image: 
                    radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.1) 0px, transparent 50%);
            }
        </style>
    </head>
    <body class="antialiased mesh-gradient min-h-screen transition-colors duration-700">
        <div class="min-h-screen flex items-center justify-center p-6 relative">
            <div class="w-full max-w-[420px] relative z-10">
                {{ $slot }}
            </div>
        </div>
        <script>lucide.createIcons();</script>
    </body>
</html>
