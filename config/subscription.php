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
            'name' => 'Gratuit',
            'stripe_price_id' => null,
            'credits' => 3,
            'price' => 0,
            'features' => [
                '3 posts par mois',
                'Modèles de posts standards',
                'Support email',
                'Conservation des posts 1 semaine',
            ],
        ],
        'pro' => [
            'name' => 'Pro',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID', ''),
            'credits' => 30,
            'price' => 14.90,
            'features' => [
                '30 posts par mois',
                'Modèles de posts standards',
                'Support prioritaire',
                'Conservation des posts 1 mois',
            ],
        ],
        'premium' => [
            'name' => 'Premium',
            'stripe_price_id' => env('STRIPE_PREMIUM_PRICE_ID', ''),
            'credits' => 100,
            'price' => 29.90,
            'features' => [
                '100 posts par mois',
                'Analytics des comptes réseaux sociaux',
                'Publication automatique',
                'Schedule des posts',
                'Modèles de posts premiums',
                'Support prioritaire',
                'Conservation des posts sans expiration',
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
