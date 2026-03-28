<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog — Postelio | Conseils marketing pour artisans</title>
    <meta name="description" content="Découvrez nos conseils pour développer votre activité d'artisan grâce aux réseaux sociaux. Stratégies marketing, astuces et témoignages.">
    <meta name="keywords" content="artisan, marketing digital, réseaux sociaux, Instagram, communication artisan">

    <meta property="og:title" content="Blog Postelio — Conseils marketing pour artisans">
    <meta property="og:description" content="Découvrez nos conseils pour développer votre activité d'artisan grâce aux réseaux sociaux.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('blog.index') }}">
    <meta property="og:site_name" content="Postelio">

    <link rel="canonical" href="{{ route('blog.index') }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/postelio-logo-gradient.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-chalk text-slate-600 selection:bg-copper/30 selection:text-amber-900">

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-2">
                    <x-logo size="md" />
                </a>
                <div class="flex items-center gap-6">
                    <a href="/" class="text-sm font-medium text-slate-500 hover:text-midnight transition-colors">Accueil</a>
                    <a href="{{ route('blog.index') }}" class="text-sm font-bold text-midnight">Blog</a>
                    @if(auth()->check())
                        <a href="{{ route('dashboard') }}" class="btn-primary px-5 py-2 text-xs">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary px-5 py-2 text-xs">Se connecter</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <header class="py-20 sm:py-28 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-[20%] -right-[10%] w-[40%] h-[60%] bg-amber-200/20 rounded-full blur-[100px]"></div>
            <div class="absolute -bottom-[20%] -left-[10%] w-[40%] h-[60%] bg-orange-200/20 rounded-full blur-[100px]"></div>
        </div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-copper/10 text-copper text-xs font-bold uppercase tracking-widest mb-6">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Blog
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-bold tracking-tight text-midnight mb-6">
                Conseils & stratégies pour <span class="text-gradient-copper italic inline-block">artisans ambitieux.</span>
            </h1>
            <p class="text-lg sm:text-xl font-light text-slate-500 max-w-2xl mx-auto">
                Tout ce qu'il faut savoir pour transformer vos chantiers en machine à clients grâce aux réseaux sociaux.
            </p>
        </div>
    </header>

    {{-- Articles Grid --}}
    <main class="pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($posts->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <article class="group bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <a href="{{ route('blog.show', $post->slug) }}">
                                <div class="aspect-[16/10] overflow-hidden bg-slate-50">
                                    @if($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-copper/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <time datetime="{{ $post->published_at->toDateString() }}" class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                            {{ $post->published_at->translatedFormat('d M Y') }}
                                        </time>
                                        <span class="text-slate-200">|</span>
                                        <span class="text-[11px] font-bold text-slate-400">{{ $post->read_time }} min</span>
                                    </div>
                                    <h2 class="font-display font-bold text-midnight text-lg leading-snug mb-3 group-hover:text-copper transition-colors line-clamp-2">
                                        {{ $post->title }}
                                    </h2>
                                    <p class="text-sm text-slate-500 font-light leading-relaxed line-clamp-3">
                                        {{ $post->excerpt }}
                                    </p>
                                    <div class="mt-5 flex items-center gap-2 text-copper text-xs font-bold uppercase tracking-widest group-hover:gap-3 transition-all">
                                        Lire l'article
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                @if($posts->hasPages())
                <div class="mt-16 flex justify-center">
                    {{ $posts->links('pagination::tailwind') }}
                </div>
                @endif
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <h2 class="font-display font-bold text-midnight text-xl mb-2">Bientôt disponible</h2>
                    <p class="text-slate-400 font-light">Nos premiers articles arrivent très prochainement.</p>
                </div>
            @endif
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-xs font-light text-slate-400">
                &copy; {{ date('Y') }} Postelio. Un outil pensé pour les artisans passionnés.
            </div>
            <div class="flex items-center gap-6">
                <a href="/" class="text-xs font-medium text-slate-400 hover:text-copper transition-colors">Accueil</a>
                <a href="{{ route('blog.index') }}" class="text-xs font-medium text-slate-400 hover:text-copper transition-colors">Blog</a>
                <a href="https://instagram.com/postelio.app" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-copper transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                </a>
            </div>
        </div>
    </footer>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Blog",
        "name": "Blog Postelio",
        "description": "Conseils marketing pour artisans",
        "url": "{{ route('blog.index') }}",
        "publisher": {
            "@@type": "Organization",
            "name": "Postelio",
            "url": "{{ url('/') }}"
        }
    }
    </script>
</body>
</html>
