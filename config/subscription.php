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
            'post_variants_count' => 1,
            'price' => 0,
            'yearly_price' => 0,
            'coming_soon' => false,
            'features' => [
                'Création' => [
                    ['name' => 'Jusqu\'à <b>2 publications</b> par mois', 'enabled' => true],
                    ['name' => '<b>1 proposition de texte</b> au choix par publication', 'enabled' => true],
                    ['name' => 'Génération de publications <b>images</b>', 'enabled' => true],
                    ['name' => 'Génération de publications <b>vidéos</b>', 'enabled' => false],
                    ['name' => 'Gabarits de posts <b>standards</b>', 'enabled' => true],
                    ['name' => 'Accès aux gabarits <b>premium</b>', 'enabled' => false],
                ],
                'Diffusion' => [
                    ['name' => 'Publication <b>directe</b> sur vos réseaux sociaux', 'enabled' => false],
                    ['name' => '<b>Programmation à l\'avance</b> pour la semaine', 'enabled' => false],
                ],
                'Suivi & Support' => [
                    ['name' => '<b>Statistiques détaillées</b> de vos réseaux sociaux', 'enabled' => false],
                    ['name' => 'Historique des publications sur <b>7 jours</b>', 'enabled' => true],
                    ['name' => 'Support par <b>email</b>', 'enabled' => true],
                    ['name' => 'Assistance <b>prioritaire</b>', 'enabled' => false],
                ],
            ],
            'disabled_features' => [],
        ],
        'basic' => [
            'name' => 'Basic',
            'stripe_price_id' => env('STRIPE_BASIC_PRICE_ID', ''),
            'yearly_stripe_price_id' => env('STRIPE_BASIC_YEARLY_PRICE_ID', ''),
            'credits' => 15,
            'post_variants_count' => 2,
            'price' => 29,
            'yearly_price' => 276,
            'coming_soon' => false,
            'features' => [
                'Création' => [
                    ['name' => 'Jusqu\'à <b>15 publications</b> par mois', 'enabled' => true],
                    ['name' => '<b>2 propositions de texte</b> au choix par publication', 'enabled' => true],
                    ['name' => 'Génération de publications <b>images</b>', 'enabled' => true],
                    ['name' => 'Génération de publications <b>vidéos</b>', 'enabled' => false],
                    ['name' => 'Accès aux gabarits <b>premium</b>', 'enabled' => true],
                ],
                'Diffusion' => [
                    ['name' => 'Publication <b>directe</b> sur vos réseaux sociaux', 'enabled' => true],
                    ['name' => '<b>Programmation à l\'avance</b> pour la semaine', 'enabled' => true],
                ],
                'Suivi & Support' => [
                    ['name' => '<b>Statistiques détaillées</b> de vos réseaux sociaux', 'enabled' => true],
                    ['name' => 'Historique des publications <b>illimité</b>', 'enabled' => true],
                    ['name' => 'Assistance <b>prioritaire</b>', 'enabled' => true],
                ],
            ],
            'disabled_features' => [],
        ],
        'pro' => [
            'name' => 'Professionnel',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID', ''),
            'yearly_stripe_price_id' => env('STRIPE_PRO_YEARLY_PRICE_ID', ''),
            'credits' => 30,
            'post_variants_count' => 3,
            'price' => 49,
            'yearly_price' => 468,
            'coming_soon' => true,
            'features' => [
                'Création' => [
                    ['name' => 'Jusqu\'à <b>30 publications</b> par mois', 'enabled' => true],
                    ['name' => '<b>3 propositions de texte</b> au choix par publication', 'enabled' => true],
                    ['name' => 'Génération de publications <b>images</b>', 'enabled' => true],
                    ['name' => 'Génération de publications <b>vidéos</b> <span class="text-copper font-bold">(bientôt)</span>', 'enabled' => true],
                    ['name' => 'Accès aux gabarits <b>premium</b>', 'enabled' => true],
                ],
                'Diffusion' => [
                    ['name' => 'Publication <b>directe</b> sur vos réseaux sociaux', 'enabled' => true],
                    ['name' => '<b>Programmation à l\'avance</b> pour la semaine', 'enabled' => true],
                ],
                'Suivi & Support' => [
                    ['name' => '<b>Statistiques détaillées</b> de vos réseaux sociaux', 'enabled' => true],
                    ['name' => 'Historique des publications <b>illimité</b>', 'enabled' => true],
                    ['name' => 'Assistance <b>prioritaire</b>', 'enabled' => true],
                ],
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
