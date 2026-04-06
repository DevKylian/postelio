<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = [
        'role_key',
        'monthly_quota',
        'post_variants_count',
        'can_generate_posts',
        'can_connect_social',
        'has_premium_templates',
        'can_view_calendar',
        'can_view_template_previews',
        'can_view_statistics',
        'can_customize_template',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_quota'       => 'integer',
            'post_variants_count' => 'integer',
            'can_generate_posts'  => 'boolean',
            'can_connect_social'  => 'boolean',
            'has_premium_templates'       => 'boolean',
            'can_view_calendar'           => 'boolean',
            'can_view_template_previews'  => 'boolean',
            'can_view_statistics'         => 'boolean',
            'can_customize_template'      => 'boolean',
        ];
    }

    /**
     * Helper to get permissions for a given role key.
     */
    public static function getForRole(string $roleKey): self
    {
        return self::firstOrCreate(
            ['role_key' => $roleKey],
            [
                'monthly_quota'       => 2,
                'post_variants_count' => 1,
                'can_generate_posts'  => true,
                'can_connect_social'  => false,
                'has_premium_templates'      => false,
                'can_view_calendar'          => false,
                'can_view_template_previews' => false,
                'can_view_statistics'        => false,
                'can_customize_template'     => true,
            ]
        );
    }
}
