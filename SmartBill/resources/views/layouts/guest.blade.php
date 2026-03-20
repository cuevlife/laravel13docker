<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SmartBill Login</title>

        <!-- Anti-Flicker & Theme Sync -->
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
                letter-spacing: -0.025em;
                transition: background-color 0.5s ease;
            }
            
            /* Background colors */
            .auth-bg { background-color: #f8fafc; }
            .dark .auth-bg { background-color: #020617; }

            /* Text Animation */
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
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .reveal-content {
                animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            }
        </style>
    </head>
    <body class="antialiased auth-bg min-h-screen flex items-center justify-center overflow-x-hidden m-0 p-0" 
          x-data="{ 
            darkMode: localStorage.getItem('darkMode') === 'true',
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('darkMode', this.darkMode);
                if (this.darkMode) document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
            }
          }">
        
        <!-- Subtle Glow Background -->
        <div class="fixed inset-0 pointer-events-none -z-10">
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/[0.03] dark:bg-indigo-500/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-emerald-500/[0.03] dark:bg-emerald-500/5 rounded-full blur-[100px]"></div>
        </div>

        <div class="w-full max-w-[900px] h-screen sm:h-auto reveal-content">
            {{ $slot }}
        </div>
    </body>
</html>
