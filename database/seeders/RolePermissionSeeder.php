<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'admin' => [
                'monthly_quota' => 999999,
                'post_variants_count' => 999,
                'can_generate_posts' => true,
                'can_connect_social' => true,
                'has_premium_templates' => true,
                'can_view_calendar' => true,
                'can_view_template_previews' => true,
                'can_view_statistics' => true,
            ],
            'pro' => [
                'monthly_quota' => 30,
                'post_variants_count' => 3,
                'can_generate_posts' => true,
                'can_connect_social' => true,
                'has_premium_templates' => true,
                'can_view_calendar' => true,
                'can_view_template_previews' => true,
                'can_view_statistics' => true,
            ],
            'basic' => [
                'monthly_quota' => 15,
                'post_variants_count' => 2,
                'can_generate_posts' => true,
                'can_connect_social' => false,
                'has_premium_templates' => false,
                'can_view_calendar' => true,
                'can_view_template_previews' => true,
                'can_view_statistics' => false,
            ],
            'tester' => [
                'monthly_quota' => 10,
                'post_variants_count' => 3,
                'can_generate_posts' => true,
                'can_connect_social' => true,
                'has_premium_templates' => true,
                'can_view_calendar' => true,
                'can_view_template_previews' => true,
                'can_view_statistics' => true,
            ],
            'free' => [
                'monthly_quota' => 2,
                'post_variants_count' => 1,
                'can_generate_posts' => true,
                'can_connect_social' => false,
                'has_premium_templates' => false,
                'can_view_calendar' => true,
                'can_view_template_previews' => true,
                'can_view_statistics' => false,
            ],
        ];

        foreach ($permissions as $role => $data) {
            RolePermission::updateOrCreate(['role_key' => $role], $data);
        }
    }
}
