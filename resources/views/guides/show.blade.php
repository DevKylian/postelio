<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $guide->title }} — Guide Gratuit Postelio</title>
    
    @php
        $rawDescription = $guide->meta_description ?: ($guide->sous_titre ?: $guide->contenu_html);
        $cleanDescription = str_replace('>', '> ', $rawDescription);
        $cleanDescription = htmlspecialchars_decode($cleanDescription);
        $cleanDescription = strip_tags($cleanDescription);
        $cleanDescription = preg_replace('/\s+/', ' ', $cleanDescription);
        $safeDescription = \Illuminate\Support\Str::limit(trim($cleanDescription), 155);
        $safeDescription = str_replace('"', '&quot;', $safeDescription);
    @endphp
    
    <meta name="description" content="{!! $safeDescription !!}">
    
    <meta property="og:title" content="{{ $guide->title }}">
    <meta property="og:description" content="{!! $safeDescription !!}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('guides.show', $guide->slug) }}">
    <meta property="og:site_name" content="Postelio">
    @if($guide->featured_image)
        <meta property="og:image" content="{{ Storage::url($guide->featured_image) }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $guide->title }}">
    <meta name="twitter:description" content="{!! $safeDescription !!}">
    @if($guide->featured_image)
        <meta name="twitter:image" content="{{ Storage::url($guide->featured_image) }}">
    @endif

    <link rel="canonical" href="{{ route('guides.show', $guide->slug) }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Umami Analytics -->
    @if(config('services.umami.id'))
        <script async src="{{ config('services.umami.url') }}/script.js" data-website-id="{{ config('services.umami.id') }}"></script>
    @endif

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


</head>
<body class="antialiased font-sans bg-chalk text-slate-600 selection:bg-copper/30 selection:text-amber-900 leading-normal">

    <div class="min-h-screen flex flex-col relative">
        {{-- Global Background Glows (Matched with Blog) --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute top-[10%] -right-[10%] w-[40%] h-[30%] bg-amber-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute top-[40%] -left-[10%] w-[40%] h-[30%] bg-orange-200/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[10%] right-[5%] w-[30%] h-[20%] bg-amber-100/10 rounded-full blur-[100px]"></div>
        </div>

        {{-- Navigation (Matched with Blog) --}}
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100 no-print" x-data="{ isMenuOpen: false }">
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

        <main class="flex-1 pb-32 relative z-10">
            {{-- Header (Editorial Style) --}}
            <header class="pt-16 sm:pt-24 pb-12 relative no-print">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
                    <a href="{{ route('guides.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-copper transition-colors mb-8">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                        Retour aux guides
                    </a>

                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-orange-600 text-[10px] font-black uppercase tracking-[0.15em] border border-orange-100">
                            Guide Stratégique Expert
                        </span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-bold tracking-tight text-midnight leading-tight mb-6 max-w-4xl">
                        {{ $guide->title }}
                    </h1>

                    <p class="text-xl font-light text-slate-500 leading-relaxed max-w-2xl italic">
                        {{ $guide->sous_titre }}
                    </p>
                </div>
            </header>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-16 relative">
                
                {{-- Sidebar (Conversion) --}}
                <aside class="lg:col-span-4 space-y-8 no-print order-2 lg:order-1">
                    <div class="sticky top-32 space-y-8">
                        {{-- Book Cover Preview --}}
                        <div class="relative aspect-[3/4] rounded-[2.5rem] overflow-hidden bg-white shadow-2xl shadow-orange-500/5 border-8 border-white ring-1 ring-slate-900/5 group">
                            @if($guide->featured_image)
                                <img src="{{ Storage::url($guide->featured_image) }}" alt="{{ $guide->title }}" class="w-full h-full object-cover">
                                <div class="absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-black/20 via-black/5 to-transparent"></div>
                            @endif
                        </div>

                        {{-- Premium Conversion Card --}}
                        <div class="bg-gradient-to-br from-midnight to-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-midnight/30">
                            <div class="absolute -top-12 -right-12 w-40 h-40 bg-orange-500/20 rounded-full blur-3xl"></div>
                            
                            <div class="relative z-10 space-y-6">
                                <x-logo size="md" class="scale-110 origin-left" />
                                
                                <div class="space-y-4">
                                    <h4 class="font-display font-bold text-xl leading-tight">
                                        Postelio automatise ces stratégies pour vous.
                                    </h4>
                                    <p class="text-sm text-slate-400 font-light leading-relaxed">
                                        Arrêtez de perdre du temps en rédaction. Postelio transforme vos chantiers en machine à clients.
                                    </p>
                                </div>

                                <div class="pt-4 space-y-4">
                                    <a href="{{ route('register') }}" class="flex items-center justify-center gap-3 w-full py-4 bg-orange-500 text-white rounded-2xl text-xs font-black uppercase tracking-[0.15em] hover:bg-orange-600 transition-all shadow-xl shadow-orange-500/20 active:scale-95">
                                        Commencer ici
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                    <p class="text-center text-[10px] text-slate-500 font-bold uppercase tracking-widest">Compte gratuit  — Sans engagement</p>
                                </div>
                            </div>
                        </div>

                        {{-- Other Guides --}}
                        @if($otherGuides->count())
                        <div class="space-y-6">
                            <h5 class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Continuer la lecture</h5>
                            <div class="space-y-4">
                                @foreach($otherGuides as $other)
                                    <a href="{{ route('guides.show', $other->slug) }}" class="group flex items-center gap-4 bg-white p-3 rounded-2xl border border-slate-100 hover:border-orange-500/20 hover:shadow-lg transition-all">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-slate-50">
                                            @if($other->featured_image)
                                                <img src="{{ Storage::url($other->featured_image) }}" alt="" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h6 class="text-sm font-bold text-midnight truncate leading-tight group-hover:text-orange-500 transition-colors">{{ $other->title }}</h6>
                                            <p class="text-[10px] text-slate-400 font-medium italic truncate mt-1">Guide Expert</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </aside>

                {{-- Content --}}
                <div class="lg:col-span-8 order-1 lg:order-2">
                    <div class="bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-16 shadow-sm border border-slate-100 guide-container relative">
                        
                        @php
                            $isUnlocked = !$guide->requires_email || auth()->check() || session()->has('unlocked_guide_'.$guide->id);
                            $displayContent = $guide->contenu_html;
                            
                            if (!$isUnlocked) {
                                // Cut AFTER the first H2 if it exists
                                $parts = preg_split('/(<h2.*?>.*?<\/h2>)/si', $displayContent, 2, PREG_SPLIT_DELIM_CAPTURE);
                                
                                if (count($parts) >= 2 && !empty(trim(strip_tags($parts[1])))) {
                                    $displayContent = $parts[0] . $parts[1];
                                } else {
                                    // Fallback: Extract first 3 block elements (p, h3, ul, ol)
                                    preg_match_all('/<(p|h3|ul|ol).*?>.*?<\/\1>/si', $displayContent, $matches);
                                    if (!empty($matches[0])) {
                                        $displayContent = implode('', array_slice($matches[0], 0, 3));
                                    } else {
                                        $displayContent = \Illuminate\Support\Str::limit(strip_tags($displayContent), 500);
                                    }
                                }
                            }
                        @endphp

                        {{-- Final Content Display --}}
                        <div class="blog-content relative">
                            {!! $displayContent !!}

                            @if(!$isUnlocked)
                                {{-- Fade effect --}}
                                <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-white via-white/80 to-transparent z-10"></div>
                            @endif
                        </div>

                        @if(!$isUnlocked)
                        {{-- Gating Section (Integrated in flow) --}}
                        <div class="mt-8 relative z-20">
                            <div class="max-w-xl mx-auto bg-midnight rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-12 shadow-2xl text-white relative overflow-hidden border border-white/5">
                                {{-- Background Sparkle --}}
                                <div class="absolute -top-10 -right-10 w-40 h-40 bg-orange-500/20 rounded-full blur-3xl"></div>
                                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl"></div>
                                
                                <div class="relative z-10 text-center space-y-8">
                                    <div class="w-20 h-20 bg-white/10 rounded-3xl flex items-center justify-center mx-auto ring-1 ring-white/20">
                                        <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <h3 class="font-display font-bold text-2xl sm:text-4xl text-white tracking-tight">Accédez à la suite</h3>
                                        <p class="text-slate-400 font-light leading-relaxed max-w-sm mx-auto">
                                            Rejoignez les artisans qui réussissent et déverrouillez ce guide complet gratuitement.
                                        </p>
                                    </div>

                                    <form action="{{ route('guides.subscribe', $guide->slug) }}" method="POST" class="space-y-4 pt-2">
                                        @csrf
                                        <div class="relative group">
                                            <input type="email" name="email" required placeholder="Votre adresse email" 
                                                   class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 text-base focus:outline-none focus:border-orange-500/50 focus:ring-4 focus:ring-orange-500/10 transition-all text-white placeholder-slate-500 text-center group-hover:bg-white/10">
                                        </div>
                                        <button type="submit" class="w-full py-4 sm:py-5 bg-orange-500 hover:bg-orange-600 text-white rounded-2xl text-xs sm:text-sm font-black uppercase tracking-[0.1em] sm:tracking-[0.2em] transition-all shadow-xl shadow-orange-500/20 active:scale-[0.98] group flex items-center justify-center gap-2 sm:gap-3">
                                            Déverrouiller le guide
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                        </button>
                                    </form>

                                    <div class="flex items-center justify-center gap-4 pt-2">
                                        <div class="flex -space-x-3">
                                            <img src="{{ asset('images/customers/1.png') }}" class="w-10 h-10 rounded-full border-2 border-midnight object-cover">
                                            <img src="{{ asset('images/customers/2.png') }}" class="w-10 h-10 rounded-full border-2 border-midnight object-cover">
                                            <img src="{{ asset('images/customers/3.png') }}" class="w-10 h-10 rounded-full border-2 border-midnight object-cover">
                                        </div>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest text-left leading-tight">Déjà lu par<br>+500 artisans</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($isUnlocked)
                        {{-- Final CTA (Only for unlocked) - Animated Premium Block --}}
                        <div class="mt-12 pt-8 border-t border-slate-50 no-print"
                             x-data="{
                                step: 1,
                                text: '',
                                fullText: '« Installation d\'une nouvelle cuisine sur-mesure à Bordeaux, client ravi ! »',
                                typeWriterInterval: null,
                                activeImage: 1,
                                startLoop() {
                                    this.step = 1;
                                    this.text = '';
                                    this.activeImage = 1;
                                    let i = 0;
                                    clearInterval(this.typeWriterInterval);
                                    
                                    // Start typing after a small delay
                                    setTimeout(() => {
                                        this.typeWriterInterval = setInterval(() => {
                                            if(i < this.fullText.length) {
                                                this.text += this.fullText.charAt(i);
                                                i++;
                                            } else {
                                                clearInterval(this.typeWriterInterval);
                                            }
                                        }, 40); // typing speed
                                    }, 500);

                                    // Etape 1 -> Dure 4s
                                    setTimeout(() => {
                                        this.step = 2; // Etape 2 (Resultat)
                                        
                                        setTimeout(() => {
                                            this.activeImage = 2;
                                        }, 2000);

                                        // Etape 2 -> Dure 5s
                                        setTimeout(() => {
                                            this.startLoop();
                                        }, 5000); 
                                    }, 4000); 
                                }
                             }"
                             x-init="startLoop()">
                            
                            <div class="max-w-3xl mx-auto bg-slate-900 rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-14 shadow-2xl relative overflow-hidden border border-slate-700/50">
                                {{-- Glassmorphism Glows --}}
                                <div class="absolute -top-32 -right-32 w-64 h-64 bg-amber-500/20 rounded-full blur-[100px]"></div>
                                <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-slate-500/20 rounded-full blur-[100px]"></div>

                                <div class="relative z-10 flex flex-col items-center text-center">
                                    
                                    {{-- En-tête --}}
                                    <h3 class="font-display font-bold text-2xl sm:text-4xl text-white tracking-tight mb-3 sm:mb-4 leading-tight px-2 sm:px-0">
                                        Arrêtez de perdre vos soirées sur Instagram.
                                    </h3>
                                    <p class="text-slate-400 font-light text-base sm:text-lg mb-8 sm:mb-12 px-2 sm:px-0">
                                        Prenez une photo. Parlez 10 secondes. On s'occupe du reste.
                                    </p>

                                    {{-- Zone d'Animation Centrale --}}
                                    <div class="relative w-full max-w-sm h-[420px] mx-auto mb-8 sm:mb-12 flex items-center justify-center">
                                        
                                        {{-- ÉTAPE 1 : Le Micro + Typewrite --}}
                                        <div x-show="step === 1" 
                                             x-transition:enter="transition ease-out duration-500"
                                             x-transition:enter-start="opacity-0 scale-90"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-300"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-90"
                                             class="absolute inset-0 flex flex-col items-center justify-center gap-6" style="display: none;">
                                            
                                            <div class="relative flex items-center justify-center mb-4">
                                                <div class="absolute w-28 h-28 bg-amber-500/20 rounded-full animate-ping" style="animation-duration: 2s;"></div>
                                                <div class="w-20 h-20 bg-gradient-to-tr from-amber-600 to-amber-400 rounded-full flex items-center justify-center text-white shadow-xl shadow-amber-500/30 z-10 relative">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                                </div>
                                            </div>

                                            {{-- Typewrite Bubble --}}
                                            <div class="w-full max-w-[280px] bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5 min-h-[80px] flex items-center justify-center relative shadow-lg shadow-black/5">
                                                <p class="text-amber-100 text-sm font-medium leading-relaxed italic text-center" x-text="text"></p>
                                            </div>
                                            
                                            <div class="flex items-center gap-1 mt-2">
                                                <div class="w-1 h-3 bg-amber-500/60 rounded-full animate-pulse"></div>
                                                <div class="w-1 h-5 bg-amber-500/80 rounded-full animate-pulse delay-75"></div>
                                                <div class="w-1 h-8 bg-amber-400 rounded-full animate-pulse delay-150"></div>
                                                <div class="w-1 h-4 bg-amber-500/60 rounded-full animate-pulse delay-200"></div>
                                                <div class="w-1 h-6 bg-amber-500/80 rounded-full animate-pulse delay-300"></div>
                                            </div>
                                        </div>

                                        {{-- ÉTAPE 2 : Résultat Final --}}
                                        <div x-show="step === 2" 
                                             x-transition:enter="transition ease-out duration-700 delay-200"
                                             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave="transition ease-in duration-300"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                                             class="absolute inset-0 flex items-center justify-center" style="display: none;">
                                             
                                            <div class="w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-2xl relative text-left">
                                                <div class="absolute -top-3 -right-3 bg-emerald-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-lg border border-white flex items-center gap-1 z-20">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    Publié sur Insta
                                                </div>
                                                
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-xs shadow-inner">
                                                        P
                                                    </div>
                                                    <div class="space-y-0.5">
                                                        <div class="text-[11px] font-bold text-slate-800 leading-none">Votre Entreprise</div>
                                                        <div class="text-[9px] text-slate-400 leading-none">Bordeaux, France</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="w-full aspect-square bg-slate-100 rounded-xl mb-3 overflow-hidden border border-slate-100 relative">
                                                    <div class="flex w-[200%] h-full transition-transform duration-500 ease-in-out" :style="`transform: translateX(${activeImage === 1 ? '0' : '-50%'})`">
                                                        <div class="w-1/2 h-full flex-shrink-0">
                                                            <img src="{{ asset('images/demo/result.png') }}" alt="Avant" class="w-full h-full object-cover">
                                                        </div>
                                                        <div class="w-1/2 h-full flex-shrink-0">
                                                            <img src="{{ asset('images/demo/result2.png') }}" alt="Après" class="w-full h-full object-cover">
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Swipe Indicators --}}
                                                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1 z-10">
                                                        <div class="w-1.5 h-1.5 rounded-full transition-colors duration-300 shadow-sm" :class="activeImage === 1 ? 'bg-white' : 'bg-white/50'"></div>
                                                        <div class="w-1.5 h-1.5 rounded-full transition-colors duration-300 shadow-sm" :class="activeImage === 2 ? 'bg-white' : 'bg-white/50'"></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="space-y-1.5">
                                                    <p class="text-[10px] text-slate-700 leading-snug">
                                                        <span class="font-bold mr-1">votre_entreprise</span>
                                                        Installation terminée ! Une nouvelle cuisine moderne et fonctionnelle pour des moments conviviaux garantis. 🍳✨
                                                    </p>
                                                    <div class="flex flex-wrap gap-1 pt-1">
                                                        <span class="text-[9px] font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">#Cuisine</span>
                                                        <span class="text-[9px] font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">#Bordeaux</span>
                                                        <span class="text-[9px] font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">#Artisan</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Bouton d'Action --}}
                                    <div class="flex flex-col items-center w-full px-4 sm:px-0">
                                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-3 px-6 py-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-xl font-bold text-sm hover:scale-105 transition-transform duration-300 shadow-xl shadow-amber-500/20 active:scale-95 group whitespace-nowrap">
                                            Tester gratuitement
                                        </a>
                                        <p class="mt-4 text-[11px] text-slate-500 font-bold uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            100% gratuit
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>

        {{-- Footer (Matched with Blog) --}}
        <footer class="bg-white/60 backdrop-blur-md border-t border-slate-100 py-12 relative z-10 no-print">
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
