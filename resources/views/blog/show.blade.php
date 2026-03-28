<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->meta_title ?: $post->title }} — Postelio Blog</title>
    <meta name="description" content="{{ $post->meta_description ?: $post->excerpt }}">
    @if($post->meta_keywords)
    <meta name="keywords" content="{{ $post->meta_keywords }}">
    @endif

    <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta property="og:description" content="{{ $post->meta_description ?: $post->excerpt }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
    <meta property="og:site_name" content="Postelio">
    @if($post->featured_image)
    <meta property="og:image" content="{{ Storage::url($post->featured_image) }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta name="twitter:description" content="{{ $post->meta_description ?: $post->excerpt }}">
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
                    <a href="{{ route('blog.index') }}" class="text-sm font-medium text-slate-500 hover:text-midnight transition-colors">Blog</a>
                    @if(auth()->check())
                        <a href="{{ route('dashboard') }}" class="btn-primary px-5 py-2 text-xs">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary px-5 py-2 text-xs">Se connecter</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="pb-32">
        {{-- Article Header --}}
        <header class="pt-16 sm:pt-24 pb-12 relative">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-[20%] -right-[10%] w-[30%] h-[50%] bg-amber-200/15 rounded-full blur-[100px]"></div>
            </div>
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
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
            <div class="rounded-3xl overflow-hidden shadow-xl">
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                     class="w-full aspect-[21/9] object-cover">
            </div>
        </div>
        @endif

        {{-- Article Content --}}
        <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-lg prose-slate max-w-none
                        prose-headings:font-display prose-headings:font-bold prose-headings:text-midnight prose-headings:tracking-tight
                        prose-h2:text-2xl prose-h2:mt-12 prose-h2:mb-4
                        prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-3
                        prose-p:font-light prose-p:leading-relaxed prose-p:text-slate-600
                        prose-a:text-copper prose-a:font-medium prose-a:no-underline hover:prose-a:underline
                        prose-strong:text-midnight prose-strong:font-bold
                        prose-img:rounded-2xl prose-img:shadow-lg
                        prose-blockquote:border-copper prose-blockquote:bg-amber-50/30 prose-blockquote:rounded-r-2xl prose-blockquote:py-1
                        prose-ul:space-y-2 prose-ol:space-y-2
                        prose-li:text-slate-600 prose-li:font-light">
                {!! $post->content !!}
            </div>
        </article>

        {{-- Share & CTA --}}
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
            <div class="h-px bg-slate-100 mb-16"></div>

            {{-- CTA --}}
            <div class="bg-gradient-to-br from-midnight to-slate-800 rounded-3xl p-10 sm:p-14 text-center relative overflow-hidden">
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
                            <div class="aspect-[16/10] overflow-hidden bg-slate-50">
                                @if($article->featured_image)
                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
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
    <footer class="bg-white border-t border-slate-100 py-12">
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
    </script>
</body>
</html>
