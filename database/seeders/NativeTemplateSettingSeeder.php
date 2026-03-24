<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NativeTemplateSetting;

class NativeTemplateSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function up(): void
    {
        $slugs = [
            'review',
            'project',
            'faq',
            'lifestyle',
            'quality_focus',
            'chantier_detail',
            'step_by_step',
            'conseil_saisonnier',
            'erreur_a_eviter',
            'guide_pedagogique',
        ];

        foreach ($slugs as $slug) {
            NativeTemplateSetting::updateOrCreate(
                ['slug' => $slug],
                ['is_active' => true]
            );
        }
    }

    /**
     * Standard seeder method
     */
    public function run(): void
    {
        $this->up();
    }
}
