<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                Avant de partir...
            </h1>
            <p class="text-gray-400 text-sm mt-0.5">
                Nous avons une offre spéciale pour vous
            </p>
        </div>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">

            {{-- Offer Card --}}
            <div class="relative bg-white rounded-3xl overflow-hidden shadow-2xl shadow-copper/10"
                 style="border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(135deg, #f97316, #fbbf24, #f97316); background-origin: border-box; background-clip: padding-box, border-box;">

                {{-- Badge --}}
                <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                    <div class="bg-gradient-to-r from-copper to-amber-500 text-white text-xs font-black px-5 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-copper/30 whitespace-nowrap">
                        Offre de fidélité exclusive
                    </div>
                </div>

                <div class="pt-10 pb-8 px-8 sm:px-12">

                    {{-- Icon + Headline --}}
                    <div class="text-center mb-8">
                        <div class="text-5xl mb-4">🎁</div>
                        <h2 class="text-2xl sm:text-3xl font-black text-midnight tracking-tight leading-tight">
                            On vous offre <span class="text-transparent bg-clip-text bg-gradient-to-r from-copper to-amber-500">-50%</span><br>
                            sur votre prochain mois
                        </h2>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed max-w-md mx-auto">
                            Avant de résilier, laissez-nous une chance de vous montrer la valeur de Postelio.
                            Cette réduction sera appliquée automatiquement sur votre prochaine facture.
                        </p>
                    </div>

                    {{-- Current plan summary --}}
                    @if($currentPlanData)
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 rounded-2xl p-5 mb-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Votre plan {{ $currentPlanData['name'] }}</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black text-midnight">{{ number_format($currentPlanData['price'] / 2, 2, ',', '') }}€</span>
                                    <span class="text-sm text-gray-400 line-through">{{ number_format($currentPlanData['price'], 2, ',', '') }}€</span>
                                    <span class="text-xs font-bold text-gray-400">/ prochain mois</span>
                                </div>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-copper to-amber-500 flex items-center justify-center shadow-lg shadow-copper/30 flex-shrink-0">
                                <span class="text-white font-black text-sm leading-tight text-center">-50%</span>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2 text-xs text-amber-700 font-medium">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Réduction automatique, sans code promo. Valable une seule fois.
                        </div>
                    </div>
                    @endif

                    {{-- Features reminder --}}
                    <div class="mb-8">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 text-center">Ce que vous gardez</p>
                        <div class="grid grid-cols-2 gap-3">
                            @if($currentPlanData)
                                @foreach(array_slice($currentPlanData['features'], 0, 4) as $feature)
                                <div class="flex items-center gap-2 bg-slate-50 rounded-xl px-3 py-2.5">
                                    <div class="w-4 h-4 rounded-full bg-copper/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-2.5 h-2.5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium text-slate-600 leading-tight">{{ $feature }}</span>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- CTA: Accept --}}
                    <form method="POST" action="{{ route('subscription.accept-retention-offer') }}" class="mb-4">
                        @csrf
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-copper to-amber-500 hover:from-amber-500 hover:to-copper text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-copper/25 transition-all duration-200 hover:shadow-xl hover:shadow-copper/30 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-3 text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Oui, je veux profiter des -50% et rester
                        </button>
                    </form>

                    {{-- CTA: Decline --}}
                    <form method="POST" action="{{ route('subscription.decline-retention-offer') }}"
                          x-data="{ confirm: false }"
                          @submit.prevent="confirm ? $el.submit() : (confirm = true)">
                        @csrf
                        <button type="submit"
                            class="w-full text-center py-3 px-6 rounded-2xl transition-all duration-200 text-sm font-medium"
                            :class="confirm
                                ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-semibold'
                                : 'text-gray-400 hover:text-gray-600'"
                            x-text="confirm ? 'Confirmer la résiliation' : 'Non merci, résilier quand même'">
                        </button>
                        <p x-show="confirm" x-transition class="mt-2 text-center text-xs text-gray-400">
                            Vous conserverez l'accès jusqu'à la fin de la période en cours.
                        </p>
                    </form>

                </div>
            </div>

            {{-- Reassurance --}}
            <div class="mt-8 flex items-center justify-center gap-6 text-xs text-gray-400 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Aucun engagement supplémentaire
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Réduction appliquée automatiquement
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Annulable à tout moment après
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
