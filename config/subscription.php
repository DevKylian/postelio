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
                'Templates de base',
                'Support par email',
            ],
        ],
        'monthly' => [
            'name' => 'Mensuel',
            'stripe_price_id' => env('STRIPE_MONTHLY_PRICE_ID', ''),
            'credits' => 100,
            'price' => 29.90,
            'old_price' => 49.90,
            'features' => [
                '100 posts par mois',
                'Publication Instagram & Facebook',
                'Tous les templates',
                'Support prioritaire',
            ],
        ],
        'annual' => [
            'name' => 'Annuel',
            'stripe_price_id' => env('STRIPE_ANNUAL_PRICE_ID', ''),
            'credits' => 100,
            'price' => 290.00,
            'old_price' => 358.80,
            'features' => [
                '100 posts par mois',
                'Publication Instagram & Facebook',
                'Tous les templates',
                'Support dédié',
                '2 mois offerts inclus',
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
