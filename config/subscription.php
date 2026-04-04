<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Define your Stripe subscription plans here. Each plan maps to a Stripe
    | Price ID and defines the credits allocated per month.
    |
    */

    'plans' => [
        'free' => [
            'name' => 'Starter',
            'stripe_price_id' => null,
            'credits' => 2,
            'max_regenerations' => 1,
            'price' => 0,
            'yearly_price' => 0,
            'features' => [
                'Jusqu\'à 2 posts par mois',
                '1 régénération par post',
                'Modèles de posts standards',
                'Historique des posts sur 7 jours',
                'Support par email',
            ],
            'disabled_features' => [
                'Publication automatique sur vos réseaux',
                'Planification et programmation des posts',
                'Analytics de vos réseaux sociaux',
                'Accès aux modèles premium',
                'Support prioritaire dédié',
            ],
        ],
        'basic' => [
            'name' => 'Basic',
            'stripe_price_id' => env('STRIPE_BASIC_PRICE_ID', ''),
            'yearly_stripe_price_id' => env('STRIPE_BASIC_YEARLY_PRICE_ID', ''),
            'credits' => 15,
            'max_regenerations' => 2,
            'price' => 19,
            'yearly_price' => 180,
            'features' => [
                'Jusqu\'à 15 posts par mois',
                '2 régénérations par post',
                'Modèles de posts standards',
                'Historique des posts sur 30 jours',
                'Support prioritaire dédié',
            ],
            'disabled_features' => [
                'Publication automatique sur vos réseaux',
                'Planification et programmation des posts',
                'Analytics de vos réseaux sociaux',
                'Accès aux modèles premium',
            ],
        ],
        'pro' => [
            'name' => 'Professionnel',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID', ''),
            'yearly_stripe_price_id' => env('STRIPE_PRO_YEARLY_PRICE_ID', ''),
            'credits' => 30,
            'max_regenerations' => 3,
            'price' => 29,
            'yearly_price' => 280,
            'features' => [
                'Jusqu\'à 30 posts par mois',
                '3 régénérations par post',
                'Publication automatique sur vos réseaux',
                'Planification et programmation des posts',
                'Analytics de vos réseaux sociaux',
                'Accès aux modèles premium',
                'Historique des posts illimité',
                'Support prioritaire dédié',
            ],
            'disabled_features' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Free Tier
    |--------------------------------------------------------------------------
    */

    'free_credits' => 2,

];
