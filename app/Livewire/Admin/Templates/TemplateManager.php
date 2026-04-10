<?php

namespace App\Livewire\Admin\Templates;

use App\Models\CustomTemplate;
use App\Models\NativeTemplateSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TemplateManager extends Component
{
    public bool $visualStylesEnabled = true;

    public function mount()
    {
        $this->visualStylesEnabled = (bool) \App\Models\Setting::get('enable_visual_styles', true);
    }

    public function toggleVisualStyles()
    {
        $this->visualStylesEnabled = !$this->visualStylesEnabled;
        \App\Models\Setting::set('enable_visual_styles', $this->visualStylesEnabled);
        $this->dispatch('notify', 'La visibilité des styles alternatifs a été mise à jour.');
    }

    public function toggleNative($slug)
    {
        $setting = NativeTemplateSetting::where('slug', $slug)->first();
        if ($setting) {
            $setting->update(['is_active' => !$setting->is_active]);
        } else {
            NativeTemplateSetting::create([
                'slug' => $slug,
                'is_active' => false,
            ]);
        }
        $this->dispatch('notify', 'Le statut du gabarit natif a été mis à jour.');
    }

    public function toggleCustom($id)
    {
        $template = CustomTemplate::find($id);
        if ($template) {
            $template->update(['is_active' => !$template->is_active]);
        }
        $this->dispatch('notify', 'Le statut du gabarit personnalisé a été mis à jour.');
    }

    public function render()
    {
        $nativeTemplates = [
            ['slug' => 'review', 'name' => 'Avis Client', 'description' => 'Transformez vos témoignages en preuves sociales fortes.'],
            ['slug' => 'project', 'name' => 'Avant / Après', 'description' => 'Parfait pour valoriser vos rénovations et finitions.'],
            ['slug' => 'faq', 'name' => 'FAQ Artisan', 'description' => 'Éduquez vos clients et affirmez votre professionnalisme.'],
            ['slug' => 'lifestyle', 'name' => 'Lifestyle', 'description' => 'Une immersion brute dans votre métier, sans artifices.'],
            ['slug' => 'quality_focus', 'name' => 'Focus Qualité', 'description' => 'Valorisez la qualité de vos matériaux et équipements.'],
            ['slug' => 'chantier_detail', 'name' => 'Chantier en Détail', 'description' => 'Une immersion complète avec plusieurs photos d\'un même projet.'],
            ['slug' => 'step_by_step', 'name' => 'Step-by-Step', 'description' => 'Expliquez votre expertise étape par étape.'],
            ['slug' => 'conseil_saisonnier', 'name' => 'Conseil Saisonnier', 'description' => 'Anticipez les besoins de vos clients selon la saison.'],
            ['slug' => 'erreur_a_eviter', 'name' => 'Erreur à Éviter', 'description' => 'Un format alerte très engageant pour éduquer vos clients.'],
            ['slug' => 'guide_pedagogique', 'name' => 'Guide Pédagogique', 'description' => 'Apportez de la valeur et positionnez-vous en expert conseil.'],
        ];

        // Enrich native list with DB status
        $nativeSettings = NativeTemplateSetting::all()->pluck('is_active', 'slug');
        foreach ($nativeTemplates as &$nt) {
            $nt['is_active'] = $nativeSettings[$nt['slug']] ?? true;
        }

        $customTemplates = CustomTemplate::with('user')->orderByDesc('created_at')->get();

        return view('livewire.admin.templates.template-manager', compact('nativeTemplates', 'customTemplates'));
    }
}
