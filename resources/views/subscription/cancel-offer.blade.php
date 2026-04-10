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

    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">

            {{--
                Gradient border via background-clip trick (single element, no wrapper needed).
                border: 2px solid transparent → defines the border area.
                background-clip: padding-box (white fill inside) + border-box (gradient in border strip).
                This gives a perfectly uniform border width, including at rounded corners.
            --}}
            <div class="mt-5 relative">

                {{-- Badge sits in the outer wrapper, above the bordered card --}}
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 z-10">
                    <span class="inline-block bg-gradient-to-r from-orange-500 to-copper text-white text-xs font-black px-5 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-copper/30 whitespace-nowrap">
                        Offre de fidélité exclusive
                    </span>
                </div>

                {{-- Bordered card --}}
                <div class="rounded-3xl shadow-xl shadow-copper/10 pt-10 pb-8 px-5 sm:px-12 overflow-hidden"
                     style="border: 2px solid transparent;
                            background-image: linear-gradient(#ffffff, #ffffff), linear-gradient(135deg, #f97316, #F59E0B);
                            background-origin: border-box;
                            background-clip: padding-box, border-box;">

                    {{-- Icon + Headline --}}
                    <div class="text-center mb-8">
                        <div class="text-5xl mb-4">🎁</div>
                        <h2 class="text-2xl sm:text-3xl font-black text-midnight tracking-tight leading-tight">
                            On vous offre <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-copper">-50%</span><br>
                            sur votre prochain mois
                        </h2>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed max-w-md mx-auto">
                            Avant de résilier, laissez-nous une chance de vous montrer la valeur de Postelio.
                            Cette réduction sera appliquée automatiquement sur votre prochaine facture.
                        </p>
                    </div>

                    {{-- Current plan summary --}}
                    @if($currentPlanData)
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50/50 border border-amber-100 rounded-3xl p-6 sm:p-8 mb-8 relative overflow-hidden shadow-sm text-center">
                        {{-- Centered Text Layout --}}
                        <div class="relative z-10">
                            <p class="text-[10px] font-bold text-amber-700/60 uppercase tracking-[0.2em] mb-3">Offre spéciale • Plan {{ $currentPlanData['name'] }}</p>
                            <div class="flex flex-col items-center">
                                <div class="flex items-baseline gap-2 mb-2">
                                    <span class="text-4xl sm:text-6xl font-black text-midnight tracking-tighter">{{ number_format($currentPlanData['price'] / 2, 2, ',', '') }}€</span>
                                    <span class="text-xs sm:text-base text-gray-400 line-through opacity-60">{{ number_format($currentPlanData['price'], 2, ',', '') }}€</span>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-white/80 border border-amber-200/50 rounded-xl shadow-sm">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-[10px] sm:text-[11px] font-black text-amber-800 uppercase tracking-widest">Réduction automatique</span>
                                </div>
                                <p class="mt-4 text-xs text-gray-400 font-bold uppercase tracking-wide">Valable sur votre prochain mois</p>
                            </div>
                        </div>
                        
                        {{-- Subtle background decoration --}}
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-orange-200/10 rounded-full blur-3xl"></div>
                        <div class="absolute -left-6 -bottom-6 w-32 h-32 bg-amber-200/10 rounded-full blur-3xl"></div>
                    </div>
                    @endif

                    {{-- Features reminder --}}
                    @if($currentPlanData && !empty($currentPlanData['features']))
                    <div class="mb-8">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 text-center">Ce que vous gardez</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach(array_slice($currentPlanData['features'], 0, 4) as $feature)
                            <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                                <div class="w-5 h-5 rounded-full bg-copper/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-[11px] font-semibold text-slate-600 leading-tight">{{ $feature }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- CTA: Accept --}}
                    <form method="POST" action="{{ route('subscription.accept-retention-offer') }}" class="mb-4">
                        @csrf
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-copper hover:from-copper hover:to-orange-500 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-copper/25 transition-all duration-200 hover:shadow-xl hover:shadow-copper/30 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-3 text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Oui, je veux profiter des -50% et rester
                        </button>
                    </form>

                    {{-- CTA: Decline (double click confirmation) --}}
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
            <div class="mt-8 mb-4 flex items-center justify-center gap-6 text-xs text-gray-400 flex-wrap">
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
