<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
            {{ __('Gabarits de publications') }}
        </h1>
        <p class="text-gray-500 text-sm mt-1">Découvrez les styles visuels que Postelio peut créer pour vous.</p>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Gabarit Avant/Après 01 -->
                <div class="space-y-4 group">
                    <div class="aspect-square bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 overflow-hidden flex flex-col relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2">
                        <!-- Photos de chantier -->
                        <div class="flex-1 flex">
                            <div class="w-1/2 relative overflow-hidden border-r-2 border-white">
                                <img src="https://images.unsplash.com/photo-1541604193435-225878999ba0?q=80&w=600&h=600&auto=format&fit=crop" alt="Avant" class="w-full h-full object-cover grayscale-[20%] group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/20 to-transparent h-12"></div>
                            </div>
                            <div class="w-1/2 relative overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1621905252507-b354bcadcabc?q=80&w=600&h=600&auto=format&fit=crop" alt="Après" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/20 to-transparent h-12"></div>
                            </div>
                        </div>
                        <!-- Bandeau Marque -->
                        <div class="h-16 bg-[#F59E0B] flex items-center justify-between px-8 shrink-0">
                            <span class="text-white font-black uppercase tracking-widest text-lg font-montserrat">Avant</span>
                            <span class="text-white font-black uppercase tracking-widest text-lg font-montserrat">Après</span>
                        </div>
                        <!-- Overlay au survol -->
                        <div class="absolute inset-0 bg-midnight/0 group-hover:bg-midnight/10 transition-colors pointer-events-none"></div>
                    </div>
                    <div>
                        <h4 class="font-bold text-midnight text-lg">Avant / Après 01</h4>
                        <p class="text-gray-500 text-sm">Parfait pour valoriser vos rénovations et finitions.</p>
                    </div>
                </div>

                <!-- Gabarit Avis Client 01 -->
                <div class="space-y-4 group">
                    <div class="aspect-square bg-[#F59E0B] rounded-2xl shadow-lg flex flex-col items-center justify-center p-12 text-center relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2 overflow-hidden">
                        <!-- Étoiles -->
                        <div class="flex space-x-2 mb-8 animate-fade-in">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="w-8 h-8 text-yellow-400 fill-current drop-shadow-md" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <!-- Texte Avis -->
                        <p class="text-white font-black uppercase tracking-tight text-xl sm:text-2xl leading-tight font-montserrat drop-shadow-sm">
                            "Travail impeccable ! L'équipe a été ponctuelle, propre et le résultat dépasse nos attentes. Je recommande à 100%."
                        </p>
                        <!-- Éléments décoratifs -->
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-black/10 rounded-full blur-2xl"></div>
                    </div>
                    <div>
                        <h4 class="font-bold text-midnight text-lg">Avis Client 01</h4>
                        <p class="text-gray-500 text-sm">Transformez vos témoignages en preuves sociales fortes.</p>
                    </div>
                </div>

                <!-- Gabarit FAQ/Conseil (Bonus pour le look) -->
                <div class="space-y-4 group">
                    <div class="aspect-square bg-midnight rounded-2xl shadow-lg border-t-8 border-[#F59E0B] flex flex-col p-10 relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2 overflow-hidden">
                        <div class="flex-1 flex flex-col justify-center">
                            <span class="inline-block w-12 h-1 bg-[#F59E0B] mb-6"></span>
                            <h3 class="text-[#F59E0B] font-black uppercase tracking-widest text-sm mb-4">FAQ Artisan</h3>
                            <p class="text-white font-black uppercase tracking-tight text-xl leading-snug font-montserrat">
                                Pourquoi choisir une isolation par l'extérieur ?
                            </p>
                        </div>
                        <div class="pt-6 border-t border-white/10">
                            <p class="text-gray-400 text-sm italic font-montserrat">Votre expertise mise en avant.</p>
                        </div>
                        <!-- Décoration -->
                        <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-white/5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-midnight text-lg">FAQ Artisan 01</h4>
                        <p class="text-gray-500 text-sm">Éduquez vos clients et affirmez votre professionnalisme.</p>
                    </div>
                </div>

                <!-- Gabarit Lifestyle (Nouveau) -->
                <div class="space-y-4 group">
                    <div class="aspect-square bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 overflow-hidden flex flex-col relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?q=80&w=800&h=800&auto=format&fit=crop" alt="Lifestyle" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div>
                        <h4 class="font-bold text-midnight text-lg">Lifestyle 01</h4>
                        <p class="text-gray-500 text-sm">Une immersion brute dans votre métier, sans artifices.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@800;900&display=swap');
        .font-montserrat {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
    @endpush
</x-app-layout>
