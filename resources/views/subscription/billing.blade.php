<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Mon abonnement
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Gérez votre abonnement et vos informations de paiement
                </p>
            </div>
            <a href="{{ route('subscription.plans') }}" wire:navigate
               class="text-sm font-semibold text-copper hover:text-amber-500 transition-colors flex items-center gap-1.5 shrink-0">
                Voir les plans
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-100 text-green-600 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Current Plan Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-midnight text-sm">Plan actuel</h3>
                    @if($currentPlan)
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">Actif</span>
                    @else
                        <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">Gratuit</span>
                    @endif
                </div>

                <div class="p-6">
                    @if($currentPlan && isset($plans[$currentPlan]))
                        @php $plan = $plans[$currentPlan]; @endphp

                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-xl font-bold text-midnight">{{ $plan['name'] }}</h4>
                                <p class="text-sm text-gray-400 mt-0.5">{{ $plan['credits'] }} posts / mois</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-midnight">{{ number_format($plan['price'], 2, ',', '') }}&euro;</p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider">/ mois</p>
                            </div>
                        </div>

                        {{-- Subscription Status Details --}}
                        @if($subscription)
                            <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                                @if($subscription->onGracePeriod())
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                                        <p class="text-sm text-orange-600 font-medium">
                                            Annulé &mdash; Accès jusqu'au {{ $subscription->ends_at->translatedFormat('d F Y') }}
                                        </p>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                        <p class="text-sm text-gray-600">
                                            Prochain renouvellement :
                                            <span class="font-semibold text-midnight">
                                                {{ $renewalDate ? $renewalDate->translatedFormat('d F Y') : 'N/A' }}
                                            </span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Usage --}}
                        <div class="mt-4 bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Utilisation ce mois</span>
                                <span class="text-sm font-bold text-midnight tabular-nums">
                                    {{ $user->used_credits }} / {{ $user->total_credits }}
                                </span>
                            </div>
                            @php
                                $usedPct = $user->total_credits > 0 ? min(100, ($user->used_credits / $user->total_credits) * 100) : 0;
                            @endphp
                            <div class="h-2 w-full bg-white rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-copper to-amber-400 rounded-full transition-all duration-500"
                                     style="width: {{ $usedPct }}%"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">{{ $user->remaining_credits }} posts restants</p>
                        </div>

                    @else
                        <div class="text-center py-4">
                            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold text-midnight text-sm">Aucun abonnement actif</h4>
                            <p class="text-xs text-gray-400 mt-1 mb-4">Vous utilisez le plan gratuit ({{ config('subscription.free_credits', 2) }} posts/mois)</p>
                            <a href="{{ route('subscription.plans') }}" wire:navigate
                               class="inline-flex items-center gap-2 bg-copper hover:bg-amber-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-lg shadow-copper/20">
                                Choisir un plan
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            @if($currentPlan && $subscription)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50">
                        <h3 class="font-semibold text-midnight text-sm">Actions</h3>
                    </div>

                    <div class="divide-y divide-gray-50">
                        {{-- Stripe Portal --}}
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-midnight">Moyens de paiement & factures</p>
                                <p class="text-xs text-gray-400 mt-0.5">Gérer vos cartes, voir vos factures sur Stripe</p>
                            </div>
                            <form method="POST" action="{{ route('subscription.portal') }}">
                                @csrf
                                <button type="submit"
                                    class="text-sm font-semibold text-copper hover:text-amber-500 transition-colors flex items-center gap-1">
                                    Ouvrir
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        {{-- Change Plan --}}
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-midnight">Changer de plan</p>
                                <p class="text-xs text-gray-400 mt-0.5">Augmenter ou modifier votre forfait</p>
                            </div>
                            <a href="{{ route('subscription.plans') }}" wire:navigate
                               class="text-sm font-semibold text-copper hover:text-amber-500 transition-colors flex items-center gap-1">
                                Voir les plans
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        {{-- Cancel / Resume --}}
                        <div class="px-6 py-4 flex items-center justify-between">
                            @if($subscription->onGracePeriod())
                                <div>
                                    <p class="text-sm font-medium text-midnight">Réactiver l'abonnement</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Annuler la résiliation et conserver votre plan</p>
                                </div>
                                <form method="POST" action="{{ route('subscription.resume') }}">
                                    @csrf
                                    <button type="submit"
                                        class="text-sm font-semibold text-green-600 hover:text-green-700 transition-colors">
                                        Réactiver
                                    </button>
                                </form>
                            @else
                                <div>
                                    <p class="text-sm font-medium text-midnight">Résilier l'abonnement</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Vous conservez l'accès jusqu'à la fin de la période</p>
                                </div>
                                <form method="POST" action="{{ route('subscription.cancel') }}">
                                    @csrf
                                    <button type="submit"
                                        class="text-sm font-semibold text-red-500 hover:text-red-600 transition-colors">
                                        Résilier
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
