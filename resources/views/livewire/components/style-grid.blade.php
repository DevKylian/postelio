<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\NativeTemplateSetting;

state([
    'activeTab' => 'standard',
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'companyCity' => '',
    'brandTextColor' => '#FFFFFF',
    'hasLogo' => false,
    'logoUrl' => null,
    'accentColor' => '#0F172A',
    'accentTextColor' => '#FFFFFF',
]);

mount(function ($activeTab = 'standard', $brandColor = '#F59E0B', $companyLabel = 'Votre Société', $companyCity = '', $brandTextColor = '#FFFFFF', $logoUrl = null, $accentColor = '#0F172A', $accentTextColor = '#FFFFFF') {
    $this->activeTab = $activeTab;
    $this->brandColor = $brandColor;
    $this->companyLabel = $companyLabel;
    $this->companyCity = $companyCity;
    $this->brandTextColor = $brandTextColor;
    $this->logoUrl = $logoUrl;
    $this->accentColor = $accentColor;
    $this->accentTextColor = $accentTextColor;
});

$nativeSettings = computed(fn() => NativeTemplateSetting::all()->pluck('is_active', 'slug'));
?>

<div class="animate-fade-in">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="font-bold text-midnight text-base">Gabarits inclus</h2>
        <div class="flex-1 h-px bg-gray-100"></div>
        <span class="text-xs text-gray-400 font-medium">
            {{ collect(['project', 'review', 'faq', 'lifestyle', 'quality_focus', 'chantier_detail', 'step_by_step', 'conseil_saisonnier', 'erreur_a_eviter', 'guide_pedagogique'])->filter(fn($s) => $this->nativeSettings[$s] ?? true)->count() }} styles
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @if($activeTab === 'standard')
            @if($this->nativeSettings['project'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.project 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        :accentColor="$accentColor"
                        :accentTextColor="$accentTextColor"
                        imageBeforeUrl="{{ asset('images/templates/avant_postelio.png') }}"
                        imageAfterUrl="{{ asset('images/templates/apres_postelio.png') }}"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Avant / Après</h4><p class="text-gray-500 text-sm">Parfait pour valoriser vos rénovations et finitions.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['review'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$logoUrl" :preview="true" :brand-text-color="$brandTextColor" :accentColor="$accentColor" :accentTextColor="$accentTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Avis Client</h4><p class="text-gray-500 text-sm">Transformez vos témoignages en preuves sociales fortes.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['faq'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$logoUrl" :preview="true" :brand-text-color="$brandTextColor" :accentColor="$accentColor" :accentTextColor="$accentTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">FAQ Artisan</h4><p class="text-gray-500 text-sm">Éduquez vos clients et affirmez votre professionnalisme.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['lifestyle'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.lifestyle 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :logoUrl="$logoUrl" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        :accentColor="$accentColor"
                        :accentTextColor="$accentTextColor"
                        imageUrl="{{ asset('images/templates/lifestyle_postelio.png') }}"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Lifestyle</h4><p class="text-gray-500 text-sm">Une immersion brute dans votre métier, sans artifices.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['quality_focus'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$logoUrl" :preview="true" :brand-text-color="$brandTextColor" :accentColor="$accentColor" :accentTextColor="$accentTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Focus Qualité</h4><p class="text-gray-500 text-sm">Valorisez la qualité de vos matériaux et équipements.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['chantier_detail'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.chantier-detail 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :logoUrl="$logoUrl" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        :accentColor="$accentColor"
                        :accentTextColor="$accentTextColor"
                        :slides="[
                            asset('images/templates/chantier_one_postelio.png'),
                            asset('images/templates/chantier_two_postelio.png'),
                            asset('images/templates/chantier_three_postelio.png')
                        ]"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Chantier en Détail</h4><p class="text-gray-500 text-sm">Une immersion complète avec plusieurs photos d'un même projet.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['step_by_step'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" :accentColor="$accentColor" :accentTextColor="$accentTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Step-by-Step</h4><p class="text-gray-500 text-sm">Expliquez votre expertise étape par étape.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['conseil_saisonnier'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$logoUrl" :preview="true" :brand-text-color="$brandTextColor" :accentColor="$accentColor" :accentTextColor="$accentTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Conseil Saisonnier</h4><p class="text-gray-500 text-sm">Anticipez les besoins de vos clients selon la saison.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['erreur_a_eviter'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$logoUrl" :preview="true" :brand-text-color="$brandTextColor" :accentColor="$accentColor" :accentTextColor="$accentTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Erreur à Éviter</h4><p class="text-gray-500 text-sm">Un format alerte très engageant pour éduquer vos clients.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['guide_pedagogique'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" :accentColor="$accentColor" :accentTextColor="$accentTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Guide Pédagogique</h4><p class="text-gray-500 text-sm">Apportez de la valeur et positionnez-vous en expert conseil.</p></div>
                </div>
            @endif
        @endif

        @if($activeTab === 'glass')
            @if($this->nativeSettings['project'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.project 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        imageBeforeUrl="{{ asset('images/templates/avant_postelio.png') }}"
                        imageAfterUrl="{{ asset('images/templates/apres_postelio.png') }}"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Avant / Après</h4><p class="text-gray-500 text-sm italic">Un style futuriste et épuré.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['review'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Avis Client</h4><p class="text-gray-500 text-sm italic">Mettez en valeur vos retours clients.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['faq'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Glass FAQ</h4><p class="text-gray-500 text-sm italic">Répondez aux questions avec un design premium.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['lifestyle'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.lifestyle :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :imageUrl="asset('images/templates/lifestyle_postelio.png')" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Lifestyle</h4><p class="text-gray-500 text-sm italic">Une immersion brute et moderne.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['quality_focus'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Focus Qualité</h4><p class="text-gray-500 text-sm italic">Valorisez vos matériaux.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['chantier_detail'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.chantier-detail 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        :slides="[
                            asset('images/templates/chantier_one_postelio.png'),
                            asset('images/templates/chantier_two_postelio.png'),
                            asset('images/templates/chantier_three_postelio.png')
                        ]"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Chantier Détail</h4><p class="text-gray-500 text-sm italic">Explorez vos projets.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['step_by_step'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Step-by-Step</h4><p class="text-gray-500 text-sm italic">Partagez votre expertise.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['conseil_saisonnier'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Conseil Saisonnier</h4><p class="text-gray-500 text-sm italic">Le bon conseil au bon moment.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['erreur_a_eviter'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Erreur à Éviter</h4><p class="text-gray-500 text-sm italic">Éduquez avec un design premium.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['guide_pedagogique'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.glass.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Glass Guide Pédagogique</h4><p class="text-gray-500 text-sm italic">Devenez la référence.</p></div>
                </div>
            @endif
        @endif

        @if($activeTab === 'blueprint')
            @if($this->nativeSettings['project'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.project 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        imageBeforeUrl="{{ asset('images/templates/avant_postelio.png') }}"
                        imageAfterUrl="{{ asset('images/templates/apres_postelio.png') }}"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Avant / Après</h4><p class="text-gray-500 text-sm italic">Le chantier vu comme un projet d'architecte.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['review'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Avis</h4><p class="text-gray-500 text-sm italic">La validation technique de vos clients.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['faq'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint FAQ</h4><p class="text-gray-500 text-sm italic">Réponses claires et documentées.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['lifestyle'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.lifestyle :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :imageUrl="asset('images/templates/lifestyle_postelio.png')" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Lifestyle</h4><p class="text-gray-500 text-sm italic">L'immersion brute.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['quality_focus'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Focus Qualité</h4><p class="text-gray-500 text-sm italic">L'analyse détaillée.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['chantier_detail'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.chantier-detail 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        :slides="[
                            asset('images/templates/chantier_one_postelio.png'),
                            asset('images/templates/chantier_two_postelio.png'),
                            asset('images/templates/chantier_three_postelio.png')
                        ]"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Zoom Chantier</h4><p class="text-gray-500 text-sm italic">Dossier technique.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['step_by_step'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Étapes</h4><p class="text-gray-500 text-sm italic">Méthodologie précise.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['conseil_saisonnier'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Saison</h4><p class="text-gray-500 text-sm italic">Conseil expert archivé.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['erreur_a_eviter'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Alerte</h4><p class="text-gray-500 text-sm italic">Éduquez avec rigueur.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['guide_pedagogique'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.blueprint.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Blueprint Guide</h4><p class="text-gray-500 text-sm italic">Base de connaissance.</p></div>
                </div>
            @endif
        @endif

        @if($activeTab === 'kraft')
            @if($this->nativeSettings['project'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.project 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        imageBeforeUrl="{{ asset('images/templates/avant_postelio.png') }}"
                        imageAfterUrl="{{ asset('images/templates/apres_postelio.png') }}"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Avant / Après</h4><p class="text-gray-500 text-sm italic">Authenticité du chantier.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['review'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Avis Client</h4><p class="text-gray-500 text-sm italic">Validation brute.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['faq'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft FAQ</h4><p class="text-gray-500 text-sm italic">Réponses franches.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['lifestyle'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.lifestyle :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :imageUrl="asset('images/templates/lifestyle_postelio.png')" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Lifestyle</h4><p class="text-gray-500 text-sm italic">Métier au naturel.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['quality_focus'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Focus Qualité</h4><p class="text-gray-500 text-sm italic">Précision technique.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['chantier_detail'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.chantier-detail 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        :slides="[
                            asset('images/templates/chantier_one_postelio.png'),
                            asset('images/templates/chantier_two_postelio.png'),
                            asset('images/templates/chantier_three_postelio.png')
                        ]"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Zoom Chantier</h4><p class="text-gray-500 text-sm italic">Rapport de chantier.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['step_by_step'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Étapes</h4><p class="text-gray-500 text-sm italic">Méthodologie industrielle.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['conseil_saisonnier'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Saison</h4><p class="text-gray-500 text-sm italic">Mémo technique.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['erreur_a_eviter'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Alerte</h4><p class="text-gray-500 text-sm italic">Design impactant.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['guide_pedagogique'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.kraft.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Kraft Guide</h4><p class="text-gray-500 text-sm italic">Guide pratique.</p></div>
                </div>
            @endif
        @endif

        @if($activeTab === 'beton')
            @if($this->nativeSettings['project'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.project 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        imageBeforeUrl="{{ asset('images/templates/avant_postelio.png') }}"
                        imageAfterUrl="{{ asset('images/templates/apres_postelio.png') }}"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Avant / Après</h4><p class="text-gray-500 text-sm italic">Modernité et solidité.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['review'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Avis Client</h4><p class="text-gray-500 text-sm italic">Ancré dans le réel.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['faq'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Béton FAQ</h4><p class="text-gray-500 text-sm italic">Précis et professionnel.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['lifestyle'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.lifestyle 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        imageUrl="{{ asset('images/templates/lifestyle_postelio.png') }}"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Lifestyle</h4><p class="text-gray-500 text-sm italic">Immersion moderne.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['quality_focus'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Focus Qualité</h4><p class="text-gray-500 text-sm italic">Le détail qui compte.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['chantier_detail'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.chantier-detail 
                        :brandColor="$brandColor" 
                        :companyLabel="$companyLabel" 
                        :city="$companyCity" 
                        :preview="true" 
                        :brand-text-color="$brandTextColor" 
                        :slides="[
                            asset('images/templates/chantier_one_postelio.png'),
                            asset('images/templates/chantier_two_postelio.png'),
                            asset('images/templates/chantier_three_postelio.png')
                        ]"
                    />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Zoom Chantier</h4><p class="text-gray-500 text-sm italic">Vue multi-angle.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['step_by_step'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Étapes Pro</h4><p class="text-gray-500 text-sm italic">Méthodologie claire.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['conseil_saisonnier'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Dossier</h4><p class="text-gray-500 text-sm italic">Mémo technique.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['erreur_a_eviter'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Alerte Erreur</h4><p class="text-gray-500 text-sm italic">Prévention expert.</p></div>
                </div>
            @endif
            @if($this->nativeSettings['guide_pedagogique'] ?? true)
                <div class="space-y-4 group">
                    <x-post-templates.beton.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" :brand-text-color="$brandTextColor" />
                    <div><h4 class="font-bold text-midnight text-lg">Béton Guide Pratique</h4><p class="text-gray-500 text-sm italic">Accompagnement client.</p></div>
                </div>
            @endif
        @endif
    </div>
</div>
