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

        $prompt = $noHallucinateGlobal . $noMarkdownPrefix . $this->getPromptForTemplate($template, $photoCount, $options);

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

        // ── JSON schema descriptions ──────────────────────────────────────────

        // For templates that show text on the image
        $jsonSingleWithImageText = fn(string $captionDesc, string $imageTextDesc) =>
        "Tu dois répondre UNIQUEMENT par un objet JSON valide avec exactement cette structure :
{
  \"caption\": \"{$captionDesc}\",
  \"image_text\": \"{$imageTextDesc}\"
}
IMPORTANT : image_text ne doit JAMAIS contenir de hashtags, d'emojis ni de ponctuation lourde.";

        // For templates that do NOT show text on the image (lifestyle, project)
        $jsonCaptionOnly = "Tu dois répondre UNIQUEMENT par un objet JSON valide avec exactement cette structure :
{\"caption\": \"La légende Instagram complète (250-400 caractères, storytelling, {$hashtagInstruction})\"}";

        // For carousels
        $jsonCarousel = "Tu dois répondre UNIQUEMENT par un objet JSON valide avec cette structure :
{
  \"caption\": \"La légende Instagram complète (300-500 caractères, {$hashtagInstruction})\",
  \"slides\": [\"Texte slide 1\", \"Texte slide 2\", ...]
}";

        return match ($template) {

                // ── Single templates WITH image text ─────────────────────────────

                'review' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}"
                . ($options['clientName'] ? " Prénom du client : {$options['clientName']}." : "")
                . ($options['serviceType'] ? " Contexte du service (indicatif) : {$options['serviceType']}." : "")
                . "{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram complète avec storytelling (250-400 caractères). {$hashtagInstruction}",
                "Citation courte et percutante tirée de l'avis client. MAX 70 caractères. Sans hashtag."
            ),

                'faq' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}"
                . ($options['faqCategory'] ? " Catégorie FAQ : {$options['faqCategory']}." : "")
                . "{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram avec la réponse développée à la question (250-400 caractères). {$hashtagInstruction}",
                "La question seule, courte, percutante, avec point d'interrogation. MAX 75 caractères. Sans hashtag."
            ),

                'quality_focus' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram qui développe pourquoi ce matériau/équipement garantit qualité et durabilité (250-400 caractères). {$hashtagInstruction}",
                "Nom ou courte description du matériau/équipement mis en valeur. MAX 55 caractères. Majuscules recommandées. Sans hashtag."
            ),

                'conseil_saisonnier' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}"
                . ($options['season'] ? " Saison ciblée : {$options['season']}." : "")
                . "{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram avec contexte saisonnier développé (250-400 caractères). {$hashtagInstruction}",
                "Le conseil saisonnier actionnable, commençant par un verbe (Vérifiez, Anticipez, Préparez...). MAX 85 caractères. Sans hashtag."
            ),

                'erreur_a_eviter' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . $jsonSingleWithImageText(
                "Légende Instagram qui décrit l'erreur et explique comment l'éviter (250-400 caractères). {$hashtagInstruction}",
                "L'erreur formulée en alerte courte et directe (ex: Peindre sur une surface humide.). MAX 75 caractères. Sans hashtag."
            ),

                // ── Single templates WITHOUT image text ──────────────────────────

                'lifestyle' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . str_replace('{$hashtagInstruction}', $hashtagInstruction, $jsonCaptionOnly),

                'project' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}"
                . ($options['workCategory'] ? " Type de travaux : {$options['workCategory']}." : "")
                . ($options['jobDuration'] ? " Durée : {$options['jobDuration']}." : "")
                . "{$extraNote} "
                . str_replace('{$hashtagInstruction}', $hashtagInstruction, $jsonCaptionOnly),

                // ── Carousels ─────────────────────────────────────────────────────

                'chantier_detail' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . "À partir de la description vocale d'un chantier, crée un post carrousel. Nous avons {$photoCount} photos. "
                . "{$jsonCarousel} Le premier slide est la couverture (titre accrocheur, MAX 50 caractères). Les slides suivants décrivent les détails correspondant aux photos (MAX 60 caractères chacun).",

                'step_by_step' => "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . "À partir du vocal décrivant un processus, crée un carrousel étape par étape. Nous avons {$photoCount} photos. "
                . "{$jsonCarousel} Le premier slide est le titre du process (MAX 50 caractères). Les slides suivants sont les étapes numérotées (MAX 55 caractères chacun, sans le préfixe 'Étape X :').",

                'guide_pedagogique' => (function () use ($noRefuseRule, $toneInstruction, $extraNote, $jsonCarousel, $options) {
                $targetSlides = isset($options['targetSlides']) ? (int)$options['targetSlides'] : 5;
                $slidesRange = max(3, $targetSlides - 1) . ' à ' . min(8, $targetSlides + 1);
                return "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                    . "À partir du vocal, crée un carrousel éducatif. "
                    . "{$jsonCarousel} Le premier slide est une question/accroche forte (MAX 80 caractères). "
                    . "Les slides suivants ({$slidesRange} au total) développent les conseils (MAX 90 caractères chacun). "
                    . "Le dernier slide est un appel à l'action (MAX 60 caractères).";
            })(),

                'custom' => (function () use ($options, $noRefuseRule, $toneInstruction, $hashtagInstruction, $extraNote, $includeHashtags) {
                $customPrompt = $options['custom_template_prompt'] ?? null;
                if ($customPrompt) {
                    return "{$customPrompt}"
                        . "\n\n{$toneInstruction}"
                        . "\n" . ($includeHashtags ? "Ajoute 3 à 5 hashtags pertinents à la fin de la légende." : "N'ajoute aucun hashtag.")
                        . ($extraNote ? "\n{$extraNote}" : "");
                }
                return "{$noRefuseRule} Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                    . "{\"caption\": \"Légende Instagram (250-400 caractères). {$hashtagInstruction}\", \"image_text\": null}";
            })(),

                default => "{$noRefuseRule} {\"caption\": \"Légende Instagram courte (150 caractères max). {$hashtagInstruction}\"}",
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
