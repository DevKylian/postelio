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

            {{-- Manage Subscription (if active) --}}
            @if(isset($subscription) && $subscription && $subscription->valid())
                <div class="mb-12 glass-panel rounded-3xl p-6 flex flex-col sm:flex-row items-center justify-between gap-6 border-copper/20 bg-gradient-to-r from-copper/5 to-transparent">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-copper/10 flex items-center justify-center text-copper">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-midnight">Vous avez un abonnement actif</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Gérez vos factures, votre mode de paiement ou résiliez.</p>
                        </div>
                    </div>
                    <a href="{{ route('subscription.billing') }}" wire:navigate>
                        <x-primary-button type="button" class="!gap-2">
                            Gérer mon abonnement
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </x-primary-button>
                    </a>
                </div>
            @endif

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="mb-8 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="mb-8 bg-green-50 border border-green-100 text-green-600 px-4 py-3 rounded-2xl text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    {{ session('success') }}
                </div>
            @endif


            {{-- Pricing Toggle --}}
            <div x-data="{ billingPeriod: 'monthly' }">
                <div class="flex justify-center items-center gap-4 mb-12">
                    <span class="text-sm font-bold transition-colors duration-300" :class="billingPeriod === 'monthly' ? 'text-midnight' : 'text-gray-400'">Mensuel</span>
                    <button @click="billingPeriod = billingPeriod === 'monthly' ? 'yearly' : 'monthly'" 
                            class="relative w-14 h-7 rounded-full transition-all duration-300 focus:outline-none ring-offset-2 focus:ring-2 focus:ring-copper/50"
                            :class="billingPeriod === 'yearly' ? 'bg-copper' : 'bg-slate-200'">
                        <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full transition-transform duration-300 shadow-md"
                             :class="billingPeriod === 'yearly' ? 'translate-x-7' : ''"></div>
                    </button>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold transition-colors duration-300" :class="billingPeriod === 'yearly' ? 'text-midnight' : 'text-gray-400'">Annuel</span>
                        <span class="bg-green-100 text-green-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-tighter border border-green-200 shadow-sm animate-pulse">-20%</span>
                    </div>
                </div>

                {{-- Plans Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch pt-4">
                @foreach($plans as $key => $plan)
                    @php
                        $isCurrent = ($currentPlan === $key) || (!$currentPlan && $key === 'free');
                        $isPopular = $key === 'pro';
                        $isFree = $key === 'free';
                    @endphp

                    <div class="group flex flex-col h-full relative bg-white rounded-3xl overflow-hidden transition-all duration-300 hover:-translate-y-1 {{ $isPopular ? 'shadow-xl shadow-copper/10 hover:shadow-2xl hover:shadow-copper/30 z-0' : 'border border-slate-200/60 shadow-md hover:shadow-xl' }}"
                         @if($isPopular)
                         style="border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(to bottom right, #f97316, #fbbf24); background-origin: border-box; background-clip: padding-box, border-box;"
                         @endif
                    >

                        {{-- Labels --}}
                        @if($isPopular)
                            <div class="absolute top-0 right-0 bg-gradient-to-r from-copper to-amber-500 text-white text-[10px] font-black px-4 py-1.5 rounded-bl-2xl uppercase tracking-widest shadow-sm z-10">
                                Meilleure Offre
                            </div>
                        @elseif($isCurrent)
                            <div class="absolute top-0 right-0 bg-midnight text-white text-[10px] font-black px-4 py-1.5 rounded-bl-2xl uppercase tracking-widest shadow-sm z-10">
                                Plan actuel
                            </div>
                        @endif

                        <div class="p-8 flex flex-col flex-grow">
                            {{-- Header --}}
                            <div class="mb-8">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 shadow-sm {{ $isFree ? 'bg-slate-50 border border-slate-100' : ($isPopular ? 'bg-amber-50 border border-amber-100' : 'bg-blue-50 border border-blue-100') }}">
                                        @if($isFree)
                                            <span class="text-2xl">⚡</span>
                                        @elseif($isPopular)
                                            <span class="text-2xl">👑</span>
                                        @else
                                            <span class="text-2xl">🚀</span>
                                        @endif
                                    </div>
                                <h3 class="text-xl font-bold text-midnight">{{ $plan['name'] }}</h3>
                                <div class="mt-4 flex items-baseline gap-1">
                                    <span class="text-4xl font-black text-midnight tracking-tighter" x-text="billingPeriod === 'yearly' ? '{{ $plan['yearly_price'] > 0 ? number_format($plan['yearly_price'] / 12, 0, ',', '.') : 0 }}€' : '{{ number_format($plan['price'], 0, ',', '.') }}€'">{{ number_format($plan['price'], 0, ',', '.') }}€</span>
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest" x-show="!{{ $isFree ? 'true' : 'false' }}">/ mois</span>
                                </div>
                                @if(!$isFree && $plan['price'] > 0 && $plan['yearly_price'] > 0)
                                    <template x-if="billingPeriod === 'yearly'">
                                        <p class="text-xs text-gray-400 mt-1">soit {{ number_format($plan['yearly_price'], 0, ',', '.') }}€ facturé par an</p>
                                    </template>
                                @endif
                                @if(isset($plan['old_price']) && $plan['old_price'] > 0)
                                    <p class="text-[11px] font-bold text-gray-400 line-through mt-0.5">{{ number_format($plan['old_price'], 2, ',', '') }}&euro;</p>
                                @endif
                            </div>

                            {{-- Divider --}}
                            <div class="h-px bg-slate-50 mb-8"></div>

                            {{-- Features --}}
                            <ul class="space-y-4 mb-10 flex-grow">
                                @foreach($plan['features'] as $feature)
                                    <li class="flex items-start gap-3">
                                        <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-copper/10 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-medium text-slate-600 leading-relaxed">{{ $feature }}</span>
                                    </li>
                                @endforeach
                                @foreach($plan['disabled_features'] ?? [] as $feature)
                                    <li class="flex items-start gap-3 opacity-70">
                                        <div class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-medium text-slate-500 leading-relaxed line-through">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- CTA --}}
                            <div class="mt-auto">
                                @if($isCurrent)
                                    <div class="w-full text-center py-4 px-6 rounded-2xl border border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase tracking-widest">
                                        Plan actuel
                                    </div>
                                @elseif($key === 'free')
                                    <div class="w-full text-center py-4 px-6 rounded-2xl border border-slate-100 bg-slate-50 text-slate-400 font-bold text-xs uppercase tracking-widest">
                                        Inclus par défaut
                                    </div>
                                @else
                                    <form method="POST" action="{{ $currentPlan ? route('subscription.change-plan') : route('subscription.checkout') }}">
                                        @csrf
                                        <input type="hidden" name="plan" value="{{ $key }}">
                                        <input type="hidden" name="period" :value="billingPeriod">
                                        <x-primary-button type="submit" class="w-full justify-center py-4 !rounded-2xl {{ $isPopular ? 'shadow-lg shadow-copper/20' : '' }}">
                                            {{ $currentPlan ? 'Changer de plan' : 'Commencer' }}
                                        </x-primary-button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Anti-trap reassurance -->
            <div class="flex items-center justify-center mt-12 animate-fade-in px-4">
                <div class="flex sm:inline-flex items-center justify-center gap-2 text-center px-4 py-3 sm:py-2 rounded-2xl sm:rounded-full bg-slate-50 border border-slate-100 shadow-sm mx-auto">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)] shrink-0"></span>
                    <span class="text-[11px] sm:text-xs font-bold text-slate-500 leading-tight">Sans engagement. Annulez en 1 clic à tout moment.</span>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="mt-16 glass-panel rounded-3xl p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-8 h-8 rounded-xl bg-midnight/5 flex items-center justify-center text-midnight">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="font-bold text-midnight tracking-tight">Questions fréquentes</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h4 class="text-sm font-bold text-midnight mb-2">Puis-je changer ?</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Oui, upgradez ou downgradez à tout moment. La différence est proratisée par Stripe.</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-midnight mb-2">Comment annuler ?</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Depuis votre espace facturation, en un seul clic et sans justificatif. Vous gardez l'accès jusqu'à la fin de la période en cours.</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-midnight mb-2">Les posts non utilisés ?</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Votre quota est réinitialisé chaque mois à la date anniversaire. Pas de report possible.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</x-app-layout>
