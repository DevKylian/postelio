<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Postelio — Votre chantier travaille pour vous</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js Collapse Plugin + Alpine.js -->
        <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>
    </head>
    <body class="antialiased font-sans bg-chalk text-slate-600 selection:bg-copper/30 selection:text-amber-900 overflow-x-hidden relative"
          x-data="{ 
            isMenuOpen: false, 
            scrolled: false, 
            openFaq: null,
            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 50;
                });
            }
          }">
      
      <!-- Background Decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[10%] -left-[10%] w-[800px] h-[800px] bg-amber-200/40 rounded-full blur-[120px] opacity-40"></div>
      <div class="absolute top-[40%] -right-[15%] w-[700px] h-[700px] bg-orange-200/20 rounded-full blur-[100px] opacity-30"></div>
      <div class="absolute -bottom-[20%] left-[20%] w-[600px] h-[600px] bg-amber-100/30 rounded-full blur-[100px] opacity-20"></div>
    </div>

      <!-- Navigation -->
      <nav class="fixed top-4 inset-x-0 z-50 transition-all duration-500 flex justify-center px-4">
        <div class="w-full max-w-5xl transition-all duration-500 rounded-full px-4 sm:px-6 py-3 flex justify-between items-center border glass-card bg-white/70"
             :class="scrolled ? 'bg-white/95 shadow-lg' : 'bg-white/70 shadow-sm'">
          <x-logo size="md" />
          
          <!-- Desktop Nav -->
          <div class="hidden md:flex items-center gap-8">
            <a href="#methode" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">La méthode</a>
            <a href="#galerie" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">Galerie</a>
            <a href="#benefices" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">Avantages</a>
            <a href="#tarifs" class="text-sm font-medium text-slate-600 hover:text-midnight transition-colors">Tarifs</a>
            
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
                <div class="badge badge-copper inline-flex items-center mb-8 shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-amber-500"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707m12.728 0-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/></svg>
                  Pour les artisans qui veulent évoluer
                </div>
              <h1 class="text-5xl sm:text-6xl lg:text-8xl font-display font-semibold tracking-tighter text-midnight mb-8 leading-[1.05]">
                Votre chantier travaille <br class="hidden sm:block" />
                <span class="text-gradient-copper italic pr-3 pb-1">pour vous.</span>
              </h1>
              <p class="text-xl sm:text-2xl font-light text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Attirez des clients haut de gamme et mieux payés, sans négociation ni effort supplémentaire.
              </p>
              <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @auth
                    <a href="{{ route('generate-post') }}" wire:navigate class="btn-primary w-full sm:w-auto flex justify-center items-center gap-2 text-base">
                        Créer un post
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" wire:navigate class="btn-primary w-full sm:w-auto flex justify-center items-center gap-2 text-base">
                        Essayer gratuitement
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @endauth
                <a href="#methode" class="btn-secondary w-full sm:w-auto flex justify-center items-center text-base font-semibold">
                  Découvrir la méthode
                </a>
              </div>
              <div class="flex flex-col items-center gap-6 mt-10">
                <div class="flex items-center gap-3">
                  <div class="flex -space-x-2">
                    @foreach(['plumber', 'painter', 'carpenter', 'electrician'] as $avatar)
                      <img src="https://picsum.photos/seed/{{ $avatar }}/100/100" alt="" class="w-8 h-8 rounded-full border-2 border-white object-cover shadow-sm">
                    @endforeach
                  </div>
                  <div class="text-sm text-slate-600">
                    <span class="font-semibold text-midnight">+200 artisans</span> nous font confiance
                  </div>
                </div>
                <div class="flex flex-wrap justify-center items-center gap-4 text-xs font-medium text-slate-400 uppercase tracking-widest">
                  <span class="flex items-center gap-1.5"><i data-lucide="check" class="w-4 h-4 text-copper"></i> 14 jours d'essai</span>
                  <span class="hidden sm:block">&bull;</span>
                  <span class="flex items-center gap-1.5"><i data-lucide="check" class="w-4 h-4 text-copper"></i> Sans CB</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Mini Demo -->
        <section class="relative z-20 pb-16 -mt-8 sm:-mt-12">
          <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-card p-4 sm:p-8 flex items-center justify-between gap-2 sm:gap-8 shadow-xl shadow-slate-200/50">
              
              <!-- Step 1: Photo -->
              <div class="flex-1 flex flex-col items-center gap-3 sm:gap-4">
                <div class="w-full max-w-[80px] sm:max-w-[140px] aspect-square rounded-2xl overflow-hidden relative shadow-sm border border-slate-100 group">
                  <img src="https://picsum.photos/seed/chantier/200/200" alt="Photo chantier" class="w-full h-full object-cover">
                  <div class="absolute inset-0 bg-slate-900/20 flex items-center justify-center">
                    <div class="w-8 h-8 sm:w-12 sm:h-12 bg-white/30 backdrop-blur-md rounded-full flex items-center justify-center border border-white/50">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 sm:w-6 sm:h-6 drop-shadow-md"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                    </div>
                  </div>
                </div>
                <span class="text-[10px] sm:text-sm font-semibold text-slate-700 uppercase tracking-wider">1. Photo</span>
              </div>

              <i data-lucide="arrow-right" class="w-4 h-4 sm:w-8 sm:h-8 text-slate-300 flex-shrink-0"></i>

              <!-- Step 2: Vocal -->
              <div class="flex-1 flex flex-col items-center gap-3 sm:gap-4">
                <div class="w-full max-w-[80px] sm:max-w-[140px] aspect-square rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 flex flex-col items-center justify-center shadow-sm relative overflow-hidden">
                  <div class="w-8 h-8 sm:w-12 sm:h-12 bg-amber-100 rounded-full flex items-center justify-center mb-1 sm:mb-2 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 sm:w-6 sm:h-6"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                  </div>
                  <div class="flex items-center gap-0.5 sm:gap-1 h-4 sm:h-6">
                     @foreach([40, 80, 60, 100, 50, 70, 40] as $h)
                       <div class="w-1 sm:w-1.5 bg-amber-400 rounded-full" style="height: {{ $h }}%"></div>
                     @endforeach
                  </div>
                </div>
                <span class="text-[10px] sm:text-sm font-semibold text-slate-700 uppercase tracking-wider">2. Vocal</span>
              </div>

              <i data-lucide="arrow-right" class="w-4 h-4 sm:w-8 sm:h-8 text-slate-300 flex-shrink-0"></i>

              <!-- Step 3: Publié -->
              <div class="flex-1 flex flex-col items-center gap-3 sm:gap-4">
                <div class="w-full max-w-[80px] sm:max-w-[140px] aspect-square rounded-2xl bg-white border border-slate-100 shadow-sm flex flex-col p-2 sm:p-3">
                  <div class="flex items-center gap-1.5 sm:gap-2 mb-1.5 sm:mb-2">
                    <div class="w-4 h-4 sm:w-6 sm:h-6 rounded-full bg-slate-200"></div>
                    <div class="w-10 sm:w-16 h-1.5 sm:h-2 rounded-full bg-slate-200"></div>
                  </div>
                  <div class="flex-1 rounded sm:rounded-lg bg-slate-100 mb-1.5 sm:mb-2 overflow-hidden relative">
                     <img src="https://picsum.photos/seed/chantier/200/200" alt="Post" class="w-full h-full object-cover">
                     <div class="absolute bottom-1 left-1 bg-slate-900/60 backdrop-blur-sm px-1 sm:px-1.5 py-0.5 rounded text-[5px] sm:text-[7px] text-white font-medium">Avant / Après</div>
                  </div>
                  <div class="flex gap-1 sm:gap-1.5 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#EF4444" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-2.5 h-2.5 sm:w-3.5 sm:h-3.5"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    <div class="w-8 sm:w-12 h-1 sm:h-1.5 rounded-full bg-slate-200"></div>
                  </div>
                </div>
                <span class="text-[10px] sm:text-sm font-semibold text-amber-600 uppercase tracking-wider">3. Publié</span>
              </div>

            </div>
          </div>
        </section>

        <!-- Phone Mockup Section -->
        <section class="py-24 overflow-hidden relative">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
              
              <!-- Left: Text & Audio Transcript -->
              <div class="flex-1 text-center lg:text-left">
                <div class="badge badge-copper inline-flex items-center mb-6 shadow-sm">
                  <i data-lucide="wand-2" class="w-3.5 h-3.5 mr-2 text-amber-500"></i>
                  L'IA qui rédige pour vous
                </div>
                <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">
                  Un rendu <span class="text-gradient-copper italic pr-2 pb-1">impeccable</span>, à chaque fois.
                </h2>
                <p class="text-lg text-slate-600 font-light leading-relaxed mb-12">
                  Fini le syndrome de la page blanche. Postelio transforme votre simple note vocale en une publication professionnelle, engageante, avec les bons hashtags et emojis.
                </p>
                
                <div class="max-w-md mx-auto lg:mx-0 bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-slate-200/60 relative">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center shadow-sm cursor-pointer hover:scale-105 transition-transform text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-0.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                      </div>
                      <div class="text-left">
                        <span class="block text-sm font-semibold text-midnight">Note vocale</span>
                        <span class="block text-xs text-slate-500">00:12</span>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                  </div>
                  
                  <!-- Fake Waveform -->
                  <div class="flex items-center gap-1.5 mb-6 h-10 w-full px-2">
                    @foreach([30, 40, 60, 80, 100, 70, 50, 40, 60, 90, 80, 50, 30, 40, 70, 100, 80, 60, 40, 30] as $h)
                      <div class="flex-1 bg-amber-500/40 rounded-full min-w-[2px]" style="height: {{ $h }}%"></div>
                    @endforeach
                    <div class="flex-1 h-full bg-amber-500 rounded-full shadow-[0_0_10px_rgba(245,158,11,0.5)] min-w-[4px]"></div>
                    @foreach([50, 70, 90, 60, 40, 30, 20, 30, 40, 20] as $h)
                      <div class="flex-1 bg-slate-300 rounded-full min-w-[2px]" style="height: {{ $h }}%"></div>
                    @endforeach
                  </div>

                  <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-left">
                    <p class="text-midnight font-medium leading-relaxed text-sm">
                      "Euh voilà, on a fini la pose de la verrière chez le client à Lyon, c'est du sur-mesure en acier noir, le rendu est super propre."
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
                      <img src="https://picsum.photos/seed/artisan-avatar/100/100" alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                      <div class="text-left">
                        <div class="text-[13px] font-bold text-midnight leading-tight">Menuiserie Thomas</div>
                        <div class="text-[11px] text-slate-500">Lyon, France</div>
                      </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-more-horizontal"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                  </div>

                  <!-- Post Image -->
                  <div class="relative aspect-square bg-slate-100 group">
                    <img src="https://picsum.photos/seed/wood-finished/500/500" alt="Réalisation" class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3 badge badge-midnight backdrop-blur-md text-white shadow-sm border border-white/10 tracking-widest uppercase">
                      Avant / Après
                    </div>
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
                      Création et pose d'une verrière sur-mesure en acier thermolaqué noir. 
                      Une belle transformation qui apporte de la lumière à cette pièce de vie ! 🔨✨ 
                      <br/><br/>
                      Merci à nos clients pour leur confiance.
                      <br/><br/>
                      <span class="text-blue-600 font-medium">#menuiserie #artisan #renovation #verriere #lyon</span>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-3 uppercase tracking-wide">Il y a 2 heures</div>
                  </div>
                </div>
                
                <div class="absolute -right-4 sm:-right-8 top-24 bg-white p-3 rounded-2xl shadow-xl border border-slate-100 z-20 animate-bounce" style="animation-duration: 3s;">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                      <i data-lucide="check" class="w-4 h-4"></i>
                    </div>
                    <div class="text-xs font-medium text-slate-700">Publié avec succès</div>
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
                  ['icon' => 'paintbrush', 'name' => 'Peintres', 'caption' => 'Avant/Après peintures façades', 'img' => 'wall'],
                  ['icon' => 'scissors', 'name' => 'Paysagistes', 'caption' => 'Avant/Après aménagement jardin', 'img' => 'garden'],
                  ['icon' => 'zap', 'name' => 'Électriciens', 'caption' => 'Avant/Après tableau électrique', 'img' => 'wires'],
                  ['icon' => 'hammer', 'name' => 'Menuisiers', 'caption' => 'Avant/Après menuiserie sur-mesure', 'img' => 'wood']
                ];
              @endphp
              @foreach($jobs as $job)
                <div class="relative rounded-3xl overflow-hidden group aspect-[4/5] min-w-[260px] sm:min-w-0 snap-center cursor-pointer border border-white/60 shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)] transition-all duration-500">
                   <img src="https://picsum.photos/seed/{{ $job['img'] }}-dirty/400/500" alt="{{ $job['name'] }} Avant" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700 group-hover:opacity-0">
                   <img src="https://picsum.photos/seed/{{ $job['img'] }}-clean/400/500" alt="{{ $job['name'] }} Après" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700 opacity-0 group-hover:opacity-100">
                   
                   <div class="absolute top-4 left-4 badge badge-white bg-white/90 backdrop-blur-md text-midnight transition-opacity duration-500 group-hover:opacity-0 uppercase tracking-widest shadow-sm">Avant</div>
                   <div class="absolute top-4 left-4 badge badge-midnight bg-midnight text-white transition-opacity duration-500 opacity-0 group-hover:opacity-100 uppercase tracking-widest shadow-sm">Après</div>

                   <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent flex flex-col justify-end p-6 pointer-events-none">
                     <div class="flex items-center gap-3 text-white font-display font-medium text-lg mb-1">
                       <i data-lucide="{{ $job['icon'] }}" class="w-5 h-5 text-copper"></i>
                       {{ $job['name'] }}
                     </div>
                     <div class="text-xs text-slate-300 font-light">{{ $job['caption'] }}</div>
                   </div>
                </div>
              @endforeach
            </div>
          </div>
        </section>

        <!-- Bento Grid: How it works -->
        <section id="methode" class="py-32">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
              <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">Prouvez votre expertise en <span class="text-gradient-copper italic pr-2 pb-1">30 secondes.</span></h2>
              <p class="text-xl font-light text-slate-600">Pas besoin de devenir un pro du digital. Montrez simplement ce que vous savez faire de mieux, directement depuis le chantier.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
              @php
                $steps = [
                  ['icon' => 'camera', 'title' => 'Capturez la preuve', 'desc' => "Prenez une photo Avant/Après de votre réalisation. C'est la preuve irréfutable de la qualité de votre travail."],
                  ['icon' => 'mic', 'title' => 'Dictez votre note', 'desc' => "Parlez à votre téléphone : « Pose d'une verrière sur-mesure ». L'IA structure votre message."],
                  ['icon' => 'send', 'title' => 'C\'est en ligne', 'desc' => "Le résultat est publié automatiquement. Vos futurs clients voient votre niveau d'exigence."]
                ];
              @endphp
              @foreach($steps as $idx => $step)
                <div class="glass-card p-8 flex flex-col items-start group">
                  <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mb-8 border border-slate-100 shadow-sm group-hover:scale-110 group-hover:border-copper group-hover:shadow-copper/10 transition-all duration-500 text-copper">
                    <i data-lucide="{{ $step['icon'] }}" class="w-6 h-6"></i>
                  </div>
                  <div class="text-copper font-display font-bold text-xl mb-2">0{{ $idx + 1 }}</div>
                  <h3 class="text-2xl font-display font-semibold tracking-tight text-midnight mb-4">{{ $step['title'] }}</h3>
                  <p class="text-slate-600 font-light leading-relaxed">{{ $step['desc'] }}</p>
                </div>
              @endforeach
            </div>
          </div>
        </section>

        <!-- Pourquoi ça marche -->
        <section id="benefices" class="py-24 bg-slate-900 text-white relative overflow-hidden">
          <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-copper/10 blur-[100px] rounded-full pointer-events-none"></div>
          <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-500/10 blur-[100px] rounded-full pointer-events-none"></div>
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
              <h2 class="text-3xl sm:text-4xl font-display font-semibold tracking-tight mb-4">Pourquoi ça marche ?</h2>
              <p class="text-lg font-light text-slate-300">La psychologie de vos clients est simple. Voici pourquoi ils vous choisiront vous.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto text-copper">
              <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6">
                  <i data-lucide="eye" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-display font-medium mb-3 text-white">Ils achètent ce qu'ils voient</h3>
                <p class="text-slate-400 font-light leading-relaxed">Les clients ont du mal à se projeter. Montrer vos réalisations concrètes lève leurs doutes instantanément.</p>
              </div>
              <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6">
                  <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-display font-medium mb-3 text-white">L'Avant/Après crée la confiance</h3>
                <p class="text-slate-400 font-light leading-relaxed">Rien n'est plus puissant que la preuve de votre transformation. C'est la garantie de votre expertise.</p>
              </div>
              <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6">
                  <i data-lucide="trending-up" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-display font-medium mb-3 text-white">La régularité crée la demande</h3>
                <p class="text-slate-400 font-light leading-relaxed">Être vu souvent par les bonnes personnes locales crée un effet de familiarité. Quand ils auront besoin, ils penseront à vous.</p>
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
              <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">Ils ont choisi <span class="text-gradient-copper italic">leurs clients.</span></h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
              @php
                $testimonials = [
                  ['name' => 'Marc D.', 'job' => 'Plombier-Chauffagiste', 'text' => "Je refusais de perdre mon temps sur le digital. Aujourd'hui je ne fais plus que des rénovations complètes. Les clients voient mon exigence et acceptent mes devis sans discuter.", 'img' => 'plumber'],
                  ['name' => 'Sophie L.', 'job' => 'Peintre en bâtiment', 'text' => "Mes photos Avant/Après publiées automatiquement m'ont apporté 30% de clients premium en plus ce mois-ci. L'outil est d'une simplicité enfantine.", 'img' => 'painter'],
                  ['name' => 'Thomas B.', 'job' => 'Menuisier Agenceur', 'text' => "Fini le bouche-à-oreille irrégulier. Ma vitrine travaille pour moi. Je choisis mes chantiers et je ne me bats plus sur les prix.", 'img' => 'carpenter']
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
                    <img src="https://picsum.photos/seed/{{ $t['img'] }}/100/100" alt="{{ $t['name'] }}" class="w-12 h-12 rounded-full object-cover border border-slate-200 shadow-sm">
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
                  Démarrer maintenant <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            @else
                <a href="{{ route('register') }}" wire:navigate class="btn-primary inline-flex items-center gap-2 text-lg px-8 py-4">
                  Démarrer mon essai gratuit <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            @endauth
          </div>
        </section>

        <!-- FAQ -->
        <section class="py-32 border-t border-slate-200/40 bg-white/40 backdrop-blur-sm">
          <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
              <h2 class="text-4xl font-display font-semibold tracking-tight text-midnight mb-6">Questions fréquentes</h2>
            </div>
            
            <div class="space-y-4">
              @php
                $faqs = [
                  ['q' => "Est-ce que j'ai besoin de beaucoup d'abonnés ?", 'short' => "Non. Quelques centaines de personnes locales suffisent.", 'a' => "Vous n'êtes pas un influenceur. Votre but est de toucher les propriétaires de votre région. Un petit réseau local qualifié génère bien plus de chantiers que 10 000 abonnés à l'autre bout du monde."],
                  ['q' => "Faut-il savoir poster sur Instagram ou Facebook ?", 'short' => "Non, l'application fait tout pour vous.", 'a' => "30 secondes suffisent pour créer et publier un post parfait. Vous n'avez besoin d'aucune compétence en réseaux sociaux."],
                  ['q' => "Combien de temps ça prend par jour ?", 'short' => "30 secondes par chantier suffisent.", 'a' => "Prenez la photo, dictez votre note vocale, et c'est publié. L'application s'occupe de la rédaction et de la mise en forme."],
                  ['q' => "Est-ce vraiment rentable dès le premier mois ?", 'short' => "Oui, nos artisans voient +30% de clients premium.", 'a' => "Dès les premières semaines, votre vitrine professionnelle rassure les prospects et justifie vos tarifs sans négociation."]
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

        <!-- Pricing -->
        <section id="tarifs" class="py-32 relative">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
              <h2 class="text-4xl sm:text-5xl font-display font-semibold tracking-tight text-midnight mb-6">Un investissement dérisoire pour <span class="text-gradient-copper italic">monter en gamme.</span></h2>
              <p class="text-xl font-light text-slate-600">Rentabilisé dès votre premier chantier haut de gamme signé.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto items-stretch">

              {{-- Offre Starter --}}
              <div class="glass-card p-8 relative overflow-hidden flex flex-col">
                <div class="text-center mb-8">
                  <h3 class="text-2xl font-display font-medium text-midnight mb-4">Starter</h3>
                  <div class="flex justify-center items-baseline gap-2">
                    <span class="text-5xl font-display font-light text-midnight tracking-tighter">0€</span>
                  </div>
                </div>

                <ul class="space-y-3.5 mb-10 flex-grow">
                  @foreach(["Jusqu'à 3 posts par mois", "Modèles de posts standards", "Historique des posts sur 7 jours", "Support par email"] as $feature)
                    <li class="flex items-center gap-3">
                      <div class="w-5 h-5 rounded-full bg-slate-50 flex items-center justify-center flex-shrink-0 border border-slate-200 text-slate-400">
                        <i data-lucide="check" class="w-3 h-3"></i>
                      </div>
                      <span class="text-slate-700 font-light text-sm">{{ $feature }}</span>
                    </li>
                  @endforeach
                  @foreach(["Publication automatique sur vos réseaux", "Planification et programmation des posts", "Analytics de vos réseaux sociaux", "Accès aux modèles premium", "Support prioritaire dédié"] as $feature)
                    <li class="flex items-center gap-3 opacity-40">
                      <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 text-slate-300">
                        <i data-lucide="x" class="w-3 h-3"></i>
                      </div>
                      <span class="text-slate-400 font-light text-sm line-through">{{ $feature }}</span>
                    </li>
                  @endforeach
                </ul>

                @auth
                  <a href="{{ route('dashboard') }}" wire:navigate class="w-full py-3.5 text-center block border border-slate-200 rounded-xl text-midnight font-medium hover:bg-slate-50 transition-colors">
                    Accéder à mon compte
                  </a>
                @else
                  <a href="{{ route('register') }}" wire:navigate class="w-full py-3.5 text-center block border border-slate-200 rounded-xl text-midnight font-medium hover:bg-slate-50 transition-colors">
                    Commencer gratuitement
                  </a>
                @endauth
              </div>

              {{-- Offre Essentiel --}}
              <div class="glass-card p-8 relative overflow-hidden flex flex-col ring-2 ring-copper/20 shadow-xl">
                <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-copper via-orange-500 to-amber-600"></div>
                <div class="text-center mb-1">
                  <div class="badge badge-copper uppercase tracking-widest font-medium text-[10px] mb-4">Populaire</div>
                </div>
                <div class="text-center mb-8">
                  <h3 class="text-2xl font-display font-medium text-midnight mb-4">Essentiel</h3>
                  <div class="flex justify-center items-baseline gap-2">
                    <span class="text-5xl font-display font-light text-midnight tracking-tighter">14,90€</span>
                    <span class="text-lg text-slate-500 font-light">/ mois</span>
                  </div>
                </div>

                <ul class="space-y-3.5 mb-10 flex-grow">
                  @foreach(["Jusqu'à 30 posts par mois", "Modèles de posts standards", "Historique des posts sur 30 jours", "Support prioritaire dédié"] as $feature)
                    <li class="flex items-center gap-3">
                      <div class="w-5 h-5 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0 border border-amber-100 text-copper">
                        <i data-lucide="check" class="w-3 h-3"></i>
                      </div>
                      <span class="text-slate-700 font-light text-sm">{{ $feature }}</span>
                    </li>
                  @endforeach
                  @foreach(["Publication automatique sur vos réseaux", "Planification et programmation des posts", "Analytics de vos réseaux sociaux", "Accès aux modèles premium"] as $feature)
                    <li class="flex items-center gap-3 opacity-40">
                      <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 text-slate-300">
                        <i data-lucide="x" class="w-3 h-3"></i>
                      </div>
                      <span class="text-slate-400 font-light text-sm line-through">{{ $feature }}</span>
                    </li>
                  @endforeach
                </ul>

                @auth
                  <a href="{{ route('dashboard') }}" wire:navigate class="btn-primary w-full py-3.5 text-center block shadow-lg shadow-amber-500/20">
                    Accéder à mon compte
                  </a>
                @else
                  <a href="{{ route('register') }}" wire:navigate class="btn-primary w-full py-3.5 text-center block shadow-lg shadow-amber-500/20">
                    Démarrer mon essai gratuit
                  </a>
                @endauth
              </div>

              {{-- Offre Professionnel --}}
              <div class="glass-card p-8 relative overflow-hidden flex flex-col">
                <div class="text-center mb-8">
                  <h3 class="text-2xl font-display font-medium text-midnight mb-4">Professionnel</h3>
                  <div class="flex justify-center items-baseline gap-2">
                    <span class="text-5xl font-display font-light text-midnight tracking-tighter">29,90€</span>
                    <span class="text-lg text-slate-500 font-light">/ mois</span>
                  </div>
                </div>

                <ul class="space-y-3.5 mb-10 flex-grow">
                  @foreach(["Jusqu'à 100 posts par mois", "Publication automatique sur vos réseaux", "Planification et programmation des posts", "Analytics de vos réseaux sociaux", "Accès aux modèles premium", "Historique des posts illimité", "Support prioritaire dédié"] as $feature)
                    <li class="flex items-center gap-3">
                      <div class="w-5 h-5 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0 border border-amber-100 text-copper">
                        <i data-lucide="check" class="w-3 h-3"></i>
                      </div>
                      <span class="text-slate-700 font-light text-sm">{{ $feature }}</span>
                    </li>
                  @endforeach
                </ul>

                @auth
                  <a href="{{ route('dashboard') }}" wire:navigate class="btn-primary w-full py-3.5 text-center block">
                    Accéder à mon compte
                  </a>
                @else
                  <a href="{{ route('register') }}" wire:navigate class="btn-primary w-full py-3.5 text-center block">
                    Démarrer mon essai gratuit
                  </a>
                @endauth
              </div>

            </div>
          </div>
        </section>
      </main>

      <div class="md:hidden fixed bottom-0 inset-x-0 p-4 pb-6 bg-white/90 backdrop-blur-2xl border-t border-slate-200 z-50">
        <a href="#tarifs" class="btn-primary flex justify-center items-center w-full py-3.5 text-base shadow-lg shadow-amber-500/20">
          Essai gratuit 14 jours 
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
                @foreach(['instagram', 'facebook', 'linkedin'] as $social)
                  <a href="#" class="w-9 h-9 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 hover:text-copper hover:border-copper/20 hover:bg-amber-50 transition-all">
                    <i data-lucide="{{ $social }}" class="w-4 h-4"></i>
                  </a>
                @endforeach
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
              </ul>
            </div>

            <!-- Col 3: Entreprise -->
            <div>
              <h4 class="font-display font-semibold text-midnight mb-6">Légal</h4>
              <ul class="space-y-4 text-sm">
                <li><a href="#" class="hover:text-copper transition-colors font-light">Mentions légales</a></li>
                <li><a href="#" class="hover:text-copper transition-colors font-light">CGV</a></li>
                <li><a href="#" class="hover:text-copper transition-colors font-light">Confidentialité</a></li>
                <li><a href="#" class="hover:text-copper transition-colors font-light">Contact</a></li>
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
