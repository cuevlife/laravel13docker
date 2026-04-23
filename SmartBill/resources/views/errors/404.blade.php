<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - SmartBill</title>
    <!-- Redirect to home after 3 seconds -->
    <meta http-equiv="refresh" content="3;url=/">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .text-discord-green { color: #23a559; }
        .bg-discord-green { background-color: #23a559; }
        .border-discord-green { border-color: #23a559; }
    </style>
</head>
<body class="bg-[#f8fafb] dark:bg-[#1e1f22] h-screen flex items-center justify-center transition-colors">
    <div class="text-center px-4 animate-in fade-in zoom-in duration-500">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-discord-green/10 mb-4">
                <i class="bi bi-exclamation-circle text-discord-green text-4xl"></i>
            </div>
        </div>
        <h1 class="text-2xl font-black text-[#1e1f22] dark:text-white mb-2 uppercase tracking-widest">
            404 - {{ __('Not Found') }}
        </h1>
        <p class="text-[#80848e] text-sm mb-8 font-medium">
            {{ __('The page you are looking for does not exist or has been moved.') }}
        </p>
        
        <div class="flex flex-col items-center gap-4">
            <div class="h-1 w-48 bg-black/5 dark:bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-discord-green animate-[progress_3s_linear]"></div>
            </div>
            <p class="text-[10px] font-black text-discord-green uppercase tracking-widest">
                {{ __('Redirecting in 3 seconds...') }}
            </p>
        </div>

        <div class="mt-10">
            <a href="/" class="inline-flex items-center gap-2 px-6 py-2.5 bg-discord-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="bi bi-house-door-fill"></i> {{ __('Go to Home Now') }}
            </a>
        </div>
    </div>

    <style>
        @keyframes progress {
            from { width: 0%; }
            to { width: 100%; }
        }
        [x-cloak] { display: none !none; }
    </style>
    
    <script>
        // Check system dark mode
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>
