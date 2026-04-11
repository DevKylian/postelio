<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact — Postelio | Nous contacter</title>
    <meta name="description" content="Une question, une suggestion ou besoin d'aide ? Contactez l'équipe Postelio. Nous vous répondons rapidement.">
    <meta name="keywords" content="contact, postelio, aide, support, artisan, marketing">

    <meta property="og:title" content="Contact — Postelio">
    <meta property="og:description" content="Une question, une suggestion ou besoin d'aide ? Contactez l'équipe Postelio.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/contact') }}">
    <meta property="og:site_name" content="Postelio">

    <link rel="canonical" href="{{ url('/contact') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @if(config('services.umami.id'))
        <script async src="{{ config('services.umami.url') }}/script.js" data-website-id="{{ config('services.umami.id') }}"></script>
    @endif

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased font-sans bg-chalk text-slate-600 selection:bg-copper/30 selection:text-amber-900 leading-normal">

    <div class="min-h-screen flex flex-col relative">

        {{-- Background Glows --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute top-[10%] -right-[10%] w-[40%] h-[30%] bg-amber-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute top-[40%] -left-[10%] w-[40%] h-[30%] bg-orange-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[10%] right-[5%] w-[30%] h-[20%] bg-amber-100/10 rounded-full blur-[100px]"></div>
        </div>

        {{-- Navigation --}}
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100" x-data="{ isMenuOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="/" class="flex items-center gap-2">
                        <x-logo size="md" />
                    </a>

                    {{-- Desktop Menu --}}
                    <div class="hidden md:flex items-center gap-6">
                        <a href="/" class="text-sm font-medium text-slate-500 hover:text-midnight transition-colors">Accueil</a>
                        <a href="{{ route('blog.index') }}" class="text-sm font-medium text-slate-500 hover:text-midnight transition-colors">Blog</a>
                        @if(auth()->check())
                            <a href="{{ route('dashboard') }}" class="btn-primary px-5 py-2 text-xs">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary px-5 py-2 text-xs">Se connecter</a>
                        @endif
                    </div>

                    {{-- Mobile Toggle --}}
                    <div class="md:hidden flex items-center">
                        <button @click="isMenuOpen = !isMenuOpen" class="text-slate-500 p-2 outline-none">
                            <svg x-show="!isMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            <svg x-show="isMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="isMenuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="md:hidden bg-white border-b border-slate-100 shadow-xl overflow-hidden"
                 style="display: none;">
                <div class="px-4 py-6 space-y-3">
                    <a href="/" class="block px-4 py-2 rounded-xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-midnight">Accueil</a>
                    <a href="{{ route('blog.index') }}" class="block px-4 py-2 rounded-xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-midnight">Blog</a>
                    <a href="{{ url('/contact') }}" class="block px-4 py-2 rounded-xl text-base font-bold text-midnight bg-slate-50">Contact</a>
                    <div class="pt-4 px-4">
                        @if(auth()->check())
                            <a href="{{ route('dashboard') }}" class="block w-full text-center btn-primary py-3">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center btn-primary py-3">Se connecter</a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        {{-- Hero --}}
        <header class="py-20 sm:py-28 relative z-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="badge badge-copper gap-2 mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contact
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-bold tracking-tight text-midnight mb-6">
                    On est là pour <span class="text-gradient-copper italic inline-block">vous aider.</span>
                </h1>
                <p class="text-lg sm:text-xl font-light text-slate-500 max-w-xl mx-auto">
                    Une question, une suggestion, ou envie d'en savoir plus ? Écrivez-nous, nous répondons sous 24h.
                </p>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 pb-32 relative z-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">

                    {{-- Left: Info cards --}}
                    <div class="lg:col-span-2 space-y-5">

                        <div class="bg-white rounded-3xl border border-slate-100 shadow-card p-7">
                            <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center mb-5">
                                <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="font-display font-bold text-midnight text-lg mb-2">Email</h3>
                            <p class="text-sm text-slate-500 font-light mb-4">Pour toute demande, écrivez-nous directement.</p>
                            <a href="mailto:hello@postelio.fr" class="text-sm font-semibold text-copper hover:text-amber-600 transition-colors">
                                hello@postelio.fr
                            </a>
                        </div>

                        <div class="bg-white rounded-3xl border border-slate-100 shadow-card p-7">
                            <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center mb-5">
                                <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-display font-bold text-midnight text-lg mb-2">Délai de réponse</h3>
                            <p class="text-sm text-slate-500 font-light">Nous répondons généralement sous <strong class="font-semibold text-midnight">24 heures</strong> les jours ouvrés.</p>
                        </div>

                        <div class="bg-white rounded-3xl border border-slate-100 shadow-card p-7">
                            <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center mb-5">
                                <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                </svg>
                            </div>
                            <h3 class="font-display font-bold text-midnight text-lg mb-2">Réseaux sociaux</h3>
                            <p class="text-sm text-slate-500 font-light mb-4">Suivez-nous et envoyez-nous un DM.</p>
                            <div class="flex gap-3">
                                <a href="https://instagram.com/postelio.app" target="_blank" rel="noopener noreferrer"
                                   class="w-9 h-9 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-copper hover:border-copper/20 hover:bg-amber-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                    </svg>
                                </a>
                                <a href="https://www.linkedin.com/company/postelio" target="_blank" rel="noopener noreferrer"
                                   class="w-9 h-9 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-copper hover:border-copper/20 hover:bg-amber-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                        <rect x="2" y="9" width="4" height="12"/>
                                        <circle cx="4" cy="4" r="2"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                    </div>

                    {{-- Right: Form --}}
                    <div class="lg:col-span-3">
                        @livewire('contact-form')
                    </div>

                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-white/60 backdrop-blur-md border-t border-slate-100 py-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-xs font-light text-slate-400">
                    &copy; {{ date('Y') }} Postelio. Un outil pensé pour les artisans passionnés.
                </div>
                <div class="flex items-center gap-6">
                    <a href="/" class="text-xs font-medium text-slate-400 hover:text-copper transition-colors">Accueil</a>
                    <a href="{{ route('blog.index') }}" class="text-xs font-medium text-slate-400 hover:text-copper transition-colors">Blog</a>
                    <a href="{{ url('/contact') }}" class="text-xs font-bold text-midnight">Contact</a>
                </div>
            </div>
        </footer>

    </div>

    @livewireScripts

</body>
</html>
