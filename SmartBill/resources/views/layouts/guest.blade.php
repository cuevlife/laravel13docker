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
                                dark: '#1e1f22',
                                black: '#020617',
                                green: '#23a55a',
                                red: '#f23f43'
                            }
                        },
                        letterSpacing: { tightest: '-.05em' }
                    }
                }
            }
        </script>
        
        <style>
            [x-cloak] { display: none !important; }
            body { 
                font-family: 'Plus Jakarta Sans', 'sans-serif';
                background-color: #020617;
                letter-spacing: -0.02em;
            }
            .cyber-border {
                border: 1px solid rgba(255, 255, 255, 0.05);
                box-shadow: 0 0 40px rgba(0, 0, 0, 0.5);
            }
        </style>
    </head>
    <body class="antialiased min-h-screen flex items-center justify-center sm:p-6 lg:p-12 overflow-x-hidden">
        
        <!-- Subtle Neon Orbs -->
        <div class="fixed inset-0 pointer-events-none">
            <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-discord-red/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-discord-green/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="w-full max-w-[850px] relative z-10">
            {{ $slot }}
        </div>
        <script>lucide.createIcons();</script>
    </body>
</html>
