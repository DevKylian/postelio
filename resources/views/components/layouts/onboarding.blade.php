<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Onboarding</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-full">
        <div class="h-screen w-full flex overflow-hidden">
            <!-- Côté Gauche : Branding (Caché sur mobile) -->
            <div class="hidden lg:flex lg:w-[35%] bg-midnight flex-col justify-between p-12 relative overflow-hidden">
                <!-- Motif discret -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-copper opacity-5 rounded-full -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-copper opacity-5 rounded-full -ml-48 -mb-48"></div>

                <div class="relative z-10">
                    <x-logo size="lg" textClass="text-[1.7rem] text-white" />
                </div>

                <div class="relative z-10">
                    <h1 class="font-display font-extrabold text-white text-4xl leading-tight mb-6">
                        Bienvenue dans l'ère de la communication <span class="text-copper">automatisée</span>.
                    </h1>
                    <p class="text-slate-400 text-lg max-w-sm">
                        Propulsez votre entreprise d'artisanat avec des publications pro générées en quelques secondes.
                    </p>
                </div>

                <div class="relative z-10 text-sm text-slate-500">
                    &copy; {{ date('Y') }} Postelio. Tous droits réservés.
                </div>
            </div>

            <!-- Côté Droit : Contenu du Wizard -->
            <div class="flex-1 bg-chalk flex flex-col justify-center items-center p-6 lg:p-12 overflow-y-auto">
                <main class="w-full max-w-xl">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
