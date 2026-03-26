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
            'credits' => 3,
            'price' => 0,
            'yearly_price' => 0,
            'features' => [
                'Jusqu\'à 3 posts par mois',
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
        'pro' => [
            'name' => 'Basic',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID', ''),
            'yearly_stripe_price_id' => env('STRIPE_PRO_YEARLY_PRICE_ID', ''),
            'credits' => 30,
            'price' => 19,
            'yearly_price' => 180,
            'features' => [
                'Jusqu\'à 30 posts par mois',
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
        'premium' => [
            'name' => 'Professionnel',
            'stripe_price_id' => env('STRIPE_PREMIUM_PRICE_ID', ''),
            'yearly_stripe_price_id' => env('STRIPE_PREMIUM_YEARLY_PRICE_ID', ''),
            'credits' => 100,
            'price' => 29,
            'yearly_price' => 280,
            'features' => [
                'Jusqu\'à 100 posts par mois',
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

    'free_credits' => 3,

];
