<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
            {{ __('Gabarits de publications') }}
        </h1>
        <p class="text-gray-500 text-sm mt-1">Découvrez les styles visuels que Postelio peut créer pour vous.</p>
    </x-slot>

    @php
        $company    = auth()->user()->company;
        $brandColor = $company?->brand_color ?? '#F59E0B';
        $companyName = $company?->name ?? 'Votre Société';
        $companyCity = $company?->city ?? '';
        $companyLabel = $companyName . ($companyCity ? '  ·  ' . strtoupper($companyCity) : '');
        $hasLogo    = $company?->logo_path && Storage::disk('public')->exists($company->logo_path);
    @endphp

    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- Banner si le branding n'est pas configuré --}}
            @if (!$company || !$company->name)
            <div class="mb-8 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-5 py-4 text-sm">
                <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>
                    Les aperçus utilisent des valeurs par défaut.
                    <a href="{{ route('post-settings') }}" wire:navigate class="font-semibold underline hover:text-amber-900">
                        Configurez votre branding
                    </a>
                    pour voir vos vraies couleurs et nom de société.
                </span>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                {{-- ── Gabarit Avant/Après ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 overflow-hidden flex flex-col relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2">
                        <div class="flex-1 flex">
                            <div class="w-1/2 relative overflow-hidden border-r-2 border-white">
                                <img src="https://images.unsplash.com/photo-1541604193435-225878999ba0?q=80&w=600&h=600&auto=format&fit=crop" alt="Avant" class="w-full h-full object-cover grayscale-[20%] group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="w-1/2 relative overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1621905252507-b354bcadcabc?q=80&w=600&h=600&auto=format&fit=crop" alt="Après" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                        </div>
                        {{-- Bandeau marque --}}
                        <div class="shrink-0 flex flex-col items-center justify-center px-6 py-2 gap-0.5" style="background-color: {{ $brandColor }}">
                            <p class="text-white/80 font-bold uppercase tracking-widest text-[10px] font-montserrat">{{ $companyLabel }}</p>
                            <div class="flex w-full items-center justify-between">
                                <span class="text-white font-black uppercase tracking-widest text-base font-montserrat">Avant</span>
                                <span class="text-white font-black uppercase tracking-widest text-base font-montserrat">Après</span>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-midnight/0 group-hover:bg-midnight/10 transition-colors pointer-events-none"></div>
                    </div>
                    <div>
                        <h4 class="font-bold text-midnight text-lg">Avant / Après</h4>
                        <p class="text-gray-500 text-sm">Parfait pour valoriser vos rénovations et finitions.</p>
                    </div>
                </div>

                {{-- ── Gabarit Avis Client ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square rounded-2xl shadow-lg flex flex-col items-center justify-center p-10 text-center relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2 overflow-hidden" style="background-color: {{ $brandColor }}">
                        {{-- Étoiles --}}
                        <div class="flex space-x-2 mb-6">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="w-7 h-7 text-yellow-300 fill-current drop-shadow-md" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        {{-- Texte --}}
                        <p class="text-white font-black uppercase tracking-tight text-lg leading-tight font-montserrat drop-shadow-sm flex-1 flex items-center">
                            "Travail impeccable ! L'équipe a été ponctuelle, propre et le résultat dépasse nos attentes."
                        </p>
                        {{-- Footer société --}}
                        <div class="mt-6 pt-4 border-t border-white/20 w-full">
                            <div class="flex items-center justify-center gap-2">
                                @if ($hasLogo)
                                    <img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-6 w-6 object-contain rounded">
                                @endif
                                <p class="text-white/80 font-bold text-xs uppercase tracking-widest font-montserrat">{{ $companyLabel }}</p>
                            </div>
                        </div>
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-black/10 rounded-full blur-2xl pointer-events-none"></div>
                    </div>
                    <div>
                        <h4 class="font-bold text-midnight text-lg">Avis Client</h4>
                        <p class="text-gray-500 text-sm">Transformez vos témoignages en preuves sociales fortes.</p>
                    </div>
                </div>

                {{-- ── Gabarit FAQ ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square bg-midnight rounded-2xl shadow-lg flex flex-col p-10 relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2 overflow-hidden" style="border-top: 8px solid {{ $brandColor }}">
                        <div class="flex-1 flex flex-col justify-center">
                            <span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span>
                            <h3 class="font-black uppercase tracking-widest text-sm mb-4" style="color: {{ $brandColor }}">FAQ Artisan</h3>
                            <p class="text-white font-black uppercase tracking-tight text-xl leading-snug font-montserrat">
                                Pourquoi choisir une isolation par l'extérieur ?
                            </p>
                        </div>
                        <div class="pt-4 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                @if ($hasLogo)
                                    <img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-5 w-5 object-contain rounded">
                                @endif
                                <p class="font-bold text-xs uppercase tracking-widest font-montserrat" style="color: {{ $brandColor }}">
                                    {{ $companyLabel }}
                                </p>
                            </div>
                        </div>
                        <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-white/5 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-midnight text-lg">FAQ Artisan</h4>
                        <p class="text-gray-500 text-sm">Éduquez vos clients et affirmez votre professionnalisme.</p>
                    </div>
                </div>

                {{-- ── Gabarit Lifestyle ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 overflow-hidden relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?q=80&w=800&h=800&auto=format&fit=crop" alt="Lifestyle" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        {{-- Overlay branding --}}
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent pt-10 pb-4 px-5">
                            <div class="w-full" style="border-top: 3px solid {{ $brandColor }}; padding-top: 8px;">
                                <div class="flex items-center gap-2">
                                    @if ($hasLogo)
                                        <img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-5 w-5 object-contain rounded">
                                    @endif
                                    <p class="text-white font-bold text-xs uppercase tracking-widest font-montserrat">{{ $companyLabel }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-midnight text-lg">Lifestyle</h4>
                        <p class="text-gray-500 text-sm">Une immersion brute dans votre métier, sans artifices.</p>
                    </div>
                </div>

            </div>

            {{-- Call to action branding --}}
            <div class="mt-12 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white shadow ring-1 ring-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl shrink-0 shadow-md" style="background-color: {{ $brandColor }}"></div>
                    <div>
                        <p class="font-semibold text-midnight">Votre branding est appliqué à tous vos posts</p>
                        <p class="text-sm text-gray-500">Couleur, nom de société et logo apparaissent sur chaque visuel généré.</p>
                    </div>
                </div>
                <a href="{{ route('post-settings') }}" wire:navigate
                   class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                   style="background-color: {{ $brandColor }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Modifier le branding
                </a>
            </div>

        </div>
    </div>

    @push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@800;900&display=swap');
        .font-montserrat { font-family: 'Montserrat', sans-serif; }
    </style>
    @endpush
</x-app-layout>
