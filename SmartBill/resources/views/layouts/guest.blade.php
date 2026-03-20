<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
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
                background-color: #0f172a; /* Deep Discord Dark */
            }
            .discord-card {
                background-color: #1e293b; /* Slightly lighter block */
            }
            .discord-input {
                background-color: #0f172a;
            }
        </style>
    </head>
    <body class="antialiased min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-[480px]">
            {{ $slot }}
        </div>
        <script>lucide.createIcons();</script>
    </body>
</html>
