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
        'starter' => [
            'name' => 'Starter',
            'stripe_price_id' => env('STRIPE_STARTER_PRICE_ID', ''),
            'credits' => 30,
            'price' => 9.99,
            'features' => [
                '30 posts par mois',
                '1 réseau social',
                'Templates de base',
                'Support par email',
            ],
        ],
        'pro' => [
            'name' => 'Pro',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID', ''),
            'credits' => 100,
            'price' => 24.99,
            'features' => [
                '100 posts par mois',
                'Instagram + Facebook',
                'Tous les templates',
                'Templates personnalisés',
                'Support prioritaire',
            ],
        ],
        'business' => [
            'name' => 'Business',
            'stripe_price_id' => env('STRIPE_BUSINESS_PRICE_ID', ''),
            'credits' => 300,
            'price' => 49.99,
            'features' => [
                '300 posts par mois',
                'Instagram + Facebook',
                'Tous les templates',
                'Templates personnalisés',
                'Calendrier éditorial',
                'Support dédié',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Free Tier
    |--------------------------------------------------------------------------
    */

    'free_credits' => 3,

];
