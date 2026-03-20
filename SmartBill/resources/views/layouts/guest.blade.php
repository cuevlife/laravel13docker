<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill Login</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Inter:wght@100..900&display=swap" rel="stylesheet">
        
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
                background-color: #020617;
                letter-spacing: -0.025em;
            }
            /* Spectacular Soft Glow */
            .glow-bg {
                background: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.08) 0%, transparent 70%);
            }
        </style>
    </head>
    <body class="antialiased min-h-screen flex items-center justify-center p-4 sm:p-8 glow-bg overflow-x-hidden">
        
        <!-- Abstract Decoration (The "แหวก" Part) -->
        <div class="fixed inset-0 pointer-events-none -z-10">
            <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] bg-emerald-500/[0.03] rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-rose-500/[0.03] rounded-full blur-[100px]"></div>
        </div>

        <div class="w-full max-w-[460px] relative">
            {{ $slot }}
        </div>
        <script>lucide.createIcons();</script>
    </body>
</html>
