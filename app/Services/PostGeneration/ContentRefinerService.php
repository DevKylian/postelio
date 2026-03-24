<?php

namespace App\Services\PostGeneration;

use OpenAI\Laravel\Facades\OpenAI;

class ContentRefinerService
{
    // Templates that display dynamic text on the generated image
    const TEMPLATES_WITH_IMAGE_TEXT = ['review', 'faq', 'quality_focus', 'conseil_saisonnier', 'erreur_a_eviter'];

    /**
     * Refine raw transcription into structured post content.
     * Always returns JSON: { caption, image_text? } for single templates,
     * or { caption, slides } for carousel templates.
     */
    public function refine(string $transcript, string $template, int $photoCount = 0, array $options = []): string
    {
        $isCarousel = in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique']);

        // ── Règle fondamentale anti-invention — s'applique à TOUS les templates ──
        $noHallucinateGlobal = "RÈGLE FONDAMENTALE — Anti-invention (priorité absolue) :\n"
            . "Utilise UNIQUEMENT les informations explicitement présentes dans la transcription fournie.\n"
            . "N'invente jamais de noms, prénoms, chiffres, dates, réalisations ou détails absents du vocal.\n"
            . "Si le mémo est court ou vague, génère un post court et sobre qui reste dans ce cadre — "
            . "ne complète JAMAIS avec du contenu inventé pour le rendre plus complet.\n"
            . "Adapte la longueur et la richesse de la légende à la richesse du mémo vocal : mémo court = légende courte.\n\n";

        $noMarkdownPrefix = "RÈGLE DE FORMAT STRICTE :\n"
            . "- Jamais de Markdown : aucun **, aucun *, aucun _, aucun #. Instagram n'en supporte pas.\n"
            . "- Structure la légende avec de vrais sauts de ligne entre chaque idée/paragraphe (retour à la ligne simple entre deux phrases liées, ligne vide entre deux blocs distincts).\n"
            . "- Exemple de structure attendue : accroche sur une ligne, ligne vide, corps espacé point par point, ligne vide, call-to-action, ligne vide, hashtags.\n\n";

        $noTruncationRule = "RÈGLE DE RÉDACTION STRICTE SUR LES TEXTES COURTS :\n"
            . "- Tu ne dois JAMAIS couper une phrase ou une idée en plein milieu pour respecter une limite de caractères.\n"
            . "- Chaque texte généré (titre de slide, texte sur image) DOIT composer une idée ou une phrase parfaitement lisible et complète.\n"
            . "- Les limites de caractères (ex: ~50 car.) sont indicatives. Si besoin, dépasse-les très légèrement pour terminer le mot ou la phrase intelligemment.\n\n";

        $prompt = $noHallucinateGlobal . $noMarkdownPrefix . $noTruncationRule . $this->getPromptForTemplate($template, $photoCount, $options);
        
        // Ensure the word "json" is present for the API requirement
        if (!str_contains(strtolower($prompt), 'json')) {
            $prompt .= "\n\nRéponds UNIQUEMENT sous forme de JSON valide.";
        }

        // ── Simplification à la demande ──────────────────────────────────────────
        if (!empty($options['simplified'])) {
            $prompt .= "\n\nSIMPLIFICATION DEMANDÉE : Génère une légende TRÈS COURTE (80-130 caractères maximum). "
                . "Va à l'essentiel : une phrase principale + 2-3 hashtags. Pas de storytelling. Pas de liste.";
        }

        $maxTokens = $isCarousel ? 1200 : 600;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => "Voici le contenu à transformer : " . $transcript . "\n\nRAPPEL : n'utilise aucun formatage Markdown (**gras**, *italique*, _souligné_, # titres). Texte brut uniquement."],
            ],
            'max_tokens' => $maxTokens,
            'temperature' => 0.4,
            'response_format' => ['type' => 'json_object'],
        ]);

        $raw = trim($response->choices[0]->message->content);

        // Detect refusals embedded in the JSON or in plain text
        $this->assertNotRefusal($raw);

        return $raw;
    }

    /**
     * Throw if the AI returned a refusal instead of content.
     */
    private function assertNotRefusal(string $raw): void
    {
        // Check plain-text refusals (JSON format broken)
        $plainPatterns = [
            '/^je ne peux pas/i',
            '/^je ne suis pas en mesure/i',
            "/^il m['']est impossible/i",
            "/^il ne m['']est pas possible/i",
            '/^désolé[,\s]/i',
            '/^je suis désolé/i',
            '/^malheureusement[,\s]/i',
        ];
        foreach ($plainPatterns as $p) {
            if (preg_match($p, trim($raw))) {
                throw new \RuntimeException(
                    'L\'IA n\'a pas pu générer le contenu : les informations fournies semblent incohérentes avec le mémo vocal. '
                    . 'Vérifiez que le champ "Service réalisé" ou "Type de travaux" correspond bien à ce que vous décrivez dans l\'audio, ou laissez ces champs vides.'
                    );
            }
        }

        // Check refusal embedded inside the JSON
        $data = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $textToCheck = ($data['caption'] ?? '') . ' ' . ($data['image_text'] ?? '');
            foreach ($plainPatterns as $p) {
                if (preg_match($p, trim($textToCheck))) {
                    throw new \RuntimeException(
                        'L\'IA n\'a pas pu générer le contenu. Vérifiez la cohérence des champs de personnalisation avec votre mémo vocal, ou laissez-les vides.'
                        );
                }
            }
        }
    }

    /**
     * Read the text zone config for a given template and field.
     * Returns ['idealChars' => [min, max], 'maxChars' => int] or null.
     */
    private function textZone(string $template, string $field, array $options = []): ?array
    {
        $zones = config('template-text-zones');

        if ($template === 'custom') {
            $layout = $options['custom_layout_type'] ?? 'standard';
            return $zones['custom'][$layout] ?? $zones['custom']['standard'];
        }

        return $zones[$template][$field] ?? null;
    }

    /**
     * Build a strict character constraint string for the AI prompt.
     * Example: "STRICTEMENT entre 40 et 75 caractères (maximum absolu : 100)"
     */
    private function charConstraint(string $template, string $field, array $options = []): string
    {
        $zone = $this->textZone($template, $field, $options);
        if (!$zone) {
            return '~80 caractères';
        }
        [$min, $max] = $zone['idealChars'];
        $hard = $zone['maxChars'];
        return "STRICTEMENT entre {$min} et {$max} caractères (maximum absolu : {$hard} car. — au-delà le texte sera coupé)";
    }

    private function getPromptForTemplate(string $template, int $photoCount = 0, array $options = []): string
    {
        $tone = $options['tone'] ?? 'professionnel';
        $includeHashtags = $options['includeHashtags'] ?? true;
        $customNote = $options['customNote'] ?? null;

        $toneInstruction = $this->toneInstruction($tone);
        $hashtagInstruction = $includeHashtags
            ? "Ajoute 3 à 5 hashtags pertinents à la fin de la légende."
            : "N'ajoute aucun hashtag.";

        $noRefuseRule = "RÈGLE ABSOLUE : Tu dois TOUJOURS générer le contenu demandé. Si certains champs de contexte semblent ne pas correspondre au mémo vocal, ignore-les et travaille avec le transcript audio uniquement. Ne refuse jamais.";

        $extraNote = $customNote ? " Note de l'artisan : {$customNote}." : '';

        // ── Strict text zone rule (applied globally) ─────────────────────────
        $textZoneRule = "RÈGLE ZONE DE TEXTE (priorité haute) :\n"
            . "Le texte affiché sur l'image (image_text, title, slides) sera rendu dans une zone visuelle fixe.\n"
            . "Si le texte est trop long, il sera TRONQUÉ automatiquement et le résultat sera visuellement cassé.\n"
            . "Tu DOIS donc respecter les limites de caractères indiquées pour chaque champ.\n"
            . "Préfère un texte court et percutant à un texte long qui déborde.\n"
            . "Compte les caractères avant de répondre.\n\n";

        // ── Caption zone config ──────────────────────────────────────────────
        $captionZones = config('template-text-zones.caption');
        $isCarousel = in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique']);
        $captionConfig = $isCarousel ? $captionZones['carousel'] : $captionZones['standard'];
        $captionRange = "{$captionConfig['idealChars'][0]}-{$captionConfig['idealChars'][1]} caractères";

        // ── JSON schema descriptions ──────────────────────────────────────────

        // For templates that show text on the image
        $jsonSingleWithImageText = fn(string $captionDesc, string $imageTextDesc) =>
        $textZoneRule
        . "Tu dois répondre UNIQUEMENT par un objet JSON valide avec exactement cette structure :\n"
        . "{\n"
        . "  \"caption\": \"{$captionDesc}\",\n"
        . "  \"image_text\": \"{$imageTextDesc}\"\n"
        . "}\n"
        . "IMPORTANT : image_text ne doit JAMAIS contenir de hashtags, d'emojis ni de ponctuation lourde.\n"
        . "IMPORTANT : Respecte EXACTEMENT les limites de caractères pour image_text. Compte avant de répondre.";

        // For carousels — uses text zone config for title & slides
        $titleConstraint = $this->charConstraint($template, 'title', $options);
        $slideConstraint = $this->charConstraint($template, 'slide', $options);
        $jsonCarousel = $textZoneRule
        . "Tu dois répondre UNIQUEMENT par un objet JSON valide avec cette structure :\n"
        . "{\n"
        . "  \"caption\": \"La légende Instagram complète ({$captionRange}, {$hashtagInstruction})\",\n"
        . "  \"title\": \"Un titre très court et accrocheur pour la couverture ({$titleConstraint}, phrase complète)\",\n"
        . "  \"slides\": [\"Texte court pour slide 1\", \"Texte court pour slide 2\", ...]\n"
        . "}\n"
        . "IMPORTANT : Les textes dans 'slides' et 'title' ne doivent JAMAIS contenir de hashtags, de numéros d'étape (car ils sont ajoutés automatiquement) ni d'emojis.\n"
        . "IMPORTANT : Chaque texte de slide doit faire {$slideConstraint}. Compte les caractères.";

        // ── Template-specific image_text constraints ──────────────────────────
        $reviewImgConstraint = $this->charConstraint('review', 'image_text');
        $faqImgConstraint = $this->charConstraint('faq', 'image_text');
        $qfImgConstraint = $this->charConstraint('quality_focus', 'image_text');
        $csImgConstraint = $this->charConstraint('conseil_saisonnier', 'image_text');
        $eaeImgConstraint = $this->charConstraint('erreur_a_eviter', 'image_text');
        $lifeImgConstraint = $this->charConstraint('lifestyle', 'image_text');
        $projImgConstraint = $this->charConstraint('project', 'image_text');

        return match ($template) {

                // ── Single templates WITH image text ─────────────────────────────

                'review' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}"
                . ($options['clientName'] ? " Prénom du client : {$options['clientName']}." : "")
                . ($options['serviceType'] ? " Contexte du service (indicatif) : {$options['serviceType']}." : "")
                . "{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram complète avec storytelling ({$captionRange}). {$hashtagInstruction}",
                "Citation percutante tirée de l'avis client. Préfère utiliser les mots EXACTS si un témoignage est présent dans le vocal. {$reviewImgConstraint}, phrase complète. Sans hashtag."
            ),

                'faq' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}"
                . ($options['faqCategory'] ? " Catégorie FAQ : {$options['faqCategory']}." : "")
                . "{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram avec la réponse développée à la question ({$captionRange}). {$hashtagInstruction}",
                "La question seule, courte, percutante, avec point d'interrogation. {$faqImgConstraint}, phrase complète. Sans hashtag."
            ),

                'quality_focus' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram qui développe pourquoi ce matériau/équipement garantit qualité et durabilité ({$captionRange}). {$hashtagInstruction}",
                "Nom ou courte description du matériau/équipement mis en valeur. {$qfImgConstraint}, expression complète. Majuscules recommandées. Sans hashtag."
            ),

                'conseil_saisonnier' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}"
                . ($options['season'] ? " Saison ciblée : {$options['season']}." : "")
                . "{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram avec contexte saisonnier développé ({$captionRange}). {$hashtagInstruction}",
                "Le conseil saisonnier actionnable, commençant par un verbe (Vérifiez, Anticipez, Préparez...). {$csImgConstraint}, phrase complète. Sans hashtag."
            ),

                'erreur_a_eviter' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram qui décrit l'erreur et explique comment l'éviter ({$captionRange}). {$hashtagInstruction}",
                "L'erreur formulée en alerte courte et directe (ex: Ne pas attendre que la fuite empire.). {$eaeImgConstraint}, phrase complète. Sans hashtag."
            ),

                // ── Single templates with short generic image text ─────────────────

                'lifestyle' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . $jsonSingleWithImageText(
                    "Légende Instagram complète ({$captionRange}, storytelling). {$hashtagInstruction}",
                    "Titre concis et percutant ({$lifeImgConstraint}, phrase complète). Sans hashtag."
                ),

                'project' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}"
                . ($options['workCategory'] ? " Type de travaux : {$options['workCategory']}." : "")
                . ($options['jobDuration'] ? " Durée : {$options['jobDuration']}." : "")
                . "{$extraNote} "
                . $jsonSingleWithImageText(
                    "Légende Instagram complète ({$captionRange}, storytelling). {$hashtagInstruction}",
                    "Titre descriptif du projet ({$projImgConstraint}, phrase complète). Sans hashtag."
                ),

                // ── Carousels ─────────────────────────────────────────────────────

                'chantier_detail' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . "À partir de la description vocale d'un chantier, crée un post carrousel. Nous avons {$photoCount} photos. "
                . "{$jsonCarousel} Le premier slide est la couverture (titre accrocheur, {$titleConstraint}, complet). Les slides suivants décrivent les détails correspondant aux photos ({$slideConstraint} chacun, complets).",

                'step_by_step' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . "À partir du vocal décrivant un processus, crée un carrousel étape par étape. Nous avons {$photoCount} photos. "
                . "{$jsonCarousel} Le premier slide est le titre du process ({$titleConstraint}, complet). Les slides suivants sont les étapes numérotées ({$slideConstraint} chacun, phrases complètes, sans le préfixe 'Étape X :').",

                'guide_pedagogique' => (function () use ($noRefuseRule, $toneInstruction, $extraNote, $jsonCarousel, $options, $titleConstraint, $slideConstraint) {
                $targetSlides = isset($options['targetSlides']) ? (int)$options['targetSlides'] : 5;
                $slidesRange = max(3, $targetSlides - 1) . ' à ' . min(8, $targetSlides + 1);
                return "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                    . "À partir du vocal, crée un carrousel éducatif. "
                    . "{$jsonCarousel} Le premier slide est une question/accroche forte ({$titleConstraint}, complète). "
                    . "Les slides suivants ({$slidesRange} au total) développent les conseils ({$slideConstraint} chacun, complets). "
                    . "Le dernier slide est un appel à l'action ({$slideConstraint}, complet).";
            })(),

                'custom' => (function () use ($options, $noRefuseRule, $toneInstruction, $hashtagInstruction, $extraNote, $includeHashtags, $textZoneRule, $captionRange) {
                $customPrompt = $options['custom_template_prompt'] ?? null;
                $layout = $options['custom_layout_type'] ?? 'standard';
                $zone = config("template-text-zones.custom.{$layout}") ?? config('template-text-zones.custom.standard');
                [$min, $max] = $zone['idealChars'];
                $hard = $zone['maxChars'];
                $imgConstraint = "STRICTEMENT entre {$min} et {$max} caractères (maximum absolu : {$hard} car.)";

                if ($customPrompt) {
                    return $textZoneRule
                        . "{$customPrompt}"
                        . "\n\nCONTRAINTE TEXTE IMAGE : Le champ image_text doit faire {$imgConstraint}. Au-delà, le texte sera tronqué."
                        . "\n\n{$toneInstruction}"
                        . "\n" . ($includeHashtags ? "Ajoute 3 à 5 hashtags pertinents à la fin de la légende." : "N'ajoute aucun hashtag.")
                        . ($extraNote ? "\n{$extraNote}" : "");
                }
                return "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                    . $textZoneRule
                    . "Tu dois répondre UNIQUEMENT par un objet JSON valide : {\"caption\": \"Légende Instagram ({$captionRange}). {$hashtagInstruction}\", \"image_text\": \"Titre accrocheur et concis ({$imgConstraint}, phrase complète). Sans hashtag.\"}";
            })(),

                default => (function () use ($noRefuseRule, $hashtagInstruction, $textZoneRule) {
                return "{$noRefuseRule} {$textZoneRule}Tu dois répondre UNIQUEMENT par un objet JSON valide : {\"caption\": \"Légende Instagram courte (150 caractères max). {$hashtagInstruction}\", \"image_text\": \"Titre concis (maximum 80 caractères, complet)\"}";
            })(),
            };
    }

    public function extractFields(string $transcript): array
    {
        $prompt = "Tu es un assistant expert en extraction de données pour artisans.
À partir du transcript d'un mémo vocal, extrais les informations suivantes sous forme de JSON uniquement.
Si une information n'est pas présente, mets null.

Champs à extraire :
- clientName: Prénom du client uniquement (ex: 'Marie')
- serviceType: Nom du service réalisé (ex: 'pose de carrelage', 'peinture salon')
- workCategory: Une des valeurs suivantes : ['Peinture', 'Carrelage', 'Maçonnerie', 'Plomberie', 'Électricité', 'Menuiserie', 'Charpente / Toiture', 'Isolation', 'Rénovation complète', 'Autre']
- jobDuration: Une des valeurs suivantes : ['1 journée', '2-3 jours', '1 semaine', '2 semaines', '1 mois', 'Plusieurs mois']
- faqCategory: Une des valeurs suivantes : ['technique', 'tarifs', 'entretien', 'garanties']
- season: Une des valeurs suivantes : ['printemps', 'ete', 'automne', 'hiver']
- tone: Une des valeurs suivantes : ['professionnel', 'chaleureux', 'expert', 'dynamique']

Structure JSON attendue :
{
  \"clientName\": string|null,
  \"serviceType\": string|null,
  \"workCategory\": string|null,
  \"jobDuration\": string|null,
  \"faqCategory\": string|null,
  \"season\": string|null,
  \"tone\": string|null
}

IMPORTANT : Ne sors RIEN d'autre que du JSON. Sois précis sur les catégories.";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => "Transcript : " . $transcript],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0,
        ]);

        $data = json_decode($response->choices[0]->message->content, true);

        return is_array($data) ? $data : [];
    }

    private function toneInstruction(string $tone): string
    {
        return match ($tone) {
                'chaleureux' => "Ton chaleureux, humain et proche. Parle comme un artisan passionné qui s'adresse à ses clients avec bienveillance.",
                'expert' => "Ton expert et technique. Utilise un vocabulaire du métier précis qui inspire confiance et crédibilité.",
                'dynamique' => "Ton dynamique, vendeur et enthousiaste. Mots percutants, énergie positive, donne envie d'agir.",
                default => "Ton professionnel, sérieux et direct. Sobre, efficace, valorise le savoir-faire.",
            };
    }

    private function buildContextHints(string $template, array $options): string
    {
        $hints = [];

        if (!empty($options['clientName'])) {
            $hints[] = "Client : {$options['clientName']}";
        }
        if (!empty($options['serviceType'])) {
            $hints[] = "Service : {$options['serviceType']}";
        }
        if (!empty($options['workCategory'])) {
            $hints[] = "Travaux : {$options['workCategory']}";
        }
        if (!empty($options['jobDuration'])) {
            $hints[] = "Durée : {$options['jobDuration']}";
        }
        if (!empty($options['faqCategory'])) {
            $hints[] = "Catégorie FAQ : {$options['faqCategory']}";
        }
        if (!empty($options['season'])) {
            $hints[] = "Saison : {$options['season']}";
        }

        return implode(' | ', $hints);
    }
}
