<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Help Center / FAQ Categories
    |--------------------------------------------------------------------------
    |
    | Define your help center categories and their respective questions here.
    | The slug must be unique across all questions.
    |
    */

    'categories' => [
        [
            'name' => 'Mon Compte & Facturation',
            'description' => 'Tout savoir sur la gestion de votre abonnement et de vos informations personnelles.',
            'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>',
            'questions' => [
                [
                    'title' => 'Comment modifier mon abonnement ?',
                    'slug' => 'comment-modifier-mon-abonnement',
                    'summary' => 'Étapes pour passer à un plan supérieur ou annuler votre abonnement actuel.',
                    'content' => '
Pour modifier votre abonnement, rendez-vous dans la section **Abonnement** via le menu de navigation à gauche de votre tableau de bord.

1. Cliquez sur le bouton "Modifier mon plan" ou "Gérer mon abonnement".
2. Vous serez redirigé vers le portail de facturation sécurisé (Stripe).
3. Depuis ce portail, vous pourrez mettre à jour votre carte bancaire, choisir une formule différente ou mettre fin à votre abonnement à tout moment.

Si vous avez des crédits restants, ils seront conservés jusqu\'à la fin de votre cycle de facturation actuel.
                    ',
                ],
                [
                    'title' => 'Comment récupérer mes factures ?',
                    'slug' => 'comment-recuperer-mes-factures',
                    'summary' => 'Où trouver et télécharger l\'historique de vos paiements.',
                    'content' => '
Vos factures sont accessibles depuis votre portail de facturation.

- Allez dans la section **Abonnement** du menu.
- Cliquez sur "Gérer mon abonnement".
- Dans l\'interface Stripe, scrollez vers le bas jusqu\'à la section "Historique des factures". 
- Vous y trouverez des liens de téléchargement au format PDF pour chaque mois de facturation.
                    ',
                ],
            ],
        ],
        [
            'name' => 'Génération avec l\'IA',
            'description' => 'Questions fréquentes concernant la création de posts sociaux et les crédits.',
            'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
            'questions' => [
                [
                    'title' => 'Comment fonctionne le système de crédits ?',
                    'slug' => 'comment-fonctionne-systeme-credits',
                    'summary' => 'Comprendre comment sont décomptés vos crédits lors d\'une génération.',
                    'content' => '
Chaque fois que vous utilisez l\'IA pour générer un contenu (texte et visuel) depuis la page **Générer un post**, **1 crédit** est déduit de votre quota mensuel.

- Vos crédits restants sont affichés en bas à gauche de votre menu.
- Le quota est **réinitialisé tous les mois** à la date anniversaire de votre inscription (ou de votre facturation).
- Les textes sont générés en combinant vos préférences, tandis que l\'image ou le visuel est composé automatiquement via vos gabarits.
                    ',
                ],
                [
                    'title' => 'L\'IA ne produit pas la qualité attendue, que faire ?',
                    'slug' => 'ia-qualite-attendue',
                    'summary' => 'Astuces pour améliorer les résultats obtenus lors de la création de contenus.',
                    'content' => '
Postelio s\'appuie sur des modèles d\'Intelligence Artificielle de pointe. Cependant, le meilleur moyen d\'avoir de bons résultats est de structurer vos consignes ("prompts").

- Soyez très précis dans la description du post que vous souhaitez.
- Précisez un ton : **Décontracté, professionnel, tutoiement, vouvoiement**.
- Complétez bien vos informations dans les **Paramètres > Informations de l\'entreprise** afin que l\'IA s\'en inspire (services proposés, ton par défaut, mots-clés...).
- N\'hésitez pas relancer la génération avec le bouton "Régénérer" si la variante proposée ne vous satisfait pas totalement.
                    ',
                ],
            ],
        ],
        [
            'name' => 'Réseaux Sociaux & Publication',
            'description' => 'Lier vos comptes Facebook et Instagram pour automatiser la publication.',
            'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>',
            'questions' => [
                [
                    'title' => 'Comment connecter mes réseaux sociaux ?',
                    'slug' => 'comment-connecter-reseaux-sociaux',
                    'summary' => 'Instructions pour lier vos comptes Facebook et Instagram (Premium).',
                    'content' => '
Si vous bénéficiez d\'un compte Premium ou d\'un abonnement valide, vous pouvez publier directement depuis l\'interface Postelio.

1. Rendez-vous dans le menu **Paramètres**.
2. Allez dans l\'onglet **Comptes Sociaux**.
3. Cliquez sur **Connecter avec Facebook / Instagram**.
4. Suivez la procédure Meta, en autorisant Postelio à accéder aux pages que vous administrez.

> Veuillez noter que vous devez avoir un **compte professionnel Instagram** relié à une page Facebook pour que cela fonctionne.
                    ',
                ],
            ],
        ],
    ],
];
