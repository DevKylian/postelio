<?php

/**
 * Template Specifications — Central source of truth
 *
 * Defines EXACTLY what each template expects from OpenAI:
 *   - type: 'single' or 'carousel'
 *   - description: what this template is about (for UI guidance)
 *   - vocal_hint: what the artisan should say in their vocal memo
 *   - fields: ordered list of JSON fields OpenAI must return
 *     - name: JSON key name
 *     - label: human-readable label (for prompts)
 *     - purpose: detailed description for the AI prompt
 *     - idealChars: [min, max] target character range
 *     - maxChars: absolute hard limit (text gets truncated beyond this)
 *     - lineClamp: max visible lines in the visual template
 *     - count: number of items (1 for single text, 'dynamic' for variable, or int for fixed)
 *     - countSource: for dynamic counts — what determines the number ('photos', 'targetSlides')
 *     - isArray: true if field is an array of texts (slides)
 *   - caption: caption field specification
 *   - context_fields: which template_options fields are relevant (for AI context)
 *   - ai_role: specific AI persona instructions
 *   - ai_content_instructions: specific content generation instructions
 */

return [

    // ═══════════════════════════════════════════════════════════════════════
    // SINGLE TEMPLATES (one image with text overlay)
    // ═══════════════════════════════════════════════════════════════════════

    'review' => [
        'type' => 'single',
        'description' => 'Avis client transformé en citation premium',
        'vocal_hint' => 'Racontez le retour de votre client : son prénom, ce que vous avez fait pour lui, et ce qu\'il a dit de votre travail.',
        'context_fields' => ['clientName', 'serviceType'],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans la valorisation des avis clients.',
        'ai_content_instructions' => 'Transforme le témoignage client en contenu Instagram premium. Préfère utiliser les mots EXACTS du client si un témoignage est présent dans le vocal.',
        'fields' => [
            [
                'name' => 'image_text',
                'label' => 'Citation client',
                'purpose' => 'Citation percutante tirée de l\'avis client, utilisant les mots exacts du témoignage si disponibles. Phrase complète, sans hashtag, sans emoji.',
                'idealChars' => [100, 150],
                'maxChars' => 180,
                'lineClamp' => 6,
                'count' => 1,
                'isArray' => false,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram complète avec storytelling autour de l\'avis client.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    'faq' => [
        'type' => 'carousel',
        'description' => 'Répondez aux questions fréquentes de vos clients',
        'vocal_hint' => 'Posez la question que vos clients vous posent souvent, puis donnez-y votre réponse d\'expert. Vous pouvez donner plusieurs questions/réponses pour un carrousel plus riche.',
        'context_fields' => ['faqCategory'],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans la création de FAQ engageantes.',
        'ai_content_instructions' => 'Depuis le vocal, extrais la ou les questions/réponses. Le 1er slide est toujours la question principale. Les slides suivants sont les réponses. Si le vocal contient plusieurs Q/R, crée plusieurs paires.',
        'fields' => [
            [
                'name' => 'title',
                'label' => 'Question FAQ',
                'purpose' => 'La question principale, formulée de manière engageante avec un point d\'interrogation. C\'est le titre de la slide de couverture.',
                'idealChars' => [40, 75],
                'maxChars' => 100,
                'lineClamp' => 4,
                'count' => 1,
                'isArray' => false,
            ],
            [
                'name' => 'slides',
                'label' => 'Réponses FAQ',
                'purpose' => 'Chaque slide contient une réponse ou un élément de réponse, en phrase affirmative/explicative claire et concise.',
                'idealChars' => [40, 75],
                'maxChars' => 100,
                'lineClamp' => 4,
                'count' => 'dynamic',
                'countSource' => 'content',
                'countMin' => 1,
                'countMax' => 4,
                'countDefault' => 1,
                'isArray' => true,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram qui résume la FAQ et invite à interagir.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    'quality_focus' => [
        'type' => 'single',
        'description' => 'Mettez en lumière vos matériaux ou équipements',
        'vocal_hint' => 'Décrivez le matériau ou l\'équipement que vous souhaitez mettre en avant : son nom, pourquoi vous l\'utilisez, ses avantages.',
        'context_fields' => [],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans la mise en valeur des matériaux et équipements.',
        'ai_content_instructions' => 'Mets en valeur le matériau/équipement mentionné. Le texte image doit être un NOM ou une courte description, jamais une phrase avec verbe. Majuscules recommandées.',
        'fields' => [
            [
                'name' => 'image_text',
                'label' => 'Nom du matériau/équipement',
                'purpose' => 'Nom ou courte description du matériau/équipement mis en valeur. Expression complète, majuscules recommandées. Sans hashtag.',
                'idealChars' => [30, 50],
                'maxChars' => 60,
                'lineClamp' => 2,
                'count' => 1,
                'isArray' => false,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram qui développe pourquoi ce matériau/équipement garantit qualité et durabilité.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    'conseil_saisonnier' => [
        'type' => 'single',
        'description' => 'Un tip utile et saisonnier pour anticiper les besoins de vos clients',
        'vocal_hint' => 'Donnez un conseil pratique lié à la saison : ce que vos clients devraient faire, anticiper ou vérifier en ce moment.',
        'context_fields' => ['season'],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans les conseils saisonniers.',
        'ai_content_instructions' => 'Génère un conseil saisonnier actionnable. Le texte image doit commencer par un verbe d\'action (Vérifiez, Anticipez, Préparez...).',
        'fields' => [
            [
                'name' => 'image_text',
                'label' => 'Conseil saisonnier',
                'purpose' => 'Le conseil saisonnier actionnable, commençant par un verbe (Vérifiez, Anticipez, Préparez...). Phrase complète. Sans hashtag.',
                'idealChars' => [50, 85],
                'maxChars' => 120,
                'lineClamp' => 4,
                'count' => 1,
                'isArray' => false,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram avec contexte saisonnier développé.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    'erreur_a_eviter' => [
        'type' => 'single',
        'description' => 'Une erreur fréquente de vos clients — format alerte très engageant',
        'vocal_hint' => 'Décrivez une erreur courante que font vos clients : laquelle, pourquoi c\'est un problème, et comment l\'éviter.',
        'context_fields' => [],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans le contenu éducatif engageant.',
        'ai_content_instructions' => 'Formule l\'erreur en alerte courte et directe. Le texte image doit être percutant, format alerte.',
        'fields' => [
            [
                'name' => 'image_text',
                'label' => 'Erreur à éviter',
                'purpose' => 'L\'erreur formulée en alerte courte et directe (ex: "Ne pas attendre que la fuite empire."). Phrase complète. Sans hashtag.',
                'idealChars' => [60, 100],
                'maxChars' => 120,
                'lineClamp' => 4,
                'count' => 1,
                'isArray' => false,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram qui décrit l\'erreur en détail et explique comment l\'éviter.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // SINGLE TEMPLATES (with short overlay text)
    // ═══════════════════════════════════════════════════════════════════════

    'lifestyle' => [
        'type' => 'single',
        'description' => 'Partagez l\'ambiance et le quotidien de votre métier',
        'vocal_hint' => 'Décrivez brièvement la scène ou le projet que montre votre photo : le lieu, l\'ambiance, ce que vous avez réalisé.',
        'context_fields' => [],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans le contenu lifestyle.',
        'ai_content_instructions' => 'Le texte image est une ÉTIQUETTE courte qui nomme le sujet, comme un titre d\'album photo. Méthode : identifie le SUJET en 1-2 mots, puis ajoute UN qualificatif. Exemples : "Cuisine sur mesure", "Douche italienne", "Terrasse bois". INTERDIT : phrases avec verbe, articles introductifs ("Création de...", "Une cuisine...").',
        'fields' => [
            [
                'name' => 'image_text',
                'label' => 'Étiquette du projet',
                'purpose' => 'Étiquette courte nommant le sujet du projet (1-3 mots max). PAS de phrase, PAS de verbe, PAS d\'article introductif. Ex: "Cuisine sur mesure", "Parquet chêne massif".',
                'idealChars' => [10, 25],
                'maxChars' => 30,
                'lineClamp' => 1,
                'count' => 1,
                'isArray' => false,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram complète avec storytelling lifestyle.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    'project' => [
        'type' => 'carousel',
        'description' => 'Transformation complète en 3 slides (Couverture, Avant, Après)',
        'vocal_hint' => 'Décrivez le projet de transformation : quel type de travaux, le contexte, et la différence entre l\'avant et l\'après.',
        'context_fields' => ['workCategory', 'jobDuration'],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans les posts avant/après.',
        'ai_content_instructions' => 'Le titre de couverture doit nommer le projet de manière accrocheuse (ex: "Rénovation Cuisine"). Les slides 2 et 3 sont fixées sur "Avant" et "Après" (le contenu texte des slides n\'est pas affiché mais la structure JSON doit contenir 2 entrées).',
        'fields' => [
            [
                'name' => 'title',
                'label' => 'Titre de couverture',
                'purpose' => 'Titre accrocheur nommant le projet de transformation. Court et impactant.',
                'idealChars' => [20, 40],
                'maxChars' => 50,
                'lineClamp' => 1,
                'count' => 1,
                'isArray' => false,
            ],
            [
                'name' => 'slides',
                'label' => 'Textes Avant/Après',
                'purpose' => 'Exactement 2 textes courts (non affichés mais requis pour la structure JSON). Le premier pour la photo "Avant", le second pour la photo "Après".',
                'idealChars' => [10, 30],
                'maxChars' => 50,
                'lineClamp' => 1,
                'count' => 2,
                'isArray' => true,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram qui raconte la transformation avec storytelling.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // CAROUSEL TEMPLATES (multi-slides)
    // ═══════════════════════════════════════════════════════════════════════

    'chantier_detail' => [
        'type' => 'carousel',
        'description' => 'Un chantier sous tous ses angles, slide par slide',
        'vocal_hint' => 'Décrivez votre chantier en détail : le contexte, les étapes importantes, les particularités techniques, le résultat.',
        'context_fields' => [],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans la présentation de chantiers.',
        'ai_content_instructions' => 'Crée un carrousel qui détaille le chantier. Le 1er slide est la couverture (titre accrocheur). Les slides suivants décrivent les détails correspondant aux photos.',
        'fields' => [
            [
                'name' => 'title',
                'label' => 'Titre du chantier',
                'purpose' => 'Titre accrocheur pour la slide de couverture. Phrase complète et impactante.',
                'idealChars' => [30, 50],
                'maxChars' => 60,
                'lineClamp' => 2,
                'count' => 1,
                'isArray' => false,
            ],
            [
                'name' => 'slides',
                'label' => 'Descriptions des photos',
                'purpose' => 'Chaque texte décrit un aspect du chantier correspondant à une photo. Court et descriptif, phrase complète.',
                'idealChars' => [30, 50],
                'maxChars' => 60,
                'lineClamp' => 2,
                'count' => 'dynamic',
                'countSource' => 'photos',
                'countMin' => 2,
                'countMax' => 5,
                'isArray' => true,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram qui résume le chantier et invite à découvrir les détails.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    'step_by_step' => [
        'type' => 'carousel',
        'description' => 'Votre process de travail, étape par étape',
        'vocal_hint' => 'Décrivez votre processus de travail étape par étape : chaque phase, chaque action importante dans l\'ordre.',
        'context_fields' => [],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans la création de tutoriels visuels.',
        'ai_content_instructions' => 'Crée un carrousel étape par étape. Le 1er slide est le titre du processus. Les slides suivants sont les étapes numérotées (sans le préfixe "Étape X :" car il est ajouté automatiquement). Phrases complètes.',
        'fields' => [
            [
                'name' => 'title',
                'label' => 'Titre du processus',
                'purpose' => 'Titre accrocheur décrivant le processus présenté. Phrase complète.',
                'idealChars' => [30, 50],
                'maxChars' => 70,
                'lineClamp' => 3,
                'count' => 1,
                'isArray' => false,
            ],
            [
                'name' => 'slides',
                'label' => 'Étapes du processus',
                'purpose' => 'Chaque texte décrit une étape du processus. Phrase complète, sans préfixe "Étape X :" (ajouté automatiquement). Court et clair.',
                'idealChars' => [30, 55],
                'maxChars' => 65,
                'lineClamp' => 3,
                'count' => 'dynamic',
                'countSource' => 'photos',
                'countMin' => 2,
                'countMax' => 5,
                'isArray' => true,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram qui présente le processus et valorise le savoir-faire.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

    'guide_pedagogique' => [
        'type' => 'carousel',
        'description' => 'Carrousel éducatif — positionnez-vous expert de votre domaine',
        'vocal_hint' => 'Partagez votre expertise : donnez des conseils, des astuces ou des informations utiles que vos clients devraient connaître.',
        'context_fields' => ['targetSlides'],
        'ai_role' => 'Tu es un expert en marketing pour artisans, spécialisé dans le contenu éducatif et pédagogique.',
        'ai_content_instructions' => 'Crée un carrousel éducatif. Le 1er slide est une question/accroche forte. Les slides suivants développent les conseils. Le dernier slide est un appel à l\'action.',
        'fields' => [
            [
                'name' => 'title',
                'label' => 'Accroche/Question',
                'purpose' => 'Question ou accroche forte pour la slide de couverture. Doit donner envie de swiper. Phrase complète.',
                'idealChars' => [40, 70],
                'maxChars' => 80,
                'lineClamp' => 4,
                'count' => 1,
                'isArray' => false,
            ],
            [
                'name' => 'slides',
                'label' => 'Conseils/Contenus éducatifs',
                'purpose' => 'Chaque slide développe un conseil ou une information. Phrase complète et autonome. Le dernier est un appel à l\'action.',
                'idealChars' => [50, 90],
                'maxChars' => 100,
                'lineClamp' => 4,
                'count' => 'dynamic',
                'countSource' => 'targetSlides',
                'countMin' => 3,
                'countMax' => 8,
                'countDefault' => 5,
                'isArray' => true,
            ],
        ],
        'caption' => [
            'purpose' => 'Légende Instagram qui résume le guide et invite à sauvegarder/partager.',
            'idealChars' => [400, 800],
            'maxChars' => 1200,
        ],
    ],

];
