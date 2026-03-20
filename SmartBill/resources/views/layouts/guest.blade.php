<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill</title>

        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&family=Inter:wght@100..900&display=swap" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
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
                background-color: #f8fafc;
                letter-spacing: -0.025em;
                transition: background-color 0.5s ease;
            }
            .dark body { background-color: #020617; }

            /* Keyframes สำหรับตัวหนังสือเรืองแสง */
            @keyframes textShimmer {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .animate-smartbill {
                background: linear-gradient(90deg, #f23f43, #fb7185, #f23f43);
                background-size: 200% auto;
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: textShimmer 3s ease infinite;
                display: inline-block;
            }

            /* แอนิเมชันตอนเปิดหน้าจอ (Fade In & Slide Up) */
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .reveal-content {
                animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            }
        </style>
    </head>
    <body class="antialiased min-h-screen flex items-center justify-center overflow-x-hidden p-4 sm:p-0">
        <div class="w-full max-w-[850px] relative z-10 reveal-content">
            {{ $slot }}
        </div>
    </body>
</html>
