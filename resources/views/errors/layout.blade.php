<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Postelio</title>

    <!-- App Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-midnight overflow-hidden">
    <div class="relative min-h-screen flex flex-col items-center justify-center p-6 sm:p-12">
        
        <!-- Background Decorations (Subtle Blurs) -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-copper/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-amber-500/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 w-full max-w-lg text-center animate-fade-in">
            <!-- Logo -->
            <div class="mb-12 flex justify-center">
                <a href="/" class="inline-block transition-transform hover:scale-105 duration-300">
                    <x-logo size="lg" />
                </a>
            </div>

            <!-- Content Slot -->
            <div class="space-y-6">
                @yield('content')
            </div>

            <!-- Footer -->
            <p class="mt-16 text-xs text-slate-400 font-medium tracking-wide uppercase opacity-60">
                &copy; {{ date('Y') }} Postelio.
            </p>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</body>
</html>
