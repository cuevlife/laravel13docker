<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill</title>

        <script>
            function applyTheme() {
                if (localStorage.getItem('darkMode') === 'true') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
            applyTheme();
        </script>

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
                                main: '#313338',
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
                letter-spacing: -0.02em;
                transition: background-color 0.5s ease;
            }
            
            .auth-bg { background-color: #f8fafc; }
            .dark .auth-bg { background-color: #020617; }

            /* Advanced SmartBill Animation */
            @keyframes shimmerFlow {
                0% { background-position: -200% center; }
                100% { background-position: 200% center; }
            }
            @keyframes breatheGlow {
                0%, 100% { filter: drop-shadow(0 0 5px rgba(242, 63, 67, 0.2)); }
                50% { filter: drop-shadow(0 0 15px rgba(242, 63, 67, 0.5)); }
            }

            .animate-smartbill-pro {
                background: linear-gradient(
                    to right, 
                    #f23f43 20%, 
                    #ff8e8e 40%, 
                    #ffffff 50%, 
                    #ff8e8e 60%, 
                    #f23f43 80%
                );
                background-size: 200% auto;
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: shimmerFlow 4s linear infinite, breatheGlow 3s ease-in-out infinite;
                display: inline-block;
            }

            /* Entrance Animations */
            @keyframes cardReveal {
                from { opacity: 0; transform: scale(0.95) translateY(10px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }
            @keyframes staggeredFade {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .reveal-card { animation: cardReveal 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
            .item-fade { opacity: 0; animation: staggeredFade 0.5s ease forwards; }
        </style>
    </head>
    <body class="antialiased auth-bg min-h-screen flex items-center justify-center m-0 p-4 sm:p-8 overflow-hidden">
        <div class="w-full max-w-[420px] relative reveal-card">
            {{ $slot }}
        </div>
    </body>
</html>
