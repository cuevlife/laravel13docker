<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill | Secure Access</title>

        <!-- Zero-Flash Theme Script -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                        colors: {
                            discord: {
                                main: '#313338',
                                darker: '#1e1f22',
                                black: '#020617',
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
                letter-spacing: -0.03em;
                transition: background-color 0.5s ease;
            }
            .auth-bg { background-color: #ffffff; }
            .dark .auth-bg { background-color: #020617; }

            /* Advanced SmartBill Shimmer */
            @keyframes shimmer {
                0% { background-position: -200% center; }
                100% { background-position: 200% center; }
            }
            .animate-smartbill-master {
                background: linear-gradient(90deg, #f23f43, #fb7185, #ffffff, #fb7185, #f23f43);
                background-size: 200% auto;
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: shimmer 5s linear infinite;
            }

            /* Entry Animation */
            @keyframes slideIn {
                from { opacity: 0; transform: translateY(15px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .reveal { animation: slideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        </style>
    </head>
    <body class="antialiased auth-bg min-h-screen flex items-center justify-center m-0 p-0 overflow-x-hidden" 
          x-data="{ 
            darkMode: localStorage.getItem('darkMode') === 'true',
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('darkMode', this.darkMode);
                if (this.darkMode) document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
            }
          }">
        
        <!-- Ambient Decor -->
        <div class="fixed inset-0 pointer-events-none -z-10">
            <div class="absolute top-0 left-0 w-full h-full dark:bg-[radial-gradient(circle_at_50%_50%,rgba(35,165,90,0.02),transparent_70%)] opacity-0 dark:opacity-100 transition-opacity duration-1000"></div>
        </div>

        <div class="w-full flex items-center justify-center p-0 sm:p-6 lg:p-12 reveal">
            {{ $slot }}
        </div>
    </body>
</html>
