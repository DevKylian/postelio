<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Postelio') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/postelio-logo-gradient.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/postelio-logo-gradient.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Umami Analytics -->
    @production
        <script async src="https://analytics.postelio.fr/script.js" data-website-id="90955bbe-b301-4559-85a5-5c0d037b01f3"></script>
    @endproduction

    <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-midnight overflow-x-hidden">
        <div class="min-h-screen flex flex-col md:flex-row shadow-2xl relative overflow-hidden">
            <!-- Left Side: Auth Forms -->
            <div class="flex-1 flex flex-col justify-center items-center p-8 sm:p-12 lg:p-20 bg-white relative z-10 overflow-y-auto">
                <div class="w-full max-w-[480px]">
                    <!-- Logo -->
                    <div class="mb-12">
                        <a href="/" wire:navigate class="inline-block transition-transform hover:scale-105 duration-300">
                            <x-logo size="lg" />
                        </a>
                    </div>

                    <div class="animate-fade-in">
                        {{ $slot }}
                    </div>

                    <p class="mt-12 text-xs text-gray-400 font-medium tracking-wide text-center">
                        &copy; {{ date('Y') }} Postelio.
                    </p>
                </div>
            </div>

            <!-- Right Side: Motion Design Panel -->
            <div class="hidden md:flex flex-1 bg-midnight relative overflow-hidden items-center justify-center p-12">
                <!-- Background Decorations -->
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-copper/10 rounded-full blur-[100px]"></div>
                    <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-amber-500/5 rounded-full blur-[100px]"></div>
                </div>

                <div class="relative z-10 w-full max-w-2xl flex items-center justify-center h-full">
                    <livewire:auth-motion-design />
                </div>
            </div>
        </div>

        <style>
            .animate-fade-in { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    </body>
</html>
