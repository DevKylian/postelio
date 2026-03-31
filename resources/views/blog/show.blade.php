<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->meta_title ?: $post->title }} — Postelio Blog</title>
    @php
        $rawDescription = $post->meta_description ?: ($post->excerpt ?: $post->content);
        // 1. Add spaces between tags to avoid word gluing (e.g. </h2>Ton -> </h2> Ton)
        $cleanDescription = str_replace('>', '> ', $rawDescription);
        // 2. Decode entities (so that &lt; becomes < and &#039; becomes ')
        $cleanDescription = htmlspecialchars_decode($cleanDescription);
        // 3. Strip tags to get plain text
        $cleanDescription = strip_tags($cleanDescription);
        // 4. Normalize whitespace (no tabs, newlines or double spaces)
        $cleanDescription = preg_replace('/\s+/', ' ', $cleanDescription);
        // 5. Limit and trim
        $cleanDescription = \Illuminate\Support\Str::limit(trim($cleanDescription), 155);
        // 6. Escape ONLY double quotes for the content="" attribute
        $safeDescription = str_replace('"', '&quot;', $cleanDescription);
    @endphp
    <meta name="description" content="{!! $safeDescription !!}">
    @if($post->meta_keywords)
    <meta name="keywords" content="{{ $post->meta_keywords }}">
    @endif

    <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta property="og:description" content="{!! $safeDescription !!}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
    <meta property="og:site_name" content="Postelio">
    @if($post->featured_image)
    <meta property="og:image" content="{{ Storage::url($post->featured_image) }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta name="twitter:description" content="{!! $safeDescription !!}">
    @if($post->featured_image)
    <meta name="twitter:image" content="{{ Storage::url($post->featured_image) }}">
    @endif

    <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">

    <link rel="canonical" href="{{ route('blog.show', $post->slug) }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/postelio-logo-gradient.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

        <main class="flex-1 pb-32 relative z-10">
            {{-- Article Header --}}
            <header class="pt-16 sm:pt-24 pb-12 relative">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-copper transition-colors mb-8">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                        Retour au blog
                    </a>

                    <div class="flex items-center gap-4 mb-6">
                        <time datetime="{{ $post->published_at->toDateString() }}" class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                            {{ $post->published_at->translatedFormat('d F Y') }}
                        </time>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ $post->read_time }} min de lecture</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-bold tracking-tight text-midnight leading-tight mb-6">
                        {{ $post->title }}
                    </h1>

                    @if($post->excerpt)
                    <p class="text-lg font-light text-slate-500 leading-relaxed">
                        {{ $post->excerpt }}
                    </p>
                    @endif
                </div>
            </header>

            {{-- Featured Image --}}
            @if($post->featured_image)
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
                <div class="rounded-3xl overflow-hidden shadow-xl shadow-midnight/5">
                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                         class="w-full aspect-[21/9] object-cover">
                </div>
            </div>
            @endif

            {{-- Article Content --}}
            <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="blog-content">
                    @php
                        $ctaBlock = '
                        <div class="my-10 p-[3px] rounded-[24px] bg-gradient-to-r from-copper to-amber-400 shadow-xl shadow-amber-500/10 not-prose transition-transform duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-amber-500/20">
                            <div class="relative bg-white rounded-[21px] px-5 py-6 sm:p-8 flex flex-col lg:flex-row items-center gap-4 lg:gap-8 h-full overflow-visible">
                                
                                <div class="flex-shrink-0 w-14 h-14 lg:w-16 lg:h-16 bg-amber-50 rounded-2xl flex items-center justify-center text-copper border border-amber-100/50">
                                    <svg class="w-7 h-7 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                
                                <div class="flex-1 text-center lg:text-left">
                                    <h3 class="text-xl sm:text-2xl font-display font-medium !text-midnight !mt-0 !mb-2.5">
                                        Vos chantiers sont votre meilleure pub.
                                    </h3>
                                    <p class="!text-slate-500 font-light text-sm sm:text-base leading-relaxed !mb-0">
                                        Arrêtez de perdre vos soirées sur les réseaux. Prenez une simple photo, <strong class="!text-midnight font-medium">Postelio rédige et publie</strong> pour vous. Attirez enfin de vrais clients locaux.
                                    </p>
                                </div>
                                
                                <div class="flex-shrink-0 relative flex flex-col items-center justify-center w-full lg:w-auto mt-2 lg:mt-0 lg:ml-auto">
                                    <a href="'.route('register').'" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-copper !text-white font-bold text-sm uppercase tracking-widest rounded-xl hover:bg-amber-600 transition-all shadow-md shadow-copper/20 relative z-10">
                                        Créer un compte
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                    <p class="lg:absolute lg:top-full mt-3 lg:mt-2 w-full text-center !text-[11px] !text-slate-400 font-bold uppercase tracking-widest whitespace-nowrap">
                                        100% Gratuit
                                    </p>
                                </div>
                            </div>
                        </div>';

                        // Remplacement intelligent du shortcode
                        $parsedContent = str_replace('<p>[CTA]</p>', $ctaBlock, $post->content);
                        $parsedContent = str_replace('[CTA]', $ctaBlock, $parsedContent);
                    @endphp
                    {!! $parsedContent !!}
                </div>
            </article>

            {{-- Share & CTA --}}
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
                <div class="h-px bg-slate-100 mb-16"></div>

                {{-- CTA --}}
                <div class="bg-gradient-to-br from-midnight to-slate-800 rounded-3xl p-10 sm:p-14 text-center relative overflow-hidden shadow-2xl shadow-midnight/20">
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute -top-[30%] -right-[10%] w-[40%] h-[60%] bg-copper/10 rounded-full blur-[80px]"></div>
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-2xl sm:text-3xl font-display font-bold text-white mb-4">
                            Prêt à passer à l'action ?
                        </h2>
                        <p class="text-slate-300 font-light mb-8 max-w-lg mx-auto">
                            Rejoignez les artisans qui utilisent Postelio pour transformer leurs chantiers en clients.
                        </p>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-copper text-white font-bold text-sm uppercase tracking-widest rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-copper/30">
                            Essayer gratuitement
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Related Articles --}}
            @if($related->count())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20">
                <h2 class="font-display font-bold text-midnight text-2xl tracking-tight mb-10 text-center">Articles similaires</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    @foreach($related as $article)
                        <article class="group bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <a href="{{ route('blog.show', $article->slug) }}">
                                <div class="overflow-hidden bg-slate-50" style="aspect-ratio: 16/8">
                                    @if($article->featured_image)
                                        <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}"
                                             class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700 [backface-visibility:hidden] [transform:translateZ(0)]">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-copper/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-6">
                                    <time class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ $article->published_at->translatedFormat('d M Y') }}</time>
                                    <h3 class="font-display font-bold text-midnight text-base leading-snug mt-3 mb-2 group-hover:text-copper transition-colors line-clamp-2">
                                        {{ $article->title }}
                                    </h3>
                                    <p class="text-sm text-slate-500 font-light line-clamp-2">{{ $article->excerpt }}</p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
            @endif
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
                </div>
            </div>
        </footer>
    </div>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BlogPosting",
        "headline": "{{ $post->meta_title ?: $post->title }}",
        "description": "{{ $post->meta_description ?: $post->excerpt }}",
        "url": "{{ route('blog.show', $post->slug) }}",
        @if($post->featured_image)
        "image": "{{ Storage::url($post->featured_image) }}",
        @endif
        "datePublished": "{{ $post->published_at->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}",
        "author": {
            "@@type": "Organization",
            "name": "Postelio"
        },
        "publisher": {
            "@@type": "Organization",
            "name": "Postelio",
            "url": "{{ url('/') }}"
        },
        "mainEntityOfPage": {
            "@@type": "WebPage",
            "@@id": "{{ route('blog.show', $post->slug) }}"
        }
    }
    <!-- Umami Event Tracking (Blog Views) -->
    @if(config('services.umami.id'))
    <script>
        window.addEventListener('load', () => {
            if (window.umami) {
                umami.track('view_blog_post', { 
                    title: {!! json_encode($post->title) !!}, 
                    id: {{ $post->id }} 
                });
            }
        });
    </script>
    @endif
</body>
</html>
