<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                Choisir un abonnement
            </h1>
            <p class="text-gray-400 text-sm mt-0.5">
                Sélectionnez le plan qui correspond à vos besoins
            </p>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-100 text-green-600 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Free Tier Info --}}
            @if(!$currentPlan)
                <div class="mb-8 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full uppercase tracking-wider">Plan actuel</span>
                            <span class="text-sm font-bold text-midnight">Gratuit</span>
                        </div>
                        <p class="text-xs text-gray-400">{{ config('subscription.free_credits', 3) }} posts par mois inclus</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-midnight">0&euro;</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">/ mois</p>
                    </div>
                </div>
            @endif

            {{-- Plans Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($plans as $key => $plan)
                    @php
                        $isCurrent = $currentPlan === $key;
                        $isPopular = $key === 'pro';
                    @endphp

                    <div class="relative bg-white rounded-2xl border shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md
                        {{ $isPopular ? 'border-copper ring-2 ring-copper/20' : 'border-gray-100' }}
                        {{ $isCurrent ? 'ring-2 ring-green-500/30 border-green-200' : '' }}">

                        {{-- Popular Badge --}}
                        @if($isPopular && !$isCurrent)
                            <div class="absolute top-0 right-0 bg-copper text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                                Populaire
                            </div>
                        @endif

                        {{-- Current Badge --}}
                        @if($isCurrent)
                            <div class="absolute top-0 right-0 bg-green-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                                Actif
                            </div>
                        @endif

                        <div class="p-6">
                            {{-- Plan Header --}}
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-midnight">{{ $plan['name'] }}</h3>
                                <div class="mt-3 flex items-baseline gap-1">
                                    <span class="text-3xl font-bold text-midnight">{{ number_format($plan['price'], 2, ',', '') }}&euro;</span>
                                    <span class="text-sm text-gray-400">/ mois</span>
                                </div>
                                <p class="mt-2 text-xs text-gray-400">{{ $plan['credits'] }} posts par mois</p>
                            </div>

                            {{-- Features --}}
                            <ul class="space-y-3 mb-8">
                                @foreach($plan['features'] as $feature)
                                    <li class="flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-copper shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- CTA Button --}}
                            @if($isCurrent)
                                <div class="w-full text-center py-2.5 px-4 rounded-xl border-2 border-green-200 bg-green-50 text-green-700 font-semibold text-sm">
                                    Plan actuel
                                </div>
                            @elseif($currentPlan)
                                <form method="POST" action="{{ route('subscription.change-plan') }}">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{ $key }}">
                                    <button type="submit"
                                        class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm transition-all duration-200
                                        {{ $isPopular ? 'bg-copper hover:bg-amber-500 text-white shadow-lg shadow-copper/20' : 'bg-midnight hover:bg-slate-800 text-white' }}">
                                        Changer vers {{ $plan['name'] }}
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('subscription.checkout') }}">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{ $key }}">
                                    <button type="submit"
                                        class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm transition-all duration-200
                                        {{ $isPopular ? 'bg-copper hover:bg-amber-500 text-white shadow-lg shadow-copper/20' : 'bg-midnight hover:bg-slate-800 text-white' }}">
                                        Commencer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FAQ --}}
            <div class="mt-12 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-midnight text-base mb-4">Questions fréquentes</h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-midnight">Puis-je changer de plan à tout moment ?</h4>
                        <p class="text-xs text-gray-400 mt-1">Oui, vous pouvez upgrader ou downgrader votre abonnement à tout moment. La différence sera proratisée.</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-midnight">Comment fonctionne l'annulation ?</h4>
                        <p class="text-xs text-gray-400 mt-1">Vous conservez l'accès jusqu'à la fin de votre période de facturation. Aucun remboursement partiel.</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-midnight">Les crédits non utilisés sont-ils reportés ?</h4>
                        <p class="text-xs text-gray-400 mt-1">Non, les crédits sont réinitialisés chaque mois à la date de renouvellement.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
