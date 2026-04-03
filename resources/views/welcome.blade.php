@php
    $announcementText = \App\Models\Setting::get('announcement_text', 'Recherche 10 artisans pour tester gratuitement l\'app !');
    $announcementLinkText = \App\Models\Setting::get('announcement_link_text', 'Rejoindre le test →');
    $announcementRoute = \App\Models\Setting::get('announcement_route', 'register');
    $announcementActive = \App\Models\Setting::get('announcement_is_active', true);
    try {
        $announcementUrl = route($announcementRoute);
    } catch (\Exception $e) {
        $announcementUrl = route('register');
    }
@endphp
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Postelio — Votre chantier travaille pour vous</title>
        <meta name="description" content="Postelio — La solution IA pour les artisans. Transformez vos chantiers en posts Instagram parfaits en 30 secondes grâce au vocal. Automatisez votre marketing local dès aujourd'hui.">
        <link rel="canonical" href="{{ url()->current() }}">

        <script type="application/ld+json">
        {
          "@@context": "https://schema.org",
          "@@type": "Organization",
          "name": "Postelio",
          "url": "{{ config('app.url') }}",
          "logo": "{{ asset('images/postelio-logo-gradient.svg') }}",
          "sameAs": [
            "https://instagram.com/postelio.app"
          ],
          "contactPoint": {
            "@@type": "ContactPoint",
            "email": "hello@postelio.fr",
            "contactType": "customer support"
          }
        }
        </script>
        <script type="application/ld+json">
        {
          "@@context": "https://schema.org",
          "@@type": "WebSite",
          "name": "Postelio",
          "url": "{{ config('app.url') }}",
          "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ config('app.url') }}/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
        </script>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/postelio-logo-gradient.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/postelio-logo-gradient.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Umami Analytics -->
    @if(config('services.umami.id'))
        <script async src="{{ config('services.umami.url') }}/script.js" data-website-id="{{ config('services.umami.id') }}"></script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js Plugins + Alpine.js -->
        <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://unpkg.com/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>
    </head>
    <body class="antialiased font-sans bg-chalk text-slate-600 selection:bg-copper/30 selection:text-amber-900 overflow-x-hidden relative"
          x-data="{ 
            isMenuOpen: false, 
            scrolled: false, 
            openFaq: null,
            showAnnouncement: {{ $announcementActive ? 'true' : 'false' }},
            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 50;
                });
            }
          }">
      
      <!-- Announcement Bar -->
      <div x-show="showAnnouncement" 
           x-transition:enter="transition ease-out duration-500"
           x-transition:enter-start="opacity-0 translate-y-4 md:translate-y-10"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="opacity-100 translate-y-0"
           x-transition:leave-end="opacity-0 translate-y-4 md:translate-y-10"
           class="md:fixed md:bottom-8 fixed md:top-auto top-0 left-0 md:left-1/2 md:-translate-x-1/2 z-[100] w-full md:w-auto"
           style="display: none;">
        <div class="bg-amber-600 rounded-none md:rounded-full py-2.5 px-4 md:px-10 flex items-center justify-between md:justify-center gap-6 shadow-2xl shadow-amber-900/20">
          <div class="flex items-center gap-3 text-white text-center md:text-left w-full md:w-auto">
            <span class="hidden md:flex h-2.5 w-2.5 relative flex-shrink-0">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
            </span>
            <p class="text-[11px] md:text-sm font-bold tracking-wide flex-1">
              {{ $announcementText }} <a href="{{ $announcementUrl }}" class="underline decoration-white/40 hover:decoration-white transition-all ml-1 underline-offset-2">{{ $announcementLinkText }}</a>
            </p>
          </div>
          <button @click="showAnnouncement = false" class="text-white/70 hover:text-white transition-colors p-1 bg-white/10 rounded-full flex-shrink-0">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
      
      <!-- Background Decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[10%] -left-[10%] w-[800px] h-[800px] bg-amber-200/40 rounded-full blur-[120px] opacity-40"></div>
      <div class="absolute top-[40%] -right-[15%] w-[700px] h-[700px] bg-orange-200/20 rounded-full blur-[100px] opacity-30"></div>
      <div class="absolute -bottom-[20%] left-[20%] w-[600px] h-[600px] bg-amber-100/30 rounded-full blur-[100px] opacity-20"></div>
    </div>

      <!-- Navigation -->
      <nav class="fixed inset-x-0 z-50 transition-all duration-500 flex justify-center px-4"
           :class="showAnnouncement ? 'top-[56px] md:top-4' : 'top-4'">
        <div class="w-full max-w-5xl transition-all duration-500 rounded-full px-4 sm:px-6 py-3 flex justify-between items-center border glass-card bg-white/70"
             :class="scrolled ? 'bg-white/95 shadow-lg' : 'bg-white/70 shadow-sm'">
          <a href="/" class="transition-opacity hover:opacity-80">
            <x-logo size="md" />
          </a>
          
          <!-- Desktop Nav -->
          <div class="hidden md:flex items-center gap-8">
            <a href="#methode" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">La méthode</a>
            <a href="#galerie" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">Galerie</a>
            <a href="#benefices" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">Avantages</a>
            <a href="#tarifs" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">Tarifs</a>
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">Blog</a>

            @auth
                <a href="{{ route('dashboard') }}" wire:navigate class="btn-primary ml-4 text-sm px-6 py-2.5">
                    Tableau de Bord
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">Connexion</a>
                <a href="{{ route('register') }}" wire:navigate class="btn-primary ml-4 text-sm px-6 py-2.5">
                    Essayer gratuitement
                </a>
            @endauth
          </div>

          <div class="md:hidden flex items-center">
            <button @click="isMenuOpen = !isMenuOpen" class="text-midnight p-2 outline-none focus:outline-none">
                <i data-lucide="menu" class="w-6 h-6" x-show="!isMenuOpen"></i>
                <i data-lucide="x" class="w-6 h-6" x-show="isMenuOpen" style="display: none;"></i>
            </button>
          </div>
        </div>

        <!-- Mobile Nav -->
        <div x-show="isMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             @click.away="isMenuOpen = false"
             class="absolute top-20 left-4 right-4 bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-2xl z-40 outline-none"
             style="display: none;">
          <div class="px-4 py-6 space-y-2">
            <a href="#methode" @click="isMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-midnight transition-colors">La méthode</a>
            <a href="#galerie" @click="isMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-midnight transition-colors">Galerie</a>
            <a href="#benefices" @click="isMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-midnight transition-colors">Avantages</a>
            <a href="#tarifs" @click="isMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-midnight transition-colors">Tarifs</a>
            <a href="{{ route('blog.index') }}" @click="isMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-midnight transition-colors">Blog</a>

            @auth
                <a href="{{ route('dashboard') }}" wire:navigate class="block w-full text-center btn-primary mt-4">
                    Tableau de Bord
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="block px-4 py-3 rounded-2xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-midnight transition-colors">Connexion</a>
                <a href="{{ route('register') }}" wire:navigate class="block w-full text-center btn-primary mt-4">
                    Essayer gratuitement
                </a>
            @endauth
          </div>
        </div>
      </nav>

      <main class="relative z-10">
        <!-- Hero Section -->
        <section class="relative pt-40 pb-20 lg:pt-52 lg:pb-32">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto"
                 x-intersect="$el.classList.add('animate-fade-up')">
                <div class="badge badge-copper gap-2 mb-8 shadow-sm">
                  L'appli des artisans fiers de leur travail.
                </div>
              <h1 class="text-4xl sm:text-6xl lg:text-7xl font-display font-semibold tracking-tighter text-midnight mb-8 leading-[1.05]">
                Ne bradez plus vos devis <br class="hidden sm:block" />
                <span class="text-gradient-copper italic">à cause de mauvaises photos.</span>
              </h1>
              <p class="text-xl sm:text-2xl font-light text-slate-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                10 secondes pour transformer vos fins de chantier en vitrine premium. Attirez de vrais clients, sans y passer vos soirées.
              </p>
              <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @auth
                    <a href="{{ route('generate-post') }}" wire:navigate class="btn-primary w-full sm:w-auto flex justify-center items-center gap-2 text-base">
                        Créer mon 1er post (Gratuit)
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" wire:navigate class="btn-primary w-full sm:w-auto flex justify-center items-center gap-2 text-base">
                        Créer mon 1er post (Gratuit)
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @endauth
                <a href="#methode" class="btn-secondary w-full sm:w-auto flex justify-center items-center text-base font-semibold">
                  Découvrir la méthode
                </a>
              </div>
              <div class="flex flex-col items-center gap-6 mt-10">
                <div class="flex items-center gap-3">
                  <div class="flex -space-x-3">
                    @foreach(range(1, 4) as $i)
                      <img src="{{ asset('images/customers/'.$i.'.png') }}" alt="Artisan {{ $i }}" class="w-9 h-9 rounded-full border-2 border-slate-50 object-cover shadow-md hover:scale-110 transition-transform relative z-[{{ 10 - $i }}]">
                    @endforeach
                  </div>
                  <div class="text-sm text-slate-600">
                    <div class="flex items-center gap-1 mb-0.5">
                        @foreach(range(1, 5) as $star)
                            <svg class="w-3 h-3 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        @endforeach
                    </div>
                    <span class="font-bold text-midnight">+200 avis artisans</span> certifiés
                  </div>
                </div>
                <div class="flex flex-wrap justify-center items-center gap-4 text-xs font-medium text-slate-400 uppercase tracking-widest">
                  <span class="flex items-center gap-1.5"><i data-lucide="check" class="w-4 h-4 text-copper"></i> Offre gratuite disponible</span>
                  <span class="hidden sm:block">&bull;</span>
                  <span class="flex items-center gap-1.5"><i data-lucide="check" class="w-4 h-4 text-copper"></i> Sans engagement</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Motion Design Demo -->
        <style>
          .md-showcase{position:relative;width:100%;min-height:560px;border-radius:1.5rem;overflow:hidden;background:linear-gradient(145deg,#ffffff 0%,#f8fafc 100%);border:1px solid rgba(226,232,240,0.6);box-shadow:0 25px 60px -12px rgba(0,0,0,0.08),0 0 0 1px rgba(0,0,0,0.02)}
          .md-scene{position:absolute;inset:0;display:flex;flex-direction:column;opacity:0;transform:translateY(15px);pointer-events:none;transition:all 0.6s cubic-bezier(0.34,1.56,0.64,1)}
          .md-scene.active{opacity:1;transform:translateY(0);pointer-events:auto}
          .md-topbar{display:flex;align-items:center;gap:8px;padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;flex-shrink:0}
          .md-dot{width:8px;height:8px;border-radius:50%}
          .md-content{flex:1;display:flex;align-items:center;justify-content:center;padding:2rem;position:relative}
          .md-cursor{position:absolute;width:20px;height:20px;z-index:50;pointer-events:none;filter:drop-shadow(0 4px 6px rgba(0,0,0,0.3));transform-origin:top left}
          .md-click{position:absolute;width:40px;height:40px;border-radius:50%;background:rgba(217,119,6,0.2);border:2px solid #d97706;pointer-events:none;z-index:49;opacity:0;transform:translate(-50%,-50%) scale(0)}
          .md-click.pop{animation:mdPop 0.6s cubic-bezier(0.25,1,0.5,1) forwards}
          @keyframes mdPop{0%{opacity:1;transform:translate(-50%,-50%) scale(0.3);border-width:4px}100%{opacity:0;transform:translate(-50%,-50%) scale(2);border-width:0px}}
          .md-mic{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;color:white;box-shadow:0 8px 30px rgba(245,158,11,0.4);position:relative;transition:all 0.3s}
          .md-mic.rec{transform:scale(1.05);box-shadow:0 10px 40px rgba(245,158,11,0.6)}
          .md-mic.rec::after{content:'';position:absolute;inset:-8px;border-radius:50%;border:2px solid rgba(245,158,11,0.5);animation:mdPulse 1s cubic-bezier(0.4,0,0.2,1) infinite}
          @keyframes mdPulse{0%{transform:scale(1);opacity:1}100%{transform:scale(1.4);opacity:0}}
          .md-wave{display:flex;align-items:center;gap:3px;height:48px;justify-content:center}
          .md-wave-bar{width:4px;border-radius:4px;background:#f59e0b;transition:height 0.15s ease}
          .md-transcript{background:#f8fafc;border-radius:1rem;border:1px solid #e2e8f0;padding:12px 16px;font-size:14px;color:#1e293b;line-height:1.6;font-style:italic;width:100%;max-width:380px}
          .md-types{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;width:100%;max-width:420px}
          .md-type{display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 8px;border-radius:12px;border:2px solid #e2e8f0;background:white;transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1)}
          .md-type.sel{border-color:#f59e0b;background:#fffbeb;box-shadow:0 0 0 3px rgba(245,158,11,0.15);transform:scale(1.05)}
          .md-type-ico{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#64748b;transition:all 0.3s}
          .md-type.sel .md-type-ico{background:#fef3c7;color:#d97706}
          .md-type-lbl{font-size:10px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;text-align:center;transition:all 0.3s}
          .md-type.sel .md-type-lbl{color:#d97706}
          .md-upload{width:100%;max-width:280px;aspect-ratio:1;border:2px dashed #cbd5e1;border-radius:16px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;position:relative;overflow:hidden;background:#fafbfc;transition:all 0.3s}
          .md-upload.sm{max-width:140px;width:100%;aspect-ratio:3.5/5;border-radius:12px;flex-shrink:0}
          .md-upload-row{display:flex !important;flex-direction:row !important;gap:16px;justify-content:center;width:100%;max-width:400px}
          .md-upload.filled{border-style:solid;border-color:#f59e0b}
          .md-upload img{position:absolute;inset:0;border-radius:12px;object-fit:cover;opacity:0;transform:scale(0.9)}
          .md-upload img.show{animation:mdDrop 0.6s cubic-bezier(0.34,1.56,0.64,1) forwards}
          @keyframes mdDrop{to{opacity:1;transform:scale(1)}}
          .md-spinner{width:64px;height:64px;border-radius:50%;border:4px solid #f8fafc;border-top-color:#d97706;animation:mdSpin 1s cubic-bezier(0.68,-0.55,0.26,1.55) infinite;box-shadow:0 0 20px rgba(217,119,6,0.15)}
          @keyframes mdSpin{to{transform:rotate(360deg)}}
          .md-sparkle{position:absolute;width:6px;height:6px;background:#d97706;border-radius:50%;opacity:0;box-shadow:0 0 10px #f59e0b}
          .md-sparkle:nth-child(1){left:25%;top:25%;animation:mdFloatSparkle 2s 0.1s ease infinite}
          .md-sparkle:nth-child(2){left:75%;top:20%;animation:mdFloatSparkle 2s 0.4s ease infinite}
          .md-sparkle:nth-child(3){left:35%;top:75%;animation:mdFloatSparkle 2s 0.7s ease infinite}
          .md-sparkle:nth-child(4){left:80%;top:65%;animation:mdFloatSparkle 2s 1s ease infinite}
          @keyframes mdFloatSparkle{0%,100%{opacity:0;transform:translateY(0) scale(0)}30%{opacity:1;transform:translateY(-15px) scale(1.5)}70%{opacity:0;transform:translateY(-30px) scale(0.5)}}
          .md-insta{width:100%;max-width:280px;background:white;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,0.12);transform-origin:bottom center;opacity:0;transform:translateY(40px) scale(0.85) rotate(-3deg)}
          .md-insta.show{animation:mdInstaPop 0.8s cubic-bezier(0.34,1.56,0.64,1) forwards}
          @keyframes mdInstaPop{to{opacity:1;transform:translateY(0) scale(1) rotate(0)}}
          .md-insta-head{display:flex;align-items:center;gap:8px;padding:8px 10px}
          .md-insta-av{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:14px;flex-shrink:0}
          .md-insta-img{width:100%;aspect-ratio:1;object-fit:cover}
          .md-insta-badge{position:absolute;top:8px;right:8px;background:rgba(30,41,59,0.8);backdrop-filter:blur(8px);color:white;font-size:9px;font-weight:700;padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.08em}
          .md-insta-acts{display:flex;gap:10px;padding:8px 10px;align-items:center}
          .md-toast{position:absolute;top:56px;right:16px;background:white;border-radius:12px;padding:8px 14px;display:flex;align-items:center;gap:8px;box-shadow:0 15px 35px rgba(0,0,0,0.08);border:1px solid #e2e8f0;z-index:60;opacity:0;transform:translateY(-20px) scale(0.9);transition:all 0.5s cubic-bezier(0.34,1.56,0.64,1)}
          .md-toast.show{opacity:1;transform:translateY(0) scale(1)}
          .md-trophy-stack{position:absolute;top:110px;right:16px;display:flex;flex-direction:column;gap:10px;z-index:60}
          .md-trophy{background:white;border-radius:12px;padding:8px 14px;display:flex;align-items:center;gap:8px;box-shadow:0 10px 30px rgba(0,0,0,0.08);border:1px solid #e2e8f0;opacity:0;transform:translateX(30px) scale(0.9);transition:all 0.6s cubic-bezier(0.34,1.56,0.64,1)}
          .md-trophy.show{opacity:1;transform:translateX(0) scale(1)}
          .md-c{position:absolute;width:8px;height:16px;border-radius:4px;opacity:0;left:50%;top:50%;margin-left:-4px;margin-top:-8px;z-index:99;box-shadow:0 0 10px rgba(0,0,0,0.1)}
          .md-c.burst{animation:mdCBurst 2.5s ease-in-out forwards}
          @keyframes mdCBurst{0%{opacity:1;transform:translate(0,0) rotate(0deg) scale(0)}40%{opacity:1;transform:translate(var(--tx),var(--ty)) rotate(var(--rot)) scale(1.5)}100%{opacity:0;transform:translate(calc(var(--tx)*1.2),calc(var(--ty) + 400px)) rotate(calc(var(--rot) + 400deg)) scale(1)}}
          .md-c-1{background:#ef4444;--tx:-180px;--ty:-220px;--rot:120deg;}
          .md-c-2{background:#f59e0b;--tx:180px;--ty:-240px;--rot:240deg; animation-delay:0.05s;}
          .md-c-3{background:#10b981;--tx:-120px;--ty:-280px;--rot:400deg;}
          .md-c-4{background:#3b82f6;--tx:120px;--ty:-250px;--rot:180deg; animation-delay:0.02s;}
          .md-c-5{background:#a855f7;--tx:-220px;--ty:-100px;--rot:310deg;}
          .md-c-6{background:#ef4444;--tx:220px;--ty:-80px;--rot:90deg; animation-delay:0.08s;}
          .md-c-7{background:#f59e0b;--tx:-150px;--ty:-150px;--rot:420deg;}
          .md-c-8{background:#10b981;--tx:150px;--ty:-150px;--rot:270deg; animation-delay:0.04s;}
          .md-c-9{background:#3b82f6;--tx:-80px;--ty:-320px;--rot:200deg;}
          .md-c-10{background:#a855f7;--tx:80px;--ty:-320px;--rot:250deg; animation-delay:0.06s;}
          .md-c-11{background:#ef4444;--tx:-250px;--ty:-180px;--rot:150deg; animation-delay:0.02s;}
          .md-c-12{background:#f59e0b;--tx:260px;--ty:-160px;--rot:320deg; animation-delay:0.09s;}
          .md-c-13{background:#10b981;--tx:-100px;--ty:-380px;--rot:410deg; animation-delay:0.05s;}
          .md-c-14{background:#3b82f6;--tx:140px;--ty:-360px;--rot:130deg; animation-delay:0.11s;}
          .md-c-15{background:#a855f7;--tx:-190px;--ty:-290px;--rot:340deg; animation-delay:0.07s;}
          .md-c-16{background:#ef4444;--tx:210px;--ty:-260px;--rot:110deg; animation-delay:0.03s;}
          .md-c-17{background:#f59e0b;--tx:-50px;--ty:-220px;--rot:460deg; animation-delay:0.06s;}
          .md-c-18{background:#10b981;--tx:40px;--ty:-250px;--rot:210deg; animation-delay:0.04s;}
          @media(max-width:768px){
            .md-showcase{min-height:560px;max-width:340px;margin:0 auto;border:10px solid #1e293b;border-radius:3rem;height:620px}
             .md-topbar{padding:18px 20px 10px;justify-content:center;background:transparent;border:0}
             .md-topbar .md-dots-wrap, .md-topbar span{display:none !important}
             .md-notch{position:absolute;top:0;left:50%;transform:translateX(-50%);width:120px;height:24px;background:#1e293b;border-bottom-left-radius:1rem;border-bottom-right-radius:1rem;z-index:60}
            .md-home-indicator{position:absolute;bottom:8px;left:50%;transform:translateX(-50%);width:100px;height:4px;background:#e2e8f0;border-radius:2px;z-index:60}
            .md-content{padding:1rem}
            .md-mic{width:64px;height:64px}
            .md-wave{height:40px}
            .md-types{grid-template-columns:repeat(2,1fr);max-width:280px;gap:10px}
            .md-type{padding:12px 8px}
            .md-upload{max-width:220px}
            .md-insta{transform:scale(0.8);margin-top:-30px}
            .md-cursor{width:16px;height:16px}
            .md-trophy-stack{top:110px;bottom:auto;right:16px;left:auto;flex-direction:column;align-items:flex-end;pointer-events:none}
            .md-trophy{padding:6px 10px;font-size:10px}
          }
          @media(max-width:480px){
            .md-showcase{height:600px;max-width:300px;border-width:8px}
            .md-notch{width:100px;height:20px}
            .md-insta{transform:scale(0.7);margin-top:-50px}
          }
        </style>
        <section class="relative z-20 pb-20 -mt-8 sm:-mt-16"
                 x-data="demoShowcase()"
                 x-init="$nextTick(() => { setTimeout(() => startAnimation(), 800) })"
                 x-intersect:enter.half="startAnimation()"
                 x-intersect:leave="stopAnimation()">
          <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md-showcase flex flex-col" x-ref="showcase">

              <!-- Persistent Safari UI -->
              <div class="md-topbar relative z-20">
                <div class="md-dots-wrap" style="display:flex;gap:6px">
                  <div class="md-dot" style="background:#f87171"></div>
                  <div class="md-dot" style="background:#fbbf24"></div>
                  <div class="md-dot" style="background:#4ade80"></div>
                </div>
                <div class="md-notch"></div>
                <span style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-left:auto;transition:opacity 0.3s" x-text="sceneLabel"></span>
              </div>
              <div class="md-home-indicator z-50"></div>

              <!-- Alerts outside of scenes to fix stacking context -->
              <div class="md-toast" :class="showSuccessToast ? 'show' : ''">
                <div style="width:28px;height:28px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span style="font-size:12px;font-weight:600;color:#1e293b;white-space:nowrap">Publié sur Instagram ✓</span>
              </div>
              <div class="md-trophy-stack">
                <template x-for="(t, index) in trophyToasts" :key="index">
                  <div class="md-trophy" :class="t.show ? 'show' : ''">
                    <div style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:18px">🏆</div>
                    <span style="font-size:12px;font-weight:700;color:#1e293b;white-space:nowrap">Nouveau devis !</span>
                  </div>
                </template>
              </div>

              <!-- Cursor -->
              <div class="md-cursor" x-show="cursorVisible"
                   :style="`left:${cursorX}px;top:${cursorY}px;transform:rotate(${cursorRot}deg);transition:left ${cursorSpeed}s cubic-bezier(0.34,1.56,0.64,1),top ${cursorSpeed}s cubic-bezier(0.34,1.56,0.64,1),transform 0.5s cubic-bezier(0.34,1.56,0.64,1)`">
                <div :class="showClick ? 'scale-75 -rotate-12' : 'scale-100 rotate-0'" style="transition:transform 0.15s cubic-bezier(0.4,0,0.2,1);transform-origin:top left">
                  <svg viewBox="0 0 24 24" fill="none"><path d="M5.5 3.21V20.8c0 .45.54.67.85.35l4.86-4.86a.5.5 0 0 1 .35-.15h6.87a.5.5 0 0 0 .35-.85L6.35 2.86a.5.5 0 0 0-.85.35Z" fill="#1E293B" stroke="white" stroke-width="1.5"/></svg>
                </div>
              </div>

              <!-- Click -->
              <div class="md-click" :style="`left:${clickX}px;top:${clickY}px`" :class="showClick ? 'pop' : ''"></div>

              <div class="relative flex-1 w-full z-10 overflow-hidden">
              <!-- SCENE 1: Vocal -->
              <div class="md-scene" :class="currentScene === 1 ? 'active' : ''">
                <div class="md-content">
                  <div style="display:flex;flex-direction:column;align-items:center;gap:1.25rem;width:100%;max-width:380px">
                    <div style="font-size:13px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em">Étape 1 · Enregistrer un vocal</div>
                    <div class="md-mic" :class="isRecording ? 'rec' : ''">
                      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                    </div>
                    <div class="md-wave" x-show="isRecording" x-transition>
                      <template x-for="(h,i) in waveformHeights" :key="i"><div class="md-wave-bar" :style="`height:${h}%`"></div></template>
                    </div>
                    <div class="md-transcript" x-show="transcriptText.length > 0" x-transition>
                      <span x-text="transcriptText"></span><span style="display:inline-block;width:2px;height:16px;background:#f59e0b;margin-left:2px;animation:mdPulse 1s infinite" x-show="isRecording"></span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- SCENE 2: Type -->
              <div class="md-scene" :class="currentScene === 2 ? 'active' : ''">
                <div class="md-content">
                  <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;width:100%">
                    <div style="font-size:13px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em">Étape 2 · Choisir le type</div>
                    <div class="md-types">
                      <div class="md-type" :class="selectedType==='avant-apres'?'sel':''">
                        <div class="md-type-ico"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><line x1="12" x2="12" y1="3" y2="21"/></svg></div>
                        <span class="md-type-lbl">Avant / Après</span>
                      </div>
                      <div class="md-type" :class="selectedType==='conseil'?'sel':''">
                        <div class="md-type-ico"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></div>
                        <span class="md-type-lbl">Conseil</span>
                      </div>
                      <div class="md-type" :class="selectedType==='citation'?'sel':''">
                        <div class="md-type-ico"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V21"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3"/></svg></div>
                        <span class="md-type-lbl">Citation</span>
                      </div>
                      <div class="md-type" :class="selectedType==='astuce'?'sel':''">
                        <div class="md-type-ico"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg></div>
                        <span class="md-type-lbl">Astuce</span>
                      </div>
                      <div class="md-type" :class="selectedType==='faq'?'sel':''">
                        <div class="md-type-ico"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg></div>
                        <span class="md-type-lbl">FAQ</span>
                      </div>
                      <div class="md-type" :class="selectedType==='chiffre'?'sel':''">
                        <div class="md-type-ico"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg></div>
                        <span class="md-type-lbl">Chiffre clé</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- SCENE 3: Photo -->
              <div class="md-scene" :class="currentScene === 3 ? 'active' : ''">
                <div class="md-content">
                  <div style="display:flex;flex-direction:column;align-items:center;gap:1.5rem;width:100%;justify-content:center">
                    <div style="font-size:13px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em">Étape 3 · Ajouter les photos</div>
                    
                    <!-- Mode Avant/Après: 2 portraits côte à côte -->
                    <div class="md-upload-row" x-show="selectedType === 'avant-apres'" x-transition>
                      <div class="md-upload sm" :class="hasPhoto1 ? 'filled' : ''">
                        <div x-show="!hasPhoto1" class="absolute inset-0 flex flex-col items-center justify-center gap-2 color-[#94a3b8] text-slate-400">
                          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                          <span style="font-size:10px;font-weight:800;letter-spacing:0.05em">AVANT</span>
                        </div>
                        <img src="{{ asset('images/demo/avant.png') }}?v=5" alt="Avant" :class="hasPhoto1 ? 'show' : ''">
                      </div>
                      <div class="md-upload sm" :class="hasPhoto2 ? 'filled' : ''">
                        <div x-show="!hasPhoto2" class="absolute inset-0 flex flex-col items-center justify-center gap-2 color-[#94a3b8] text-slate-400">
                          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                          <span style="font-size:10px;font-weight:800;letter-spacing:0.05em">APRÈS</span>
                        </div>
                        <img src="{{ asset('images/demo/apres.png') }}?v=5" alt="Après" :class="hasPhoto2 ? 'show' : ''">
                      </div>
                    </div>

                    <!-- Mode Simple -->
                    <div class="md-upload" :class="hasPhoto1 ? 'filled' : ''" x-show="selectedType !== 'avant-apres'" x-transition>
                      <div x-show="!hasPhoto1" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;color:#94a3b8;width:100%">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                        <span style="display:block;width:100%;font-size:13px;font-weight:500;text-align:center">Cliquez pour importer</span>
                      </div>
                      <img src="{{ asset('images/demo/result.png') }}?v=5" alt="Photo chantier" :class="hasPhoto1 ? 'show' : ''">
                    </div>
                  </div>
                </div>
              </div>

              <!-- SCENE 4: AI Loading -->
              <div class="md-scene" :class="currentScene === 4 ? 'active' : ''">
                <div class="md-content">
                  <div class="md-sparkle"></div><div class="md-sparkle"></div><div class="md-sparkle"></div><div class="md-sparkle"></div>
                  <div style="display:flex;flex-direction:column;align-items:center;gap:1.25rem">
                    <div class="md-spinner"></div>
                    <div style="font-size:15px;font-weight:500;color:#1e293b">Postelio rédige votre post<span style="animation:mdDots 1.5s steps(4) infinite">&nbsp;</span></div>
                    <div style="font-size:12px;color:#94a3b8">Rédaction du texte, choix des hashtags, mise en page...</div>
                  </div>
                </div>
              </div>

              <!-- SCENE 5: Instagram Post -->
              <div class="md-scene" :class="currentScene === 5 ? 'active' : ''">
                <div class="md-content" style="justify-content:center;position:relative;">

                  <!-- Confettis -->
                  <div class="md-c md-c-1" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-2" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-3" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-4" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-5" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-6" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-7" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-8" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-9" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-10" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-11" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-12" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-13" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-14" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-15" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-16" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-17" :class="showInstaPost ? 'burst' : ''"></div>
                  <div class="md-c md-c-18" :class="showInstaPost ? 'burst' : ''"></div>

                  <div class="md-insta relative z-20" :class="showInstaPost ? 'show' : ''">
                    <div class="md-insta-head">
                      <div class="md-insta-av">M</div>
                      <div>
                        <div style="font-size:11px;font-weight:700;color:#1e293b">Menuiserie Thomas</div>
                        <div style="font-size:9px;color:#94a3b8">Lyon, France</div>
                      </div>
                    </div>
                    <div style="position:relative">
                      <img src="{{ asset('images/demo/result.png') }}?v=5" alt="Post" class="md-insta-img">
                    </div>
                    <div class="md-insta-acts">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#EF4444" stroke="#EF4444" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="2"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    </div>
                    <div style="font-size:11px;font-weight:700;color:#1e293b;padding:0 10px"><span x-text="likeCount"></span> J'aime</div>
                    <div style="font-size:10px;color:#334155;padding:4px 10px 8px;line-height:1.6">
                      <strong style="color:#1e293b">Menuiserie Thomas</strong> Transformation complète d'une cuisine. De la conception à la pose des luminaires. 🔨✨
                      <br><span style="color:#3b82f6;font-weight:500">#menuiserie #artisan #cuisine #lyon #renovation</span>
                    </div>
                    <div style="font-size:8px;color:#94a3b8;padding:0 10px 8px;text-transform:uppercase;letter-spacing:0.05em">Il y a quelques secondes</div>
                  </div>
                </div>
              </div>
              
              </div> <!-- End Scenes Container -->

            </div>
          </div>
        </section>

        <script>
        function demoShowcase() {
            return {
                currentScene: 0, cursorX: 100, cursorY: 100, cursorSpeed: 0.6, cursorVisible: false,
                clickX: 0, clickY: 0, showClick: false, sceneLabel: '',
                isRecording: false, waveformHeights: Array(20).fill(15), transcriptText: '',
                selectedType: '', hasPhoto1: false, hasPhoto2: false, showInstaPost: false, showSuccessToast: false,
                likeCount: 0, trophyToasts: [], animationRunning: false, animationTimer: null, waveformInterval: null,
                fullTranscript: "On a terminé la rénovation complète de la cuisine chez le client à Lyon, le rendu est vraiment superbe.",

                startAnimation() {
                    if (this.animationRunning) return;
                    this.animationRunning = true;
                    this.runLoop();
                },
                stopAnimation() {
                    this.animationRunning = false;
                    if (this.animationTimer) clearTimeout(this.animationTimer);
                    if (this.waveformInterval) clearInterval(this.waveformInterval);
                },
                async runLoop() {
                    while (this.animationRunning) {
                        await this.reset();
                        await this.scene1(); if (!this.animationRunning) break;
                        await this.scene2(); if (!this.animationRunning) break;
                        await this.scene3(); if (!this.animationRunning) break;
                        await this.scene4(); if (!this.animationRunning) break;
                        await this.scene5(); if (!this.animationRunning) break;
                        await this.wait(1800);
                    }
                },
                cursorRot: 0,
                async reset() {
                    this.currentScene = 0; this.cursorVisible = false; this.isRecording = false;
                    this.cursorRot = 0;
                    this.transcriptText = ''; this.selectedType = ''; this.hasPhoto1 = false; this.hasPhoto2 = false;
                    this.showInstaPost = false; this.showSuccessToast = false; this.likeCount = 0;
                    this.sceneLabel = ''; this.waveformHeights = Array(20).fill(15); this.trophyToasts = [];
                    if (this.waveformInterval) clearInterval(this.waveformInterval);
                    await this.wait(250);
                },
                async scene1() {
                    this.currentScene = 1; this.sceneLabel = '01 · Enregistrement vocal';
                    await this.wait(450);
                    this.cursorVisible = true;
                    const s = this.$refs.showcase; const sw = s.offsetWidth; const sh = s.offsetHeight;
                    await this.moveCursor(sw * 0.3, sh * 0.3, 0);
                    await this.wait(130);
                    await this.moveCursor(sw * 0.5, sw < 600 ? sh * 0.52 : sh * 0.54, 0.5);
                    await this.wait(130);
                    await this.doClick(this.cursorX, this.cursorY);
                    this.isRecording = true;
                    this.waveformInterval = setInterval(() => {
                        this.waveformHeights = this.waveformHeights.map(() => Math.floor(Math.random() * 70) + 20);
                    }, 140);
                    const words = this.fullTranscript.split(' ');
                    for (let i = 0; i < words.length; i++) {
                        if (!this.animationRunning) return;
                        this.transcriptText += (i > 0 ? ' ' : '"') + words[i];
                        await this.wait(60);
                    }
                    this.transcriptText += '"'; await this.wait(250);
                    this.isRecording = false;
                    if (this.waveformInterval) clearInterval(this.waveformInterval);
                    await this.wait(450);
                },
                async scene2() {
                    this.currentScene = 2; this.sceneLabel = '02 · Type de post';
                    await this.wait(450);
                    const s = this.$refs.showcase; const sw = s.offsetWidth; const sh = s.offsetHeight;
                    await this.moveCursor(sw < 600 ? sw * 0.28 : sw * 0.35, sw < 600 ? sh * 0.42 : sh * 0.45, 0.4);
                    await this.wait(130);
                    await this.doClick(this.cursorX, this.cursorY);
                    this.selectedType = 'avant-apres'; await this.wait(900);
                },
                async scene3() {
                    this.currentScene = 3; this.sceneLabel = '03 · Photos du chantier';
                    await this.wait(350);
                    const s = this.$refs.showcase; const sw = s.offsetWidth; const sh = s.offsetHeight;
                    
                    if (this.selectedType === 'avant-apres') {
                      // Click Left Slot
                      await this.moveCursor(sw < 600 ? sw * 0.35 : sw * 0.4, sh * 0.55, 0.27);
                      await this.wait(70);
                      await this.doClick(this.cursorX, this.cursorY);
                      this.hasPhoto1 = true;
                      
                      await this.wait(220);
                      
                      // Click Right Slot
                      await this.moveCursor(sw < 600 ? sw * 0.65 : sw * 0.6, sh * 0.55, 0.18);
                      await this.wait(70);
                      await this.doClick(this.cursorX, this.cursorY);
                      this.hasPhoto2 = true;
                    } else {
                      await this.moveCursor(sw * 0.5, sh * 0.55, 0.27);
                      await this.wait(70);
                      await this.doClick(this.cursorX, this.cursorY);
                      this.hasPhoto1 = true;
                    }
                    await this.wait(720);
                },
                async scene4() {
                    this.currentScene = 4; this.sceneLabel = '04 · Génération IA';
                    const s = this.$refs.showcase; const sw = s.offsetWidth; const sh = s.offsetHeight;
                    this.moveCursor(sw * 0.8, sh * 0.8, 1.08);
                    await this.wait(1260);
                },
                async scene5() {
                    this.currentScene = 5; this.sceneLabel = '05 · Publication IG';
                    const s = this.$refs.showcase; const sw = s.offsetWidth; const sh = s.offsetHeight;
                    await this.wait(450);
                    this.showInstaPost = true;
                    this.moveCursor(sw > 600 ? sw - 80 : sw - 50, 80, 1.8);
                    const target = 142; const steps = 20;
                    for (let i = 0; i <= steps; i++) {
                        if (!this.animationRunning) return;
                        this.likeCount = Math.round((target / steps) * i); await this.wait(32);
                    }
                    await this.wait(360); this.showSuccessToast = true;
                    await this.wait(630); this.trophyToasts.push({show: false}); 
                    setTimeout(() => { if(this.trophyToasts[0]) this.trophyToasts[0].show = true; }, 10);
                    await this.wait(450); this.trophyToasts.push({show: false});
                    setTimeout(() => { if(this.trophyToasts[1]) this.trophyToasts[1].show = true; }, 10);
                    await this.wait(450); this.trophyToasts.push({show: false});
                    setTimeout(() => { if(this.trophyToasts[2]) this.trophyToasts[2].show = true; }, 10);
                },
                async doClick(x, y) {
                    this.clickX = x; this.clickY = y; this.showClick = false;
                    await this.wait(50); this.showClick = true; await this.wait(350);
                    this.showClick = false;
                },
                async moveCursor(nx, ny, speed) {
                    this.cursorSpeed = speed;
                    let dx = nx - this.cursorX;
                    if (speed > 0) this.cursorRot = Math.max(-20, Math.min(20, dx * 0.08));
                    else this.cursorRot = 0;
                    this.cursorX = nx; this.cursorY = ny;
                    if (speed > 0) {
                        await this.wait(speed * 1000);
                        if (this.cursorX === nx) this.cursorRot = 0;
                    }
                },
                wait(ms) { return new Promise(resolve => setTimeout(resolve, ms)); }
            };
        }

        function audioPreview() {
            return {
                progress: 0, isPlaying: false, displayedText: '',
                fullText: "Salut ! On vient de finir cette superbe rénovation de cuisine à Lyon. Le résultat est top, regardez l'avant-après !",
                waveform: [30, 40, 60, 80, 100, 70, 50, 40, 60, 90, 80, 50, 30, 40, 70, 100, 80, 60, 40, 30, 50, 70, 90, 60, 40, 35, 25, 35, 45, 25],
                start() {
                    if (this.isPlaying) { this.isPlaying = false; return; }
                    this.isPlaying = true; this.run();
                },
                run() {
                    this.progress = 0; this.displayedText = '';
                    const words = this.fullText.split(' ');
                    const duration = 3000; const startTime = Date.now();
                    const interval = setInterval(() => {
                        if (!this.isPlaying) { clearInterval(interval); return; }
                        const elapsed = Date.now() - startTime;
                        this.progress = Math.min((elapsed / duration) * 100, 100);
                        const wordCount = Math.floor((this.progress / 100) * words.length);
                        this.displayedText = words.slice(0, wordCount + 1).join(' ');
                        if (this.progress >= 100) { 
                            clearInterval(interval);
                            setTimeout(() => { if (this.isPlaying) this.run(); }, 2000);
                        }
                    }, 30);
                }
            }
        }
        </script>

        <!-- Phone Mockup Section -->
        <section class="py-24 overflow-hidden relative">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
              
              <!-- Left: Text & Audio Transcript -->
              <div class="flex-1 text-center lg:text-left">
                <div class="badge badge-copper inline-flex items-center mb-6 shadow-sm">
                  <i data-lucide="wand-2" class="w-3.5 h-3.5 mr-2 text-amber-500"></i>
                  Votre texte pro, généré en un clic.
                </div>
                <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">
                  Vous faites du bon boulot. <span class="text-gradient-copper italic pr-1 -mr-1">Montrez-le.</span>
                </h2>
                <p class="text-lg text-slate-600 font-light leading-relaxed mb-12">
                  Gérer Insta le soir après le chantier, c'est l'enfer. Résultat : vous publiez peu, et on vous compare sur le prix avec des bricoleurs.
                </p>
                
                <div class="max-w-md mx-auto lg:mx-0 bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-slate-200/60 relative"
                     x-data="audioPreview()"
                     x-intersect:enter="start()">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div @click="start()" class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center shadow-sm cursor-pointer hover:scale-105 transition-transform text-white">
                        <svg x-show="!isPlaying" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-0.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        <svg x-show="isPlaying" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="10" x2="10" y1="4" y2="20"/><line x1="14" x2="14" y1="4" y2="20"/></svg>
                      </div>
                      <div class="text-left">
                        <span class="block text-sm font-semibold text-midnight">Note vocale</span>
                        <span class="block text-xs text-slate-500">00:12</span>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="isPlaying ? 'color:#f59e0b;animation:mdPulse 1.5s infinite' : ''"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                  </div>
                  
                  <!-- Animated Waveform -->
                  <div class="flex items-center gap-1.5 mb-6 h-10 w-full px-2">
                    <template x-for="(h, i) in waveform" :key="i">
                        <div class="flex-1 rounded-full min-w-[2px] transition-colors duration-300" 
                             :style="`height: ${h}%; background-color: ${ (i / waveform.length * 100) <= progress ? '#f59e0b' : '#e2e8f0' }`">
                        </div>
                    </template>
                  </div>

                  <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-left min-h-[80px] flex align-center">
                    <p class="text-midnight font-medium leading-relaxed text-sm" x-text="isPlaying ? displayedText : 'Salut ! On vient de finir cette superbe rénovation de cuisine à Lyon. Le résultat est top, regardez l\'avant-après !'">
                    </p>
                  </div>
                </div>
              </div>

              <!-- Right: Phone Mockup -->
              <div class="flex-1 relative w-full max-w-[340px] mx-auto lg:max-w-none lg:mx-0 flex justify-center mt-12 lg:mt-0">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-gradient-to-tr from-amber-200/40 to-blue-200/40 blur-[70px] rounded-full pointer-events-none"></div>
                
                <div class="relative w-[320px] bg-white rounded-[3rem] border-[10px] border-slate-900 shadow-2xl overflow-hidden z-10">
                  <div class="absolute top-0 inset-x-0 h-6 flex justify-center z-20">
                    <div class="w-24 h-5 bg-slate-900 rounded-b-2xl"></div>
                  </div>                  <!-- App Header -->
                  <div class="pt-10 pb-3 px-4 border-b border-slate-100 flex items-center justify-between bg-white sticky top-0 z-10 w-full">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center border border-amber-600/20 text-white font-bold text-[14px]">M</div>
                      <div class="text-left">
                        <div class="text-[13px] font-bold text-midnight leading-tight">Menuiserie Thomas</div>
                        <div class="text-[11px] text-slate-500">Lyon, France</div>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-more-horizontal"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                  </div>

                  <!-- Post Image -->
                  <div class="relative aspect-square bg-slate-100 group">
                    <img src="{{ asset('images/demo/result.png') }}?v=5" alt="Réalisation" class="w-full h-full object-cover">
                  </div>

                  <!-- Post Actions & Content -->
                  <div class="p-4 bg-white text-left">
                    <div class="flex items-center justify-between mb-3">
                      <div class="flex items-center gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#EF4444" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                      </div>
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>
                    </div>
                    <div class="text-[13px] font-bold text-midnight mb-2">124 J'aime</div>
                    <div class="text-[13px] text-slate-800 leading-relaxed">
                      <span class="font-bold mr-2">Menuiserie Thomas</span>
                      Transformation complète d'une cuisine. De la conception à la pose des luminaires. 
                      Une réalisation qui change totalement l'espace de vie ! 🔨✨ 
                      <br/><br/>
                      Merci à nos clients pour leur confiance.
                      <br/><br/>
                      <span class="text-blue-600 font-medium">#menuiserie #artisan #renovation #cuisine #lyon</span>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-3 uppercase tracking-wide">Il y a 2 heures</div>
                  </div>
                </div>
                
                <div class="absolute right-0 sm:right-4 top-40 bg-white p-3 rounded-2xl shadow-xl border border-slate-100 z-30 animate-bounce" style="animation-duration: 3s;">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                      <i data-lucide="check" class="w-4 h-4"></i>
                    </div>
                    <div class="text-xs font-medium text-slate-700">Publié avec succès</div>
                  </div>
                </div>

                <div class="absolute left-0 sm:left-4 top-[45%] -translate-y-1/2 bg-white p-3 rounded-2xl shadow-xl border border-slate-100 z-30 animate-bounce" style="animation-duration: 4s;">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                      <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    <div class="text-xs font-medium text-slate-700">Nouveau devis ! 🏆</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Professions Carousel -->
        <section id="galerie" class="py-16 border-y border-slate-200/40 bg-white/40 backdrop-blur-sm overflow-hidden">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="section-label text-center mb-12">Les métiers qui réussissent avec Postelio</p>
            <div class="flex overflow-x-auto pb-8 -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-4 gap-6 max-w-6xl mx-auto snap-x snap-mandatory [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
              @php
                $jobs = [
                  ['icon' => 'paintbrush', 'name' => 'Peintres', 'img' => 'peintre.png'],
                  ['icon' => 'scissors', 'name' => 'Paysagistes', 'img' => 'jardinier.png'],
                  ['icon' => 'zap', 'name' => 'Électriciens', 'img' => 'electricien.png'],
                  ['icon' => 'hammer', 'name' => 'Menuisiers', 'img' => 'menuiserie.png']
                ];
              @endphp
              @foreach($jobs as $job)
                <div class="relative rounded-3xl overflow-hidden group aspect-[4/5] min-w-[260px] sm:min-w-0 snap-center cursor-pointer border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)] transition-all duration-500">
                   <img src="{{ asset('images/demo/' . $job['img']) }}" alt="{{ $job['name'] }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                   
                   <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/10 to-transparent flex flex-col justify-end p-6 pointer-events-none">
                     <h3 class="text-xl font-bold text-white transition-transform duration-500 group-hover:translate-x-1">{{ $job['name'] }}</h3>
                   </div>
                </div>
              @endforeach
            </div>

            <div class="mt-12 text-center animate-fade-in" x-intersect="$el.classList.add('animate-fade-up')">
                <p class="text-sm text-slate-500 font-medium max-w-2xl mx-auto leading-relaxed px-4">
                    <span class="text-copper mr-1">●</span> Aussi disponible pour : Maçons, Plombiers, Couvreurs, Carreleurs, Chauffagistes, Serruriers, Multi-services, et bien d'autres corps de métier.
                </p>
            </div>
          </div>
        </section>

        <!-- Bento Grid: How it works -->
        <section id="methode" class="py-32">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
              <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">En ligne en <span class="text-gradient-copper italic">30 secondes chrono.</span></h2>
              <p class="text-xl font-light text-slate-600">Pas besoin d'être un pro du digital. Rangez vos outils, sortez votre téléphone, et laissez l'application faire le reste.</p>
            </div>

            <div class="relative max-w-5xl mx-auto">
              <!-- Connecting Line (Desktop) -->
              <div class="hidden md:block absolute top-12 left-[10%] right-[10%] h-[1px] bg-slate-200 z-0"></div>

              <div class="grid md:grid-cols-3 gap-16 md:gap-8 relative z-10">
                @php
                  $steps = [
                    [
                      'icon' => 'image',
                      'title' => 'Capturez votre travail',
                      'desc' => "Prenez une photo de votre chantier, d'un détail ou d'un conseil. C'est la vitrine irréfutable de votre expertise.",
                      'num' => '01'
                    ],
                    [
                      'icon' => 'mic',
                      'title' => 'Dictez votre note',
                      'desc' => "Parlez à votre téléphone : « Pose d'une verrière sur-mesure ». L'IA structure votre message.",
                      'num' => '02'
                    ],
                    [
                      'icon' => 'send',
                      'title' => 'C\'est en ligne',
                      'desc' => "Le résultat est publié automatiquement. Vos futurs clients voient votre niveau d'exigence.",
                      'num' => '03'
                    ]
                  ];
                @endphp
                @foreach($steps as $idx => $step)
                  <div class="flex flex-col items-center text-center group">
                    <div class="relative mb-10">
                      <!-- Icon Circle -->
                      <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center border border-slate-50 shadow-[0_15px_40px_rgba(0,0,0,0.06)] group-hover:scale-110 group-hover:border-copper/30 group-hover:shadow-copper/10 transition-all duration-500 text-copper">
                        <i data-lucide="{{ $step['icon'] }}" class="w-9 h-9 opacity-90"></i>
                      </div>
                      <!-- Number Badge -->
                      <div class="absolute -top-1 -right-1 w-7 h-7 bg-midnight text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                        {{ $step['num'] }}
                      </div>
                    </div>
                    
                    <h3 class="text-xl font-display font-semibold tracking-tight text-midnight mb-4">{{ $step['title'] }}</h3>
                    <p class="text-slate-500 font-light leading-relaxed max-w-[280px]">{{ $step['desc'] }}</p>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </section>

        <!-- Pourquoi ça marche -->
        <section id="benefices" class="py-24 bg-slate-900 text-white relative overflow-hidden">
          <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-copper/10 blur-[100px] rounded-full pointer-events-none"></div>
          <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-500/10 blur-[100px] rounded-full pointer-events-none"></div>
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
              <h2 class="text-3xl sm:text-4xl font-display font-semibold tracking-tight mb-4">Pourquoi vous allez signer plus ?</h2>
              <p class="text-lg font-light text-slate-300">La psychologie de vos clients est simple. Voici pourquoi ils vous choisiront vous.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto text-copper">
              <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6">
                  <i data-lucide="camera" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-display font-medium mb-3 text-white">L'expertise visuelle</h3>
                <p class="text-slate-400 font-light leading-relaxed">Montrer votre travail lève les doutes instantanément.</p>
              </div>
              <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6">
                  <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-display font-medium mb-3 text-white">La confiance</h3>
                <p class="text-slate-400 font-light leading-relaxed">Un post propre et technique justifie vos prix.</p>
              </div>
              <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6">
                  <i data-lucide="map-pin" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-display font-medium mb-3 text-white">La régularité</h3>
                <p class="text-slate-400 font-light leading-relaxed">Être vu souvent ramène les chantiers locaux.</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Stats Section -->
        <section class="py-24 border-y border-slate-200/40 bg-white/40 backdrop-blur-sm">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-3 gap-12 sm:gap-6 text-center divide-y sm:divide-y-0 sm:divide-x divide-slate-200/60">
              <div class="pt-8 sm:pt-0">
                <div class="text-6xl sm:text-7xl font-display font-light text-midnight mb-4 tracking-tighter">30<span class="text-copper">s</span></div>
                <div class="font-medium text-slate-700 text-lg">Chrono en main</div>
                <div class="text-sm text-slate-500 mt-2 font-light">Pour créer et publier un post</div>
              </div>
              <div class="pt-8 sm:pt-0">
                <div class="text-6xl sm:text-7xl font-display font-light text-midnight mb-4 tracking-tighter">100<span class="text-copper">%</span></div>
                <div class="font-medium text-slate-700 text-lg">Automatique</div>
                <div class="text-sm text-slate-500 mt-2 font-light">De la note vocale à la publication</div>
              </div>
              <div class="pt-8 sm:pt-0">
                <div class="text-6xl sm:text-7xl font-display font-light text-midnight mb-4 tracking-tighter">4<span class="text-copper">h</span></div>
                <div class="font-medium text-slate-700 text-lg">Gagnées par semaine</div>
                <div class="text-sm text-slate-500 mt-2 font-light">Fini les soirées sur les réseaux</div>
              </div>
            </div>
          </div>
        </section>

        <!-- Testimonials -->
        <section class="py-24">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
              <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">Ils ont arrêté de chercher des chantiers. <span class="text-gradient-copper italic">Leurs chantiers parlent pour eux.</span></h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
              @php
                $testimonials = [
                  ['name' => 'Marc D.', 'job' => 'Plombier', 'text' => "Aujourd'hui, je montre des finitions propres en 10s. Bizarrement, les clients négocient beaucoup moins mes prix.", 'img' => 'plumber'],
                  ['name' => 'Sophie L.', 'job' => 'Peintre', 'text' => "Je fais ma photo, je parle dans le téléphone, et ça me ramène de belles rénovations sans y passer mes soirées.", 'img' => 'painter'],
                  ['name' => 'Thomas B.', 'job' => 'Menuisier', 'text' => "Je montre mon exigence, je signe avec des clients sérieux, fini la guerre des prix.", 'img' => 'carpenter']
                ];
              @endphp
              @foreach($testimonials as $t)
                <div class="glass-card p-8 flex flex-col">
                  <div class="flex gap-1.5 mb-6 text-copper">
                    @for($i=0; $i<5; $i++)
                      <i data-lucide="star" class="w-4 h-4 fill-copper"></i>
                    @endfor
                  </div>
                  <p class="text-lg text-slate-700 mb-10 font-light leading-relaxed flex-1">"{{ $t['text'] }}"</p>
                  <div class="flex items-center gap-4 mt-auto">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 shadow-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                      <div class="font-medium text-midnight">{{ $t['name'] }}</div>
                      <div class="text-sm text-copper">{{ $t['job'] }}</div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </section>

        <!-- CTAs -->
        <section class="py-32 text-center px-4 relative">
          <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-amber-300/10 blur-[120px] rounded-full pointer-events-none"></div>
          <div class="max-w-4xl mx-auto relative z-10">
            <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">Rentrez chez vous, et laissez vos chantiers continuer à vous trouver des clients.</h2>
            <p class="text-xl font-light text-slate-600 mb-10">Prêt à transformer vos réalisations en aimants à clients premium ?</p>
            @auth
                <a href="{{ route('dashboard') }}" wire:navigate class="btn-primary inline-flex items-center gap-2 text-lg px-8 py-4">
                  Créer mon 1er post (Gratuit) <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            @else
                <a href="{{ route('register') }}" wire:navigate class="btn-primary inline-flex items-center gap-2 text-lg px-8 py-4">
                  Créer mon compte gratuitement <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            @endauth
          </div>
        </section>

        <!-- Pricing -->
        <section id="tarifs" class="py-32 relative" x-data="{ billingPeriod: 'monthly' }">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10">
              <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">Un investissement <span class="text-gradient-copper italic">dérisoire.</span></h2>
              <p class="text-xl font-light text-slate-600">Un abonnement rentabilisé à la seconde où vous signez un seul chantier sans devoir baisser votre prix.</p>
            </div>

            <!-- Toggle Billing Period -->
            <div class="flex justify-center items-center gap-4 mb-16">
              <span class="text-sm font-bold transition-colors duration-300" :class="billingPeriod === 'monthly' ? 'text-midnight' : 'text-gray-400'">Mensuel</span>
              <button @click="billingPeriod = billingPeriod === 'monthly' ? 'yearly' : 'monthly'" 
                      class="relative w-14 h-7 rounded-full transition-all duration-300 focus:outline-none ring-offset-2 focus:ring-2 focus:ring-copper/50"
                      :class="billingPeriod === 'yearly' ? 'bg-copper' : 'bg-slate-200'">
                  <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full transition-transform duration-300 shadow-md"
                       :class="billingPeriod === 'yearly' ? 'translate-x-7' : ''"></div>
              </button>
              <div class="flex items-center gap-2">
                  <span class="text-sm font-bold transition-colors duration-300" :class="billingPeriod === 'yearly' ? 'text-midnight' : 'text-gray-400'">Annuel</span>
                  <span class="badge badge-success !text-[10px] !px-2 !py-0.5 animate-pulse">-20%</span>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto items-stretch">

              {{-- Offre Starter --}}
              <div class="group flex flex-col h-full relative bg-white border border-slate-200/60 shadow-md rounded-3xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="p-8 flex flex-col flex-grow">
                  <div class="mb-8">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 bg-slate-50 border border-slate-100 shadow-sm">
                      <span class="text-2xl">⚡</span>
                    </div>
                    <h3 class="text-xl font-bold text-midnight">Starter</h3>
                    <div class="mt-4 flex items-baseline gap-1">
                      <span class="text-4xl font-black text-midnight tracking-tighter">0€</span>
                    </div>
                  </div>

                  <div class="h-px bg-slate-100 mb-8"></div>

                  <ul class="space-y-4 mb-10 flex-grow">
                    @foreach(["Jusqu'à 3 posts par mois", "Modèles de posts standards", "Historique des posts sur 7 jours", "Support par email"] as $feature)
                      <li class="flex items-start gap-3">
                        <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-copper/10 flex items-center justify-center">
                          <svg class="w-3 h-3 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <span class="text-xs font-medium text-slate-600 leading-relaxed">{{ $feature }}</span>
                      </li>
                    @endforeach
                    @foreach(["Publication automatique sur vos réseaux", "Planification et programmation des posts", "Analytics de vos réseaux sociaux", "Accès aux modèles premium", "Support prioritaire dédié"] as $feature)
                      <li class="flex items-start gap-3 opacity-70">
                        <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center">
                          <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        <span class="text-xs font-medium text-slate-500 leading-relaxed line-through">{{ $feature }}</span>
                      </li>
                    @endforeach
                  </ul>

                  <div class="mt-auto">
                    @auth
                      <a href="{{ route('dashboard') }}" wire:navigate class="w-full text-center py-4 px-6 rounded-2xl border border-slate-100 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-widest block hover:bg-slate-100 transition-colors">
                        Accéder à mon compte
                      </a>
                    @else
                      <a href="{{ route('register') }}" wire:navigate class="w-full text-center py-4 px-6 rounded-2xl border border-slate-100 bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-widest block hover:bg-slate-100 transition-colors">
                        Commencer gratuitement
                      </a>
                    @endauth
                  </div>
                </div>
              </div>

              {{-- Offre Basic --}}
              <div class="group flex flex-col h-full relative bg-white border border-slate-200/60 shadow-md rounded-3xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="p-8 flex flex-col flex-grow">
                  <div class="mb-8">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 bg-blue-50 border border-blue-100 shadow-sm">
                      <span class="text-2xl">🚀</span>
                    </div>
                    <h3 class="text-xl font-bold text-midnight">Basic</h3>
                    <div class="mt-4 flex items-baseline gap-1">
                      <span class="text-4xl font-black text-midnight tracking-tighter" x-text="billingPeriod === 'yearly' ? '15€' : '19€'">19€</span>
                      <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">/ mois</span>
                    </div>
                    <template x-if="billingPeriod === 'yearly'">
                        <p class="text-xs text-gray-400 mt-1">soit 180€ facturé par an</p>
                    </template>
                  </div>

                  <div class="h-px bg-slate-100 mb-8"></div>

                  <ul class="space-y-4 mb-10 flex-grow">
                    @foreach(["Jusqu'à 30 posts par mois", "Modèles de posts standards", "Historique des posts sur 30 jours", "Support prioritaire dédié"] as $feature)
                      <li class="flex items-start gap-3">
                        <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-copper/10 flex items-center justify-center">
                          <svg class="w-3 h-3 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <span class="text-xs font-medium text-slate-600 leading-relaxed">{{ $feature }}</span>
                      </li>
                    @endforeach
                    @foreach(["Publication automatique sur vos réseaux", "Planification et programmation des posts", "Analytics de vos réseaux sociaux", "Accès aux modèles premium"] as $feature)
                      <li class="flex items-start gap-3 opacity-70">
                        <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center">
                          <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        <span class="text-xs font-medium text-slate-500 leading-relaxed line-through">{{ $feature }}</span>
                      </li>
                    @endforeach
                  </ul>

                  <div class="mt-auto">
                    @auth
                      <a href="{{ route('dashboard') }}" wire:navigate class="btn-primary w-full py-4 text-center block !rounded-2xl">
                        Accéder à mon compte
                      </a>
                    @else
                      <a href="{{ route('register') }}" wire:navigate class="btn-primary w-full py-4 text-center block !rounded-2xl">
                        Commencer gratuitement
                      </a>
                    @endauth
                  </div>
                </div>
              </div>

              {{-- Offre Professionnel --}}
              <div class="group flex flex-col h-full relative bg-white border border-slate-200/60 shadow-md rounded-3xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 ring-2 ring-copper/20">
                <div class="absolute top-0 right-0 bg-copper text-white text-[10px] font-black px-4 py-1.5 rounded-bl-2xl uppercase tracking-widest shadow-sm">
                  Meilleure offre
                </div>

                <div class="p-8 flex flex-col flex-grow">
                  <div class="mb-8">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 bg-amber-50 border border-amber-100 shadow-sm">
                      <span class="text-2xl">👑</span>
                    </div>
                    <h3 class="text-xl font-bold text-midnight">Professionnel</h3>
                    <div class="mt-4 flex items-baseline gap-1">
                      <span class="text-4xl font-black text-midnight tracking-tighter" x-text="billingPeriod === 'yearly' ? '23€' : '29€'">29€</span>
                      <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">/ mois</span>
                    </div>
                    <template x-if="billingPeriod === 'yearly'">
                        <p class="text-xs text-gray-400 mt-1">soit 280€ facturé par an</p>
                    </template>
                  </div>

                  <div class="h-px bg-slate-100 mb-8"></div>

                  <ul class="space-y-4 mb-10 flex-grow">
                    @foreach(["Jusqu'à 100 posts par mois", "Publication automatique sur vos réseaux", "Planification et programmation des posts", "Analytics de vos réseaux sociaux", "Accès aux modèles premium", "Historique des posts illimité", "Support prioritaire dédié"] as $feature)
                      <li class="flex items-start gap-3">
                        <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-copper/10 flex items-center justify-center">
                          <svg class="w-3 h-3 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <span class="text-xs font-medium text-slate-600 leading-relaxed">{{ $feature }}</span>
                      </li>
                    @endforeach
                  </ul>

                  <div class="mt-auto">
                    @auth
                      <a href="{{ route('dashboard') }}" wire:navigate class="btn-primary w-full py-4 text-center block !rounded-2xl shadow-lg shadow-copper/20">
                        Accéder à mon compte
                      </a>
                    @else
                      <a href="{{ route('register') }}" wire:navigate class="btn-primary w-full py-4 text-center block !rounded-2xl shadow-lg shadow-copper/20">
                        Commencer gratuitement
                      </a>
                    @endauth
                  </div>
                </div>
              </div>

            </div>
          </div>
        </section>

        <!-- Blog Articles -->
        @php
            try {
                $latestPosts = \App\Models\BlogPost::published()->orderByDesc('published_at')->limit(3)->get();
            } catch (\Exception $e) {
                $latestPosts = collect();
            }
        @endphp
        @if($latestPosts->count())
        <section class="py-32 border-t border-slate-200/40">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
              <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-copper/10 text-copper text-xs font-bold uppercase tracking-widest mb-6">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Blog
              </span>
              <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">Derniers <span class="text-gradient-copper italic inline-block">articles.</span></h2>
              <p class="text-xl font-light text-slate-600">Conseils, stratégies et retours d'expérience pour les artisans qui veulent se démarquer.</p>
            </div>

            @if($latestPosts->count() === 1)
              @php $blogPost = $latestPosts->first(); @endphp
              <div class="max-w-5xl mx-auto">
                <article class="group bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                  <a href="{{ route('blog.show', $blogPost->slug) }}" class="flex flex-col md:flex-row items-stretch">
                    <div class="md:w-1/2 overflow-hidden bg-slate-50" style="aspect-ratio: 16/8">
                      @if($blogPost->featured_image)
                        <img src="{{ Storage::url($blogPost->featured_image) }}" alt="{{ $blogPost->title }}"
                             class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700 [backface-visibility:hidden] [transform:translateZ(0)]">
                      @else
                        <div class="w-full h-full bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center">
                          <svg class="w-16 h-16 text-copper/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                      @endif
                    </div>
                    <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                      <div class="flex items-center gap-3 mb-6">
                        <time class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $blogPost->published_at->translatedFormat('d M Y') }}</time>
                        <span class="text-slate-200">|</span>
                        <span class="text-xs font-bold text-slate-400">{{ $blogPost->read_time }} min de lecture</span>
                      </div>
                      <h3 class="font-display font-bold text-midnight text-2xl lg:text-3xl leading-tight mb-4 group-hover:text-copper transition-colors">{{ $blogPost->title }}</h3>
                      <p class="text-base text-slate-500 font-light leading-relaxed mb-8 line-clamp-3">{{ $blogPost->excerpt }}</p>
                      <div class="flex items-center gap-2 text-copper text-sm font-bold uppercase tracking-widest group-hover:gap-3 transition-all">
                        Lire l'article complet
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                      </div>
                    </div>
                  </a>
                </article>
              </div>
            @else
              <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @foreach($latestPosts as $blogPost)
                  <article class="group bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <a href="{{ route('blog.show', $blogPost->slug) }}">
                      <div class="overflow-hidden bg-slate-50" style="aspect-ratio: 16/8">
                        @if($blogPost->featured_image)
                          <img src="{{ Storage::url($blogPost->featured_image) }}" alt="{{ $blogPost->title }}"
                               class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700 [backface-visibility:hidden] [transform:translateZ(0)]">
                        @else
                          <div class="w-full h-full bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center">
                            <svg class="w-12 h-12 text-copper/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                          </div>
                        @endif
                      </div>
                      <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                          <time class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ $blogPost->published_at->translatedFormat('d M Y') }}</time>
                          <span class="text-slate-200">|</span>
                          <span class="text-[11px] font-bold text-slate-400">{{ $blogPost->read_time }} min</span>
                        </div>
                        <h3 class="font-display font-bold text-midnight text-lg leading-snug mb-3 group-hover:text-copper transition-colors line-clamp-2">{{ $blogPost->title }}</h3>
                        <p class="text-sm text-slate-500 font-light leading-relaxed line-clamp-3">{{ $blogPost->excerpt }}</p>
                        <div class="mt-5 flex items-center gap-2 text-copper text-xs font-bold uppercase tracking-widest group-hover:gap-3 transition-all">
                          Lire l'article
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </div>
                      </div>
                    </a>
                  </article>
                @endforeach
              </div>
            @endif

            <div class="text-center mt-12">
              <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl border border-slate-200 bg-white text-midnight font-bold text-sm uppercase tracking-widest hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                Voir tous les articles
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
              </a>
            </div>
          </div>
        </section>
        @endif

        <!-- FAQ -->
        <section class="py-32 border-t border-slate-200/40 bg-white/40 backdrop-blur-sm">
          <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
              <h2 class="text-4xl font-display font-semibold tracking-tight text-midnight mb-6">Questions fréquentes</h2>
            </div>

            <div class="space-y-4">
              @php
                $faqs = [
                  ['q' => "Est-ce que j'ai besoin de beaucoup d'abonnés ?", 'short' => "Non. Quelque centaines de locaux suffisent.", 'a' => "Non. Quelques centaines de personnes locales (vos anciens clients, vos partenaires) suffisent pour créer un bouche-à-oreille qualifié et puissant."],
                  ['q' => "Je suis nul en informatique, c'est pour moi ?", 'short' => "Si vous utilisez WhatsApp, c'est bon.", 'a' => "Si vous savez envoyer un message vocal sur WhatsApp, vous savez utiliser Postelio. L'application gère toute la technique."],
                  ['q' => "Combien de temps ça prend par jour ?", 'short' => "Strictement 30 secondes.", 'a' => "Strictement 30 secondes par chantier. Vous parlez, on publie."],
                  ['q' => "Est-ce vraiment rentable ?", 'short' => "Remboursé pour 5 ans dès le 1er devis.", 'a' => "Oui. Un seul devis haut de gamme signé au prix fort grâce à l'image professionnelle de votre page rembourse l'outil pour les 5 prochaines années."]
                ];
              @endphp
              @foreach($faqs as $idx => $faq)
                <div class="glass-card overflow-hidden">
                  <button @click="openFaq === {{ $idx }} ? openFaq = null : openFaq = {{ $idx }}"
                          class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50/50 transition-colors">
                    <div>
                      <span class="block font-medium text-lg text-midnight mb-1">{{ $faq['q'] }}</span>
                      <span class="text-copper text-sm">{{ $faq['short'] }}</span>
                    </div>
                    <div class="ml-4 flex-shrink-0 text-slate-400">
                      <i data-lucide="chevron-down" class="w-5 h-5 transition-transform duration-300" :class="openFaq === {{ $idx }} ? 'rotate-180' : ''"></i>
                    </div>
                  </button>
                  <div x-show="openFaq === {{ $idx }}"
                       x-collapse
                       class="px-6 pb-6 text-slate-600 font-light leading-relaxed"
                       style="display: none;">
                    <div class="pt-4 border-t border-slate-100">
                      {{ $faq['a'] }}
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </section>
      </main>

      <div class="md:hidden fixed bottom-0 inset-x-0 p-4 pb-6 bg-white/90 backdrop-blur-2xl border-t border-slate-200 z-50">
        <a href="#tarifs" class="btn-primary flex justify-center items-center w-full py-3.5 text-base shadow-lg shadow-amber-500/20">
          Découvrir les offres
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>

      <!-- Footer -->
      <footer class="bg-white text-slate-500 pt-20 pb-28 md:pb-12 border-t border-slate-100 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-16">
            <!-- Col 1: Brand -->
            <div class="col-span-2 md:col-span-1">
              <x-logo size="lg" class="mb-6" />
              <p class="text-slate-500 font-light leading-relaxed mb-6 max-w-xs">
                La solution intelligente pour les artisans qui veulent transformer leurs chantiers en aimants à clients.
              </p>
              <div class="flex gap-4">
                <a href="https://instagram.com/postelio.app" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-copper hover:border-copper/20 hover:bg-amber-50 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                </a>
              </div>
            </div>

            <!-- Col 2: Produit -->
            <div class="md:pl-10">
              <h4 class="font-display font-semibold text-midnight mb-6">Produit</h4>
              <ul class="space-y-4 text-sm">
                <li><a href="#methode" class="hover:text-copper transition-colors font-light">La méthode</a></li>
                <li><a href="#galerie" class="hover:text-copper transition-colors font-light">Réalisations</a></li>
                <li><a href="#benefices" class="hover:text-copper transition-colors font-light">Avantages</a></li>
                <li><a href="#tarifs" class="hover:text-copper transition-colors font-light">Tarifs</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-copper transition-colors font-light">Blog</a></li>
              </ul>
            </div>

            <!-- Col 3: Entreprise -->
            <div>
              <h4 class="font-display font-semibold text-midnight mb-6">Légal</h4>
              <ul class="space-y-4 text-sm">
                <li><a href="{{ route('legal.mentions') }}" class="hover:text-copper transition-colors font-light">Mentions légales</a></li>
                <li><a href="{{ route('legal.cgv') }}" class="hover:text-copper transition-colors font-light">CGV</a></li>
                <li><a href="{{ route('legal.confidentialite') }}" class="hover:text-copper transition-colors font-light">Confidentialité</a></li>
                <li><a href="mailto:hello@postelio.fr" class="hover:text-copper transition-colors font-light">Contact</a></li>
              </ul>
            </div>

            <!-- Col 4: Plus -->
            <div>
              <h4 class="font-display font-semibold text-midnight mb-6">Plus</h4>
              <ul class="space-y-4 text-sm">
                <li><a href="#" class="hover:text-copper transition-colors font-light">Blog</a></li>
                <li><a href="#" class="hover:text-copper transition-colors font-light">Guides artisans</a></li>
                <li><a href="#" class="hover:text-copper transition-colors font-light">Aides au digital</a></li>
                <li><a href="{{ route('login') }}" class="text-copper font-medium">Connexion Pro</a></li>
              </ul>
            </div>
          </div>

          <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-xs font-light text-slate-400 whitespace-nowrap">
              &copy; {{ date('Y') }} Postelio. Un outil pensé pour les artisans passionnés.
            </div>
            <div class="flex items-center gap-2 text-[10px] uppercase tracking-widest font-bold text-slate-400 bg-slate-50 px-4 py-2 rounded-full border border-slate-100">
              <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
              Système 100% opérationnel
            </div>
          </div>
        </div>
      </footer>

      <script>
        // Initialize Lucide icons
        lucide.createIcons();
      </script>
    </body>
</html>
<!-- Déclencheur de build V2 -->
