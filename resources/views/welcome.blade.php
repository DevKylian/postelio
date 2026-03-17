<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Postelio — Créez vos posts artisanaux en quelques secondes</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-chalk text-midnight">

        <!-- Navigation -->
        <header class="sticky top-0 z-50 bg-chalk/80 backdrop-blur-md border-b border-gray-200/50">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <x-application-logo class="w-8 h-8 fill-current text-copper" />
                    <span class="text-xl font-black tracking-tight text-midnight uppercase italic">Postelio</span>
                </a>
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate
                           class="inline-flex items-center px-5 py-2.5 bg-copper text-white text-sm font-bold rounded-xl uppercase tracking-wider hover:bg-amber-600 transition-all hover:scale-[1.02]">
                            Tableau de bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                           class="text-sm font-semibold text-gray-600 hover:text-midnight transition-colors px-4 py-2">
                            Connexion
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" wire:navigate
                               class="inline-flex items-center px-5 py-2.5 bg-copper text-white text-sm font-bold rounded-xl uppercase tracking-wider hover:bg-amber-600 transition-all hover:scale-[1.02]">
                                Commencer gratuitement
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-copper/5 via-transparent to-midnight/5 pointer-events-none"></div>
            <div class="max-w-6xl mx-auto px-6 py-24 sm:py-32 text-center relative">
                <span class="inline-block mb-6 px-4 py-1.5 bg-copper/10 text-copper text-xs font-black uppercase tracking-widest rounded-full">
                    IA au service des artisans
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-midnight tracking-tight leading-tight mb-6">
                    Vos posts LinkedIn,<br>
                    <span class="text-copper">générés en secondes.</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed mb-10">
                    Postelio transforme vos mémos vocaux en visuels premium prêts à publier.
                    Avis clients, avant/après, FAQ — sans effort, sans agence.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('generate-post') }}" wire:navigate
                           class="inline-flex items-center gap-2 px-8 py-4 bg-copper text-white font-black text-sm uppercase tracking-widest rounded-2xl shadow-xl shadow-copper/25 hover:bg-amber-600 hover:-translate-y-0.5 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Créer un post maintenant
                        </a>
                    @else
                        <a href="{{ route('register') }}" wire:navigate
                           class="inline-flex items-center gap-2 px-8 py-4 bg-copper text-white font-black text-sm uppercase tracking-widest rounded-2xl shadow-xl shadow-copper/25 hover:bg-amber-600 hover:-translate-y-0.5 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Essayer gratuitement
                        </a>
                        <a href="{{ route('login') }}" wire:navigate
                           class="inline-flex items-center px-8 py-4 bg-white text-midnight font-bold text-sm uppercase tracking-widest rounded-2xl border border-gray-200 shadow-sm hover:border-gray-300 hover:-translate-y-0.5 transition-all duration-300">
                            Se connecter
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Features / Templates preview -->
        <section class="py-20 px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-14">
                    <h2 class="text-3xl sm:text-4xl font-black text-midnight tracking-tight mb-4">
                        4 gabarits pour tous vos besoins
                    </h2>
                    <p class="text-gray-500 text-lg max-w-xl mx-auto">
                        Choisissez le style adapté à votre message — Postelio s'occupe du reste.
                    </p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Avis Client -->
                    <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        <div class="aspect-square bg-copper flex flex-col items-center justify-center p-6 text-center relative overflow-hidden">
                            <div class="flex space-x-1 mb-4">
                                @for ($i = 0; $i < 5; $i++)
                                    <svg class="w-5 h-5 text-yellow-300 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-white font-black text-sm uppercase leading-snug">"Travail impeccable, je recommande !"</p>
                            <div class="absolute -top-8 -right-8 w-24 h-24 bg-white/10 rounded-full"></div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-midnight">Avis Client</h3>
                            <p class="text-gray-500 text-sm mt-1">Transformez un témoignage en preuve sociale.</p>
                        </div>
                    </div>

                    <!-- Avant / Après -->
                    <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        <div class="aspect-square bg-gray-100 flex flex-col overflow-hidden">
                            <div class="flex flex-1">
                                <div class="w-1/2 bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-500 text-xs font-bold uppercase">Avant</span>
                                </div>
                                <div class="w-1/2 bg-copper/20 flex items-center justify-center border-l-2 border-white">
                                    <span class="text-copper text-xs font-bold uppercase">Après</span>
                                </div>
                            </div>
                            <div class="h-10 bg-copper flex items-center justify-between px-4">
                                <span class="text-white text-xs font-black uppercase">Avant</span>
                                <span class="text-white text-xs font-black uppercase">Après</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-midnight">Avant / Après</h3>
                            <p class="text-gray-500 text-sm mt-1">Valorisez vos réalisations de chantier.</p>
                        </div>
                    </div>

                    <!-- FAQ Artisan -->
                    <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        <div class="aspect-square bg-midnight flex flex-col p-6 relative overflow-hidden">
                            <span class="inline-block w-8 h-0.5 bg-copper mb-4"></span>
                            <h3 class="text-copper font-black uppercase tracking-widest text-xs mb-3">FAQ Artisan</h3>
                            <p class="text-white font-bold text-sm leading-snug">Pourquoi choisir une isolation par l'extérieur ?</p>
                            <svg class="absolute -bottom-2 -right-2 w-20 h-20 text-white/5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                            </svg>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-midnight">FAQ Artisan</h3>
                            <p class="text-gray-500 text-sm mt-1">Affirmez votre expertise auprès de vos clients.</p>
                        </div>
                    </div>

                    <!-- Lifestyle -->
                    <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        <div class="aspect-square bg-gradient-to-br from-gray-700 to-gray-900 flex items-end p-6">
                            <p class="text-white font-black text-sm uppercase leading-snug">"Le chantier du jour, notre fierté."</p>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-midnight">Lifestyle</h3>
                            <p class="text-gray-500 text-sm mt-1">Partagez l'ambiance de votre quotidien.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="py-20 px-6 bg-midnight">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-4">
                    Comment ça marche ?
                </h2>
                <p class="text-gray-400 text-lg mb-14">3 étapes, 60 secondes.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                    <div class="bg-white/5 rounded-2xl p-8 border border-white/10">
                        <div class="w-12 h-12 bg-copper rounded-xl flex items-center justify-center text-white font-black text-lg mb-6">1</div>
                        <h3 class="text-white font-bold text-lg mb-2">Enregistrez votre idée</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Dictez votre avis client, votre réalisation ou votre conseil directement depuis votre téléphone.</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-8 border border-white/10">
                        <div class="w-12 h-12 bg-copper rounded-xl flex items-center justify-center text-white font-black text-lg mb-6">2</div>
                        <h3 class="text-white font-bold text-lg mb-2">Postelio génère le visuel</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Notre IA transcrit, reformule et crée un visuel 1080×1080 aux couleurs de votre entreprise.</p>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-8 border border-white/10">
                        <div class="w-12 h-12 bg-copper rounded-xl flex items-center justify-center text-white font-black text-lg mb-6">3</div>
                        <h3 class="text-white font-bold text-lg mb-2">Publiez en un clic</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">Téléchargez votre post et partagez-le sur LinkedIn, Instagram ou Facebook. Simple et rapide.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-24 px-6 text-center">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-3xl sm:text-4xl font-black text-midnight tracking-tight mb-4">
                    Prêt à booster votre présence en ligne ?
                </h2>
                <p class="text-gray-500 text-lg mb-10">
                    Rejoignez les artisans qui publient professionnel, sans agence et sans effort.
                </p>
                @auth
                    <a href="{{ route('generate-post') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-10 py-4 bg-copper text-white font-black text-sm uppercase tracking-widest rounded-2xl shadow-xl shadow-copper/25 hover:bg-amber-600 hover:-translate-y-0.5 transition-all duration-300">
                        Créer mon premier post
                    </a>
                @else
                    <a href="{{ route('register') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-10 py-4 bg-copper text-white font-black text-sm uppercase tracking-widest rounded-2xl shadow-xl shadow-copper/25 hover:bg-amber-600 hover:-translate-y-0.5 transition-all duration-300">
                        Commencer gratuitement
                    </a>
                @endauth
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-gray-200 py-8 px-6 text-center text-sm text-gray-400">
            <p>&copy; {{ date('Y') }} Postelio. Tous droits réservés.</p>
        </footer>
    </body>
</html>
