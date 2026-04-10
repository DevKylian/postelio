<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guides Gratuits — Postelio | Experts en Lead Magnets pour artisans</title>
    <meta name="description" content="Téléchargez nos guides gratuits experts pour développer votre activité d'artisan. Scripts de vente, stratégies marketing et processus étape par étape.">
    <meta name="keywords" content="guide artisan, lead magnet, vente bâtiment, objection prix, marketing artisan">

    <meta property="og:title" content="Guides Gratuits Postelio — Développez votre activité d'artisan">
    <meta property="og:description" content="Téléchargez nos guides gratuits experts pour développer votre activité d'artisan.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('guides.index') }}">
    <meta property="og:site_name" content="Postelio">

    <link rel="canonical" href="{{ route('guides.index') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Umami Analytics -->
    @if(config('services.umami.id'))
        <script async src="{{ config('services.umami.url') }}/script.js" data-website-id="{{ config('services.umami.id') }}"></script>
    @endif

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased font-sans bg-chalk text-slate-600 selection:bg-copper/30 selection:text-amber-900 leading-normal">

    <div class="min-h-screen flex flex-col relative">
        {{-- Global Background Glows --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute top-[10%] -right-[10%] w-[40%] h-[30%] bg-amber-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute top-[40%] -left-[10%] w-[40%] h-[30%] bg-orange-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[10%] right-[5%] w-[30%] h-[20%] bg-amber-100/10 rounded-full blur-[100px]"></div>
        </div>

        {{-- Navigation (Matched with Blog) --}}
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
                        <a href="{{ route('guides.index') }}" class="text-sm font-bold text-midnight">Guides</a>
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
                    <a href="{{ route('guides.index') }}" class="block px-4 py-2 rounded-xl text-base font-bold text-midnight bg-slate-50">Guides</a>
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

        {{-- Hero (Matched with Blog) --}}
        <header class="py-20 sm:py-28 relative z-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="badge badge-copper gap-2 mb-6">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    Guides Stratégiques
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-display font-bold tracking-tight text-midnight mb-6">
                    La bibliothèque des <br class="hidden sm:block">
                    <span class="text-gradient-copper italic inline-block">artisans qui osent.</span>
                </h1>
                <p class="text-lg sm:text-xl font-light text-slate-500 max-w-2xl mx-auto leading-relaxed">
                    Scripts de vente, méthodes marketing et processus d'automatisation.
                    Tout ce qu'il faut savoir pour transformer vos chantiers en machine à clients.
                </p>
            </div>
        </header>

        {{-- Guides Grid --}}
        <main class="flex-1 pb-32 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($guides->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 xl:gap-12">
                        @foreach($guides as $guide)
                            <article class="group relative flex flex-col h-full bg-white rounded-[2.5rem] border border-slate-100 p-3 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden">
                                <a href="{{ route('guides.show', $guide->slug) }}" class="flex flex-col h-full">
                                    {{-- Book Cover Wrapper --}}
                                    <div class="relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-slate-100 mb-6 shadow-inner ring-1 ring-slate-900/5">
                                        @if($guide->featured_image)
                                            <img src="{{ Storage::url($guide->featured_image) }}" alt="{{ $guide->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                                            {{-- Spine/Depth Effect --}}
                                            <div class="absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-black/20 via-black/5 to-transparent"></div>
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center p-8 text-center">
                                                <div class="space-y-4">
                                                    <x-logo size="sm" :showText="false" class="mx-auto opacity-20" />
                                                    <h3 class="text-white font-display font-bold text-lg leading-tight">{{ $guide->title }}</h3>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Badge --}}
                                        <div class="absolute top-4 right-4">
                                            <span class="px-3 py-1 rounded-full bg-white/90 backdrop-blur shadow-sm text-[9px] font-black text-slate-800 uppercase tracking-widest border border-white">
                                                Gratuit
                                            </span>
                                        </div>
                                    </div>

                                    <div class="px-4 pb-4 flex flex-col flex-1">
                                        <h2 class="font-display font-bold text-midnight text-base leading-tight mb-2 group-hover:text-copper transition-colors line-clamp-2">
                                            {{ $guide->title }}
                                        </h2>
                                        <p class="text-sm text-slate-400 font-light line-clamp-2 mb-6 flex-1 italic leading-relaxed">
                                            {{ $guide->sous_titre }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                            <div class="flex items-center gap-1.5 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Accès immédiat
                                            </div>
                                            <div class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-copper group-hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    @if($guides->hasPages())
                    <div class="mt-16 flex justify-center">
                        {{ $guides->links('pagination::tailwind') }}
                    </div>
                    @endif
                @else
                    <div class="max-w-md mx-auto text-center py-20">
                        <div class="w-20 h-20 rounded-[2.5rem] bg-slate-50 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" /></svg>
                        </div>
                        <h2 class="font-display font-bold text-midnight text-xl mb-3">La bibliothèque se remplit...</h2>
                        <p class="text-slate-400 font-light">Nos experts rédigent actuellement les futurs guides stratégiques de Postelio. Revenez très bientôt.</p>
                    </div>
                @endif
            </div>
        </main>

        {{-- Footer (Matched with Blog) --}}
        <footer class="bg-white/60 backdrop-blur-md border-t border-slate-100 py-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-xs font-light text-slate-400">
                    &copy; {{ date('Y') }} Postelio. Un outil pensé pour les artisans passionnés.
                </div>
                <div class="flex items-center gap-6">
                    <a href="/" class="text-xs font-medium text-slate-400 hover:text-copper transition-colors">Accueil</a>
                    <a href="{{ route('blog.index') }}" class="text-xs font-medium text-slate-400 hover:text-copper transition-colors">Blog</a>
                    <a href="{{ route('guides.index') }}" class="text-xs font-medium text-slate-400 hover:text-copper transition-colors">Guides</a>
                    <a href="https://instagram.com/postelio.app" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-copper transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/company/postelio" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-copper transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
