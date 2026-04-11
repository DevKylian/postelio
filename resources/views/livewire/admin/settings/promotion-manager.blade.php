<div class="py-12">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <div>
                <h2 class="font-display font-semibold text-2xl text-midnight leading-tight">Gestion des promotions</h2>
                <p class="text-xs text-slate-400 mt-0.5">Prix barrés sur les pages de tarifs + code promo pré-appliqué au paiement.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session()->has('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-slate-200/60">
            <div class="p-8">
                <form wire:submit="save" class="space-y-8">

                    {{-- Toggle --}}
                    <div class="flex items-center justify-between p-6 rounded-3xl border-2 transition-all {{ $promo_active ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-100' }}">
                        <div>
                            <h4 class="text-sm font-bold text-midnight">Promotion active</h4>
                            <p class="text-xs text-slate-500 mt-1">
                                Affiche les prix barrés et pré-applique le code au paiement.
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="promo_active" class="sr-only peer">
                            <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-500 shadow-inner"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        {{-- Discount % --}}
                        <div>
                            <label class="block text-sm font-bold text-midnight mb-2">Réduction (%)</label>
                            <div class="relative">
                                <input type="number" wire:model="promo_discount" min="1" max="99"
                                       class="w-full px-4 py-3 pr-10 rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 transition-all shadow-sm"
                                       placeholder="20">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">%</span>
                            </div>
                            @error('promo_discount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Label --}}
                        <div>
                            <label class="block text-sm font-bold text-midnight mb-2">Label affiché</label>
                            <input type="text" wire:model="promo_label" maxlength="30"
                                   class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 transition-all shadow-sm uppercase"
                                   placeholder="LANCEMENT">
                            @error('promo_label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    {{-- Stripe Promotion Code ID --}}
                    <div>
                        <label class="block text-sm font-bold text-midnight mb-2">
                            ID du code promo Stripe
                            <span class="font-normal text-slate-400">(optionnel — pré-appliqué au checkout)</span>
                        </label>
                        <input type="text" wire:model="promo_stripe_promotion_code_id"
                               class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 transition-all shadow-sm font-mono text-sm"
                               placeholder="promo_1Abc123…">
                        <p class="text-xs text-slate-400 mt-1.5">
                            Trouvez cet ID dans le dashboard Stripe → Coupons → votre coupon → Promotion codes.
                            Si vide, le code reste saisissable manuellement par l'utilisateur.
                        </p>
                    </div>

                    {{-- Preview --}}
                    @if($promo_active && $promo_discount > 0)
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 space-y-4">
                        <h4 class="text-sm font-bold text-midnight flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-500"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            Aperçu des prix après réduction
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($plans as $key => $plan)
                                @if($plan['price'] > 0)
                                @php
                                    $monthly = $plan['price'];
                                    $yearly  = $plan['yearly_price'] > 0 ? round($plan['yearly_price'] / 12) : 0;
                                    $monthlyDiscounted = round($monthly * (1 - $promo_discount / 100));
                                    $yearlyDiscounted  = $yearly > 0 ? round($yearly * (1 - $promo_discount / 100)) : 0;
                                @endphp
                                <div class="bg-white rounded-2xl p-4 border border-slate-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">{{ $plan['name'] }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-slate-400 line-through">{{ $monthly }}€</span>
                                        <span class="text-xl font-black text-amber-600">{{ $monthlyDiscounted }}€</span>
                                        <span class="text-xs text-slate-400">/mois</span>
                                    </div>
                                    @if($yearlyDiscounted > 0)
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-slate-400 line-through">{{ $yearly }}€</span>
                                        <span class="text-sm font-bold text-amber-600">{{ $yearlyDiscounted }}€</span>
                                        <span class="text-xs text-slate-400">/mois (annuel)</span>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Save --}}
                    <div class="pt-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
                        <p class="text-xs text-slate-500 italic flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            Les modifications sont appliquées instantanément sur toutes les pages.
                        </p>
                        <button type="submit"
                                class="w-full md:w-auto px-10 py-3.5 bg-midnight text-white text-sm font-bold rounded-2xl hover:bg-slate-800 transition-all shadow-lg hover:shadow-xl active:scale-95 flex items-center justify-center gap-2">
                            Enregistrer
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
