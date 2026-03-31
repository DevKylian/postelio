<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Tableau de bord
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    {{ now()->isoFormat('dddd D MMMM YYYY') }}
                </p>
            </div>
            <x-primary-button :href="route('generate-post')" wire:navigate class="!rounded-xl !gap-2 !py-2.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                Générer un post
            </x-primary-button>
        </div>
    </x-slot>

    @php
        $user = auth()->user();
        $company = $user->company;

        // Real stats
        $totalPosts = $user->posts()->where('status', 'completed')->count();
        $thisMonth  = $user->posts()->where('status', 'completed')
                          ->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->count();
        $thisWeek   = $user->posts()->where('status', 'completed')
                          ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                          ->count();
        $lastMonth  = $user->posts()->where('status', 'completed')
                          ->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year)
                          ->count();
        $recentPosts = $user->posts()->where('status', 'completed')->latest()->take(4)->get();

        // Onboarding checklist
        $hasCompany   = $company && $company->name;
        $hasInstagram = $company && $company->instagram_connected;
        $hasFacebook  = $company && $company->facebook_connected;
        $hasPost      = $totalPosts > 0;
        $onboardingComplete = $hasCompany && $hasInstagram && $hasFacebook && $hasPost;

        // Month-over-month trend
        $trend = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100) : null;

        $templateLabels = [
            'review'            => 'Avis Client',
            'project'           => 'Avant/Après',
            'faq'               => 'FAQ',
            'lifestyle'         => 'Lifestyle',
            'quality_focus'     => 'Qualité',
            'chantier_detail'   => 'Chantier',
            'step_by_step'      => 'Étapes',
            'guide_pedagogique' => 'Guide',
        ];
        $steps = [$hasCompany, $hasInstagram, $hasFacebook, $hasPost];
        $done  = count(array_filter($steps));
        $onboardingComplete = ($done === 4);
    @endphp

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- ── Onboarding Banner ── --}}
            @if (!$onboardingComplete)
            <div class="glass-panel rounded-2xl overflow-hidden">
                <div class="px-6 py-5 flex items-center justify-between border-b border-gray-50">
                    <div>
                        <h3 class="font-semibold text-midnight text-sm">Démarrez avec Postelio</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Complétez votre configuration pour générer des posts professionnels</p>
                    </div>
                    <span class="badge badge-copper !text-xs !px-2.5">{{ $done }}/4</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $checklist = [
                            ['done' => $hasCompany,   'label' => 'Nom de société',    'href' => route('post-settings')],
                            ['done' => $hasInstagram, 'label' => 'Instagram lié',     'href' => route('post-settings')],
                            ['done' => $hasFacebook,  'label' => 'Facebook lié',      'href' => route('post-settings')],
                            ['done' => $hasPost,      'label' => 'Premier post créé', 'href' => route('generate-post')],
                        ];
                    @endphp
                    @foreach($checklist as $item)
                    <a href="{{ $item['href'] }}" wire:navigate
                       class="flex items-center gap-2.5 p-3 rounded-xl border transition-all duration-150 {{ $item['done'] ? 'border-green-100 bg-green-50/50' : 'border-gray-100 bg-gray-50 hover:border-copper/30 hover:bg-copper/5' }}">
                        <div class="w-5 h-5 rounded-full shrink-0 flex items-center justify-center {{ $item['done'] ? 'bg-green-500' : 'bg-white border-2 border-gray-200' }}">
                            @if($item['done'])
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </div>
                        <span class="text-xs font-medium {{ $item['done'] ? 'text-green-700 line-through decoration-green-300' : 'text-gray-600' }}">
                            {{ $item['label'] }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── Stats Row ── --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

                {{-- Total Posts --}}
                <div class="glass-panel rounded-2xl p-5 group card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all duration-200">
                            <svg class="w-4.5 h-4.5 w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        @if($totalPosts > 0)
                            <span class="badge badge-gray !bg-blue-50 !text-blue-500 !border-blue-100 !text-[11px] !px-2">Total</span>
                        @endif
                    </div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Posts générés</p>
                    <p class="text-2xl font-bold text-midnight mt-1">{{ $totalPosts }}</p>
                </div>

                {{-- Ce mois --}}
                <div class="glass-panel rounded-2xl p-5 group card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-xl bg-copper/10 flex items-center justify-center text-copper group-hover:bg-copper group-hover:text-white transition-all duration-200">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @if($trend !== null)
                            <span class="badge !text-[11px] !px-2 {{ $trend >= 0 ? 'badge-success' : 'badge-error' }}">
                                {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Ce mois</p>
                    <p class="text-2xl font-bold text-midnight mt-1">{{ $thisMonth }}</p>
                </div>

                {{-- Objectif Hebdomadaire --}}
                <div class="glass-panel rounded-2xl p-5 group card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 shrink-0 bg-amber-50 text-amber-500 group-hover:bg-amber-500 group-hover:text-white">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        @php $weeklyGoal = $company?->weekly_posts_goal ?? 3; @endphp
                        @if($weeklyGoal > 0)
                            @if($thisWeek >= $weeklyGoal)
                                <span class="badge badge-success !text-[11px] !px-2">Atteint 🎉</span>
                            @else
                                <span class="badge badge-gray !text-[11px] !px-2">En cours</span>
                            @endif
                        @else
                            <span class="badge badge-gray !text-[11px] !px-2">Non défini</span>
                        @endif
                    </div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Objectif hebdo</p>
                    <div class="flex items-baseline gap-1 mt-1">
                        <p class="text-2xl font-bold text-midnight">{{ $thisWeek }}</p>
                        <p class="text-sm font-semibold text-gray-400">/ {{ $weeklyGoal }}</p>
                    </div>
                </div>

                {{-- Instagram --}}
                <div class="glass-panel rounded-2xl p-5 group card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                             style="background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888)">
                            <svg class="w-[18px] h-[18px] text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </div>
                        @if($hasInstagram)
                            <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Connecté</span>
                        @else
                            <span class="text-[11px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Non lié</span>
                        @endif
                    </div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Instagram</p>
                    <p class="text-sm font-bold text-midnight mt-1 truncate">
                        {{ $company?->instagram_username ? '@'.$company->instagram_username : 'Non connecté' }}
                    </p>
                </div>

                {{-- Facebook --}}
                <div class="glass-panel rounded-2xl p-5 group card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-[#1877F2]">
                            <svg class="w-[18px] h-[18px] text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                        @if($hasFacebook)
                            <span class="badge badge-success !text-[11px] !px-2">Connecté</span>
                        @else
                            <span class="badge badge-gray !text-[11px] !px-2">Non lié</span>
                        @endif
                    </div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Facebook</p>
                    <p class="text-sm font-bold text-midnight mt-1 truncate">
                        {{ $company?->facebook_page_name ?? 'Non connecté' }}
                    </p>
                </div>
            </div>

            {{-- ── Main Grid ── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Recent Creations (2/3) --}}
                <div class="lg:col-span-2">
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                            <h3 class="font-semibold text-midnight text-sm">Dernières créations</h3>
                            @if($totalPosts > 0)
                            <a href="{{ route('posts.index') }}" wire:navigate
                               class="text-xs font-semibold text-copper hover:text-amber-600 transition-colors flex items-center gap-1">
                                Tout voir
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            @endif
                        </div>

                        @if($recentPosts->isEmpty())
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-midnight text-sm mb-1">Aucune création pour l'instant</h4>
                                <p class="text-xs text-gray-400 mb-4">Générez votre premier post en quelques secondes.</p>
                                <x-primary-button :href="route('generate-post')" wire:navigate class="!text-xs !py-2 !px-4">
                                    Créer mon premier post
                                </x-primary-button>
                            </div>
                        @else
                            <div class="p-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($recentPosts as $post)
                                @php
                                    $slides = $post->slides ?? [];
                                    $thumb = count($slides) > 0 ? Storage::url($slides[0]) : ($post->image_path ? Storage::url($post->image_path) : null);
                                @endphp
                                <a href="{{ route('posts.index') }}" wire:navigate
                                   class="group relative aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-100 hover:border-copper/30 transition-all duration-200">
                                    @if($thumb)
                                        <img src="{{ $thumb }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-2 translate-y-full group-hover:translate-y-0 transition-transform duration-200">
                                        <p class="text-white text-[10px] font-semibold truncate">{{ $templateLabels[$post->template] ?? 'Post' }}</p>
                                        <p class="text-white/60 text-[9px]">{{ $post->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    @if(count($slides) > 1)
                                        <div class="absolute top-2 right-2 bg-black/50 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">
                                            {{ count($slides) }}
                                        </div>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions (1/3) --}}
                <div class="space-y-4">
                    {{-- Welcome --}}
                    <div class="glass-panel-dark rounded-2xl p-5 relative overflow-hidden">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-copper/10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-6 -left-6 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
                        <div class="relative">
                            <p class="text-white/50 text-xs font-medium mb-1">Bonjour,</p>
                            <h3 class="text-white font-bold text-lg leading-tight">{{ explode(' ', $user->name)[0] }} 👋</h3>
                            <p class="text-white/40 text-xs mt-2 leading-relaxed">
                                Prêt à créer du contenu qui attire de nouveaux clients ?
                            </p>
                        </div>
                    </div>

                    {{-- Quota Card --}}
                    <div class="glass-panel rounded-2xl p-5 group relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-16 h-16 bg-copper/5 rounded-full blur-xl"></div>
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-copper animate-pulse"></div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest leading-none">Usage Quota</p>
                            </div>
                            <span class="text-[13px] font-bold text-midnight tabular-nums leading-none flex items-center">
                                @if(auth()->user()->is_admin || auth()->user()->is_premium)
                                    <span class="text-lg leading-none">∞</span>
                                @else
                                    {{ auth()->user()->remaining_credits }} <span class="text-gray-400 font-medium ml-1">/ {{ auth()->user()->total_credits }}</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="h-2 w-full bg-slate-100/50 rounded-full shadow-inner flex relative">
                            @php
                                $remaining = auth()->user()->remaining_credits;
                                $total = max(1, auth()->user()->total_credits);
                                $isUnlimited = auth()->user()->is_admin || auth()->user()->is_premium;
                                $percentage = $isUnlimited ? 100 : min(100, ($remaining / $total) * 100);
                                $colorClass = $isUnlimited ? 'from-amber-400 to-copper' : ($percentage < 15 ? 'from-red-500 to-rose-600' : ($percentage < 40 ? 'from-orange-400 to-amber-500' : 'from-amber-400 to-copper'));
                                $shadowColor = $percentage < 15 ? 'rgba(239,68,68,0.4)' : 'rgba(245,158,11,0.4)';
                            @endphp
                            <div class="absolute h-full bg-gradient-to-r {{ $colorClass }} transition-all duration-1000 rounded-full"
                                 style="width: {{ $percentage }}%; box-shadow: 0 0 10px {{ $shadowColor }};">
                            </div>
                        </div>
                        
                        <div class="mt-3 flex items-center justify-between">
                            <p class="text-[10px] text-gray-400 font-medium">Usage mensuel</p>
                            <span class="text-[10px] font-bold text-copper">{{ isset($isUnlimited) && $isUnlimited ? 'Génération illimitée' : round($percentage) . '% restant' }}</span>
                        </div>
                    </div>


                </div>

            </div>

        </div>
    </div>
</x-app-layout>
