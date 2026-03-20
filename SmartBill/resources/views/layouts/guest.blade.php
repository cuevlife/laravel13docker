<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill Login</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&family=Inter:wght@100..900&display=swap" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: { sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'] },
                        colors: {
                            discord: {
                                dark: '#313338',
                                darker: '#1e1f22',
                                black: '#0f172a',
                                green: '#23a55a',
                                red: '#f23f43'
                            }
                        }
                    }
                }
            }
        </script>
        
        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Plus Jakarta Sans', 'sans-serif';
                background-color: #0f172a;
                letter-spacing: -0.02em;
            }
        </style>
    </head>
    <body class="antialiased min-h-screen flex items-center justify-center p-0 sm:p-6 lg:p-12 overflow-x-hidden">
        
        <!-- Background Decor -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
            <div class="absolute top-[-5%] right-[-10%] w-[60%] h-[40%] bg-emerald-500/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-5%] left-[-10%] w-[50%] h-[30%] bg-rose-500/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="w-full max-w-[1000px]">
            {{ $slot }}
        </div>
        <script>lucide.createIcons();</script>
    </body>
</html>
