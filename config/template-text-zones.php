<?php

/**
 * Template Text Zone Configuration
 *
 * Defines the exact text constraints for each template's visual text zone.
 * These constraints are used by:
 *   - ContentRefinerService: to give the AI precise character limits
 *   - TextFitterService: to enforce limits post-generation (hard truncation)
 *   - Blade templates: for consistent font sizing (referenced, not directly consumed)
 *
 * Dimensions are based on a 360x360px viewport (3x scale → 1080x1080 final).
 *
 * Keys:
 *   maxChars     — absolute maximum characters before text gets clipped
 *   idealChars   — target range [min, max] for the AI prompt
 *   lineClamp    — max visible lines (informational, enforced in CSS)
 */

return [

    // ─── Single templates with image text ────────────────────────────────

    'review' => [
        'image_text' => [
            'maxChars'   => 180,
            'idealChars' => [100, 150],
            'lineClamp'  => 6,
        ],
    ],

    'faq' => [
        'image_text' => [
            'maxChars'   => 100,
            'idealChars' => [40, 75],
            'lineClamp'  => 4,
        ],
    ],

    'quality_focus' => [
        'image_text' => [
            'maxChars'   => 60,
            'idealChars' => [30, 50],
            'lineClamp'  => 2,
        ],
    ],

    'conseil_saisonnier' => [
        'image_text' => [
            'maxChars'   => 120,
            'idealChars' => [50, 85],
            'lineClamp'  => 4,
        ],
    ],

    'erreur_a_eviter' => [
        'image_text' => [
            'maxChars'   => 120,
            'idealChars' => [60, 100],
            'lineClamp'  => 4,
        ],
    ],

    // ─── Single templates with short overlay text ────────────────────────

    'lifestyle' => [
        'image_text' => [
            'maxChars'   => 50,
            'idealChars' => [20, 40],
            'lineClamp'  => 2,
        ],
    ],

    'project' => [
        'image_text' => [
            'maxChars'   => 50,
            'idealChars' => [20, 40],
            'lineClamp'  => 1,
        ],
    ],

    // ─── Carousel templates ──────────────────────────────────────────────

    'chantier_detail' => [
        'title' => [
            'maxChars'   => 60,
            'idealChars' => [30, 50],
            'lineClamp'  => 2,
        ],
        'slide' => [
            'maxChars'   => 60,
            'idealChars' => [30, 50],
            'lineClamp'  => 2,
        ],
    ],

    'step_by_step' => [
        'title' => [
            'maxChars'   => 70,
            'idealChars' => [30, 50],
            'lineClamp'  => 3,
        ],
        'slide' => [
            'maxChars'   => 65,
            'idealChars' => [30, 55],
            'lineClamp'  => 3,
        ],
    ],

    'guide_pedagogique' => [
        'title' => [
            'maxChars'   => 80,
            'idealChars' => [40, 70],
            'lineClamp'  => 4,
        ],
        'slide' => [
            'maxChars'   => 100,
            'idealChars' => [50, 90],
            'lineClamp'  => 4,
        ],
    ],

    // ─── Custom templates (by layout_type) ───────────────────────────────

    'custom' => [
        'standard' => [
            'maxChars'   => 200,
            'idealChars' => [80, 160],
            'lineClamp'  => 8,
        ],
        'centered' => [
            'maxChars'   => 140,
            'idealChars' => [60, 120],
            'lineClamp'  => 5,
        ],
        'quote' => [
            'maxChars'   => 150,
            'idealChars' => [60, 120],
            'lineClamp'  => 5,
        ],
        'bold_title' => [
            'maxChars'   => 160,
            'idealChars' => [60, 130],
            'lineClamp'  => 5,
        ],
        'split' => [
            'maxChars'   => 120,
            'idealChars' => [50, 100],
            'lineClamp'  => 4,
        ],
        'minimal' => [
            'maxChars'   => 160,
            'idealChars' => [60, 130],
            'lineClamp'  => 5,
        ],
        'dynamic' => [
            'maxChars'   => 180,
            'idealChars' => [60, 150],
            'lineClamp'  => 6,
        ],
    ],

    // ─── Caption constraints (Instagram) ─────────────────────────────────

    'caption' => [
        'standard'  => ['idealChars' => [200, 400], 'maxChars' => 500],
        'carousel'  => ['idealChars' => [250, 450], 'maxChars' => 600],
        'simplified'=> ['idealChars' => [60, 130],  'maxChars' => 150],
    ],

];
