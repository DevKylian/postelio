<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HelpCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nettoyage des anciennes données pour éviter les doublons
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('faq_questions')->truncate();
        DB::table('faq_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'name' => 'Mon Compte & Facturation',
                'description' => 'Gérez votre abonnement, vos factures et vos informations personnelles.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>',
                'questions' => [
                    [
                        'title' => 'Comment modifier mon abonnement ?',
                        'slug' => 'comment-modifier-mon-abonnement',
                        'summary' => 'Étapes pour passer au plan supérieur ou ajuster votre offre actuelle.',
                        'content' => '
Pour modifier votre abonnement, rendez-vous dans la section **Abonnement** accessible depuis le menu de navigation à gauche de votre écran.

1. Cliquez sur le bouton **"Gérer mon abonnement"**.
2. Vous serez redirigé vers le portail de facturation sécurisé de **Stripe** (notre partenaire de paiement certifié).
3. Depuis cette interface, vous pourrez mettre à jour votre mode de paiement, changer de formule ou suspendre votre abonnement en toute autonomie.

Tout changement prend effet immédiatement ou à la fin de votre période en cours selon l\'action choisie.
                        ',
                    ],
                    [
                        'title' => 'Où trouver mes factures ?',
                        'slug' => 'comment-recuperer-mes-factures',
                        'summary' => 'Téléchargez l\'historique de vos paiements en quelques clics.',
                        'content' => '
Vos factures sont centralisées sur votre espace de gestion sécurisé.

- Rendez-vous dans l\'onglet **Abonnement** du menu principal (à gauche).
- Cliquez sur **"Gérer mon abonnement"**.
- Une fois sur le portail **Stripe** (notre partenaire de paiement), descendez jusqu\'à la section **"Historique de facturation"**.
- Vous pourrez y visionner et télécharger chaque facture au format PDF.
                        ',
                    ],
                ]
            ],
            [
                'name' => 'Création & Contenu',
                'description' => 'Apprenez à générer des posts percutants et à gérer vos crédits de création.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                'questions' => [
                    [
                        'title' => 'Comment fonctionne le quota de générations ?',
                        'slug' => 'comment-fonctionne-systeme-credits',
                        'summary' => 'Comprendre comment est décompté votre quota mensuel.',
                        'content' => '
Chaque nouvelle création de contenu (incluant la rédaction du texte et la génération du visuel) effectuée via la page **Générer un post** est décomptée de votre **quota mensuel**.

- Votre quota restant est visible en permanence en bas du **menu de navigation à gauche**.
- Votre quota est automatiquement rechargé chaque mois à la date de votre inscription.
- Les corrections manuelles sur vos posts déjà générés ne sont pas décomptées de votre quota.
                        ',
                    ],
                    [
                        'title' => 'Le rendu ne me satisfait pas, comment l\'améliorer ?',
                        'slug' => 'ia-qualite-attendue',
                        'summary' => 'Conseils pour obtenir des résultats parfaitement adaptés à votre style.',
                        'content' => '
Postelio utilise des algorithmes avancés pour créer vos contenus. Pour obtenir un résultat optimal dès le premier essai :

- **Soyez précis** : Dans la description du post, donnez des détails concrets sur le chantier ou le service que vous souhaitez mettre en avant.
- **Paramètres de marque** : Vérifiez que vos informations sont complètes dans **Paramètres > Informations de l\'entreprise** et **Paramètres > Identité visuelle**. Notre technologie s\'appuie sur ces données pour personnaliser chaque mot.
- **Le bon ton** : Précisez votre préférence (Professionnel, Amical, etc.) lors de la génération.
- **Régénérez** : Si une variante ne vous convient pas, le bouton **"Régénérer"** vous permet de proposer une nouvelle approche instantanément.
                        ',
                    ],
                ]
            ],
            [
                'name' => 'Réseaux Sociaux',
                'description' => 'Connectez vos comptes pour automatiser vos publications sur Instagram et Facebook.',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>',
                'questions' => [
                    [
                        'title' => 'Comment lier mes comptes Instagram et Facebook ?',
                        'slug' => 'comment-connecter-reseaux-sociaux',
                        'summary' => 'Étape par étape pour activer la publication automatique (Offre Premium).',
                        'content' => '
Pour diffuser vos créations directement, vous devez autoriser Postelio à communiquer avec vos pages professionnelles :

1. Cliquez sur **Paramètres** dans le menu de gauche.
2. Allez dans l\'onglet **Réseaux sociaux**.
3. Choisissez **"Connecter Facebook / Instagram"**.
4. Suivez les étapes de validation sécurisées de Meta.

**Important** : Assurez-vous d\'utiliser un **compte Instagram Professionnel** lié à une page Facebook dont vous êtes l\'administrateur.
                        ',
                    ],
                ]
            ],
        ];

        foreach ($categories as $i => $catData) {
            $catId = DB::table('faq_categories')->insertGetId([
                'name' => $catData['name'],
                'description' => $catData['description'],
                'icon' => $catData['icon'],
                'sort_order' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($catData['questions'] as $j => $qData) {
                DB::table('faq_questions')->insert([
                    'faq_category_id' => $catId,
                    'title' => $qData['title'],
                    'slug' => $qData['slug'],
                    'summary' => $qData['summary'],
                    'content' => Str::markdown($qData['content']),
                    'sort_order' => $j,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
