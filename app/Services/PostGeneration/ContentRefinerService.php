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
     * or { caption, title?, slides? } for carousel templates.
     *
     * Uses template-specs.php to dynamically build the AI prompt with
     * precise field requirements, character limits, and content instructions.
     */
    public function refine(string $transcript, string $template, int $photoCount = 0, array $options = []): string
    {
        $spec = config("template-specs.{$template}");
        $isCarousel = $spec ? $spec['type'] === 'carousel' : in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique', 'project', 'faq']);

        // Inject artisan bio into options so prompt builders can use it
        // bio_text is passed via options['artisan_bio'] from the Livewire component

        // Build the prompt from template spec or fall back to custom/default
        if ($spec) {
            $prompt = $this->buildPromptFromSpec($spec, $template, $photoCount, $options);
        } else {
            $prompt = $this->buildFallbackPrompt($template, $photoCount, $options);
        }

        // Ensure the word "json" is present for the API requirement
        if (!str_contains(strtolower($prompt), 'json')) {
            $prompt .= "\n\nRéponds UNIQUEMENT sous forme de JSON valide.";
        }

        // Simplification override
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
     * Build a complete AI prompt from the template specification.
     *
     * The prompt tells OpenAI exactly:
     * - What type of content to generate
     * - How many text fields are needed
     * - The character limits for each field
     * - The expected JSON structure
     * - Context from the artisan's options
     */
    private function buildPromptFromSpec(array $spec, string $template, int $photoCount, array $options): string
    {
        $tone = $options['tone'] ?? 'professionnel';
        $includeHashtags = $options['includeHashtags'] ?? true;
        $customNote = $options['customNote'] ?? null;

        $prompt = '';

        // ── 1. Global anti-hallucination rule ──
        $prompt .= "RÈGLE FONDAMENTALE — Anti-invention (priorité absolue) :\n"
            . "Utilise UNIQUEMENT les informations explicitement présentes dans la transcription fournie.\n"
            . "N'invente jamais de noms, prénoms, chiffres, dates, réalisations ou détails absents du vocal.\n"
            . "Si le mémo est court ou vague, génère un post court et sobre qui reste dans ce cadre — "
            . "ne complète JAMAIS avec du contenu inventé pour le rendre plus complet.\n"
            . "Adapte la longueur et la richesse de la légende à la richesse du mémo vocal : mémo court = légende courte.\n\n";

        // ── 2. Format rules ──
        $prompt .= "RÈGLE DE FORMAT STRICTE :\n"
            . "- Jamais de Markdown : aucun **, aucun *, aucun _, aucun #. Instagram n'en supporte pas.\n"
            . "- Structure la légende avec de vrais sauts de ligne entre chaque idée/paragraphe.\n"
            . "- Exemple de structure : accroche, ligne vide, corps espacé, ligne vide, call-to-action, ligne vide, hashtags.\n\n";

        // ── 3. Image text rules ──
        $prompt .= "RÈGLE DE RÉDACTION STRICTE SUR LES TEXTES D'IMAGE :\n"
            . "- Les textes affichés sur l'image (image_text, title, slides) s'affichent dans des zones visuelles FIXES.\n"
            . "- Si le texte est trop long, il sera TRONQUÉ automatiquement et le résultat sera visuellement CASSÉ.\n"
            . "- Tu DOIS respecter scrupuleusement les limites de caractères indiquées pour chaque champ.\n"
            . "- Préfère un texte court et percutant à un texte long qui déborde.\n"
            . "- Compte les caractères avant de répondre.\n"
            . "- Les mots composés avec trait d'union comptent comme UN SEUL mot (ex: 'savoir-faire'). Ne coupe JAMAIS un mot composé.\n\n";

        // ── 4. No-refuse rule ──
        $prompt .= "RÈGLE ABSOLUE : Tu dois TOUJOURS générer le contenu demandé. Si certains champs de contexte "
            . "semblent ne pas correspondre au mémo vocal, ignore-les et travaille avec le transcript uniquement. Ne refuse jamais.\n\n";

        // ── 5. AI role and content instructions ──
        $prompt .= $spec['ai_role'] . ' ' . $this->toneInstruction($tone) . "\n\n";
        $prompt .= "TYPE DE POST : " . $spec['description'] . "\n";
        $prompt .= "INSTRUCTIONS : " . $spec['ai_content_instructions'] . "\n\n";

        // ── 6. Context from artisan options ──
        $contextParts = [];
        foreach ($spec['context_fields'] as $field) {
            $value = $options[$field] ?? null;
            if ($value && $value !== 'auto') {
                $label = $this->contextFieldLabel($field);
                $contextParts[] = "{$label} : {$value}";
            }
        }
        if ($customNote) {
            $contextParts[] = "Note de l'artisan : {$customNote}";
        }

        // ── Artisan bio context (from onboarding presentation) ──
        $artisanBio = $options['artisan_bio'] ?? null;
        if ($artisanBio) {
            $prompt .= "PROFIL DE L'ARTISAN (extrait de sa présentation personnelle) :\n"
                . "{$artisanBio}\n"
                . "Utilise ces informations pour personnaliser le ton et les services mentionnés, "
                . "mais UNIQUEMENT si elles sont cohérentes avec le mémo vocal.\n\n";
        }

        if (!empty($contextParts)) {
            $prompt .= "CONTEXTE FOURNI PAR L'ARTISAN :\n" . implode("\n", $contextParts) . "\n\n";
        }

        // ── 7. Build JSON structure specification ──
        $prompt .= "═══ STRUCTURE JSON ATTENDUE ═══\n";
        $prompt .= "Tu dois répondre UNIQUEMENT par un objet JSON valide avec EXACTEMENT cette structure :\n\n";
        $prompt .= "{\n";

        // Caption field
        $captionSpec = $spec['caption'];
        $hashtagInstruction = $this->hashtagInstruction($includeHashtags, $options);
        $prompt .= "  \"caption\": \"" . $captionSpec['purpose']
            . " ({$captionSpec['idealChars'][0]}-{$captionSpec['idealChars'][1]} caractères idéal, max {$captionSpec['maxChars']}). "
            . "{$hashtagInstruction}\",\n";

        // Template-specific fields
        foreach ($spec['fields'] as $field) {
            $charConstraint = "STRICTEMENT entre {$field['idealChars'][0]} et {$field['idealChars'][1]} caractères "
                . "(maximum absolu : {$field['maxChars']} car. — au-delà le texte sera coupé)";

            if ($field['isArray']) {
                // Determine count
                $count = $this->resolveFieldCount($field, $photoCount, $options);
                $prompt .= "  \"{$field['name']}\": [\n";
                for ($i = 1; $i <= min($count, 3); $i++) {
                    $comma = ($i < min($count, 3)) ? ',' : ($count > 3 ? ', ...' : '');
                    $prompt .= "    \"Texte slide {$i} — {$field['purpose']} {$charConstraint}\"{$comma}\n";
                }
                $prompt .= "  ]  // Exactement {$count} " . ($count > 1 ? 'textes' : 'texte') . " dans ce tableau\n";
            } else {
                $prompt .= "  \"{$field['name']}\": \"{$field['purpose']} {$charConstraint}\"\n";
            }
        }

        $prompt .= "}\n\n";

        // ── 8. Critical reminders ──
        $prompt .= "RAPPELS CRITIQUES :\n";
        $prompt .= "- Les champs image (image_text, title, slides) ne doivent JAMAIS contenir de hashtags, d'emojis ni de numéros d'étape.\n";
        $prompt .= "- Respecte EXACTEMENT les limites de caractères. COMPTE avant de répondre.\n";
        $prompt .= "- Chaque texte doit avoir du SENS seul — jamais une phrase coupée au milieu.\n";

        if ($photoCount > 0 && $spec['type'] === 'carousel') {
            $prompt .= "- Nous avons {$photoCount} photos. Adapte le nombre de slides en conséquence.\n";
        }

        return $prompt;
    }

    /**
     * Resolve how many items a dynamic array field should contain.
     */
    private function resolveFieldCount(array $field, int $photoCount, array $options): int
    {
        if (is_int($field['count'])) {
            return $field['count'];
        }

        // Dynamic count
        $source = $field['countSource'] ?? 'content';

        if ($source === 'photos' && $photoCount > 0) {
            return max($field['countMin'] ?? 1, min($photoCount, $field['countMax'] ?? 8));
        }

        if ($source === 'targetSlides') {
            $target = (int) ($options['targetSlides'] ?? ($field['countDefault'] ?? 5));
            // targetSlides includes cover, so subtract 1 for the content slides
            $slideCount = max(1, $target - 1);
            return max($field['countMin'] ?? 1, min($slideCount, $field['countMax'] ?? 8));
        }

        // 'content' source — let AI decide based on content, give a reasonable range
        $default = $field['countDefault'] ?? $field['countMin'] ?? 1;
        $max = $field['countMax'] ?? 4;
        return max($default, min($default, $max));
    }

    /**
     * Build the hashtag instruction string.
     */
    private function hashtagInstruction(bool $includeHashtags, array $options): string
    {
        if (!$includeHashtags) {
            return "N'ajoute aucun hashtag.";
        }

        $city = $options['company_city'] ?? null;
        $trade = $options['company_trade'] ?? null;
        $locationTag = $city ? "#" . str_replace(' ', '', $city) : null;
        $tradeTag = $trade ? "#" . str_replace([' ', '/'], '', $trade) : null;

        return "Ajoute exactement 5 hashtags à la fin de la légende, séparés par une ligne vide. "
            . "Structure : 2 métier" . ($tradeTag ? " (dont {$tradeTag})" : "")
            . " + 2 locaux" . ($locationTag ? " (dont {$locationTag} et #Artisan" . str_replace('#', '', $locationTag) . ")" : " (ville/département)")
            . " + 1 hashtag #Postelio (indispensable à la fin). "
            . "Sois pertinent et local pour attirer des clients de proximité.";
    }

    /**
     * Build fallback prompt for custom templates or unknown template types.
     */
    private function buildFallbackPrompt(string $template, int $photoCount, array $options): string
    {
        $tone = $options['tone'] ?? 'professionnel';
        $includeHashtags = $options['includeHashtags'] ?? true;
        $customNote = $options['customNote'] ?? null;
        $toneInstruction = $this->toneInstruction($tone);
        $hashtagInstruction = $this->hashtagInstruction($includeHashtags, $options);
        $extraNote = $customNote ? " Note de l'artisan : {$customNote}." : '';

        $noHallucinateGlobal = "RÈGLE FONDAMENTALE — Anti-invention (priorité absolue) :\n"
            . "Utilise UNIQUEMENT les informations explicitement présentes dans la transcription fournie.\n"
            . "N'invente jamais de noms, prénoms, chiffres, dates, réalisations ou détails absents du vocal.\n"
            . "Si le mémo est court ou vague, génère un post court et sobre qui reste dans ce cadre — "
            . "ne complète JAMAIS avec du contenu inventé pour le rendre plus complet.\n"
            . "Adapte la longueur et la richesse de la légende à la richesse du mémo vocal : mémo court = légende courte.\n\n";

        $noMarkdownPrefix = "RÈGLE DE FORMAT STRICTE :\n"
            . "- Jamais de Markdown : aucun **, aucun *, aucun _, aucun #. Instagram n'en supporte pas.\n"
            . "- Structure la légende avec de vrais sauts de ligne entre chaque idée/paragraphe.\n\n";

        $textZoneRule = "RÈGLE ZONE DE TEXTE (priorité haute) :\n"
            . "Le texte affiché sur l'image (image_text, title, slides) sera rendu dans une zone visuelle fixe.\n"
            . "Si le texte est trop long, il sera TRONQUÉ automatiquement et le résultat sera visuellement cassé.\n"
            . "Tu DOIS donc respecter les limites de caractères indiquées pour chaque champ.\n"
            . "Préfère un texte court et percutant à un texte long qui déborde.\n"
            . "Compte les caractères avant de répondre.\n\n";

        $noRefuseRule = "RÈGLE ABSOLUE : Tu dois TOUJOURS générer le contenu demandé. Ne refuse jamais.\n\n";

        $captionRange = "200-400 caractères";

        if ($template === 'custom') {
            $customPrompt = $options['custom_template_prompt'] ?? null;
            $layout = $options['custom_layout_type'] ?? 'standard';
            $zone = config("template-text-zones.custom.{$layout}")
                ?? config('template-text-zones.custom.standard')
                ?? ['idealChars' => [60, 150], 'maxChars' => 180];
            [$min, $max] = $zone['idealChars'];
            $hard = $zone['maxChars'];
            $imgConstraint = "STRICTEMENT entre {$min} et {$max} caractères (maximum absolu : {$hard} car.)";

            if ($customPrompt) {
                return $noHallucinateGlobal . $noMarkdownPrefix . $textZoneRule
                    . "{$customPrompt}"
                    . "\n\nCONTRAINTE TEXTE IMAGE : Le champ image_text doit faire {$imgConstraint}. Au-delà, le texte sera tronqué."
                    . "\n\n{$toneInstruction}"
                    . "\n" . ($includeHashtags ? "Ajoute 3 à 5 hashtags pertinents à la fin de la légende. Le dernier doit TOUJOURS être #Postelio." : "N'ajoute aucun hashtag.")
                    . ($extraNote ? "\n{$extraNote}" : "");
            }
            return $noHallucinateGlobal . $noMarkdownPrefix . $noRefuseRule . $textZoneRule
                . "Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote} "
                . "Tu dois répondre UNIQUEMENT par un objet JSON valide : {\"caption\": \"Légende Instagram ({$captionRange}). {$hashtagInstruction}\", \"image_text\": \"Titre accrocheur et concis ({$imgConstraint}, phrase complète). Sans hashtag.\"}";
        }

        // Unknown template — generic fallback
        return $noHallucinateGlobal . $noMarkdownPrefix . $noRefuseRule . $textZoneRule
            . "Tu dois répondre UNIQUEMENT par un objet JSON valide : {\"caption\": \"Légende Instagram courte (150 caractères max). {$hashtagInstruction}\", \"image_text\": \"Titre concis (maximum 80 caractères, complet)\"}";
    }

    /**
     * Get human-readable label for a context field.
     */
    private function contextFieldLabel(string $field): string
    {
        return match ($field) {
            'clientName' => 'Prénom du client',
            'serviceType' => 'Service réalisé',
            'workCategory' => 'Type de travaux',
            'jobDuration' => 'Durée du chantier',
            'faqCategory' => 'Catégorie FAQ',
            'season' => 'Saison ciblée',
            'targetSlides' => 'Nombre de slides souhaité',
            default => ucfirst($field),
        };
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
        $zones = config('template-text-zones') ?? [];

        if ($template === 'custom') {
            $layout = $options['custom_layout_type'] ?? 'standard';
            return $zones['custom'][$layout] ?? ($zones['custom']['standard'] ?? null);
        }

        return $zones[$template][$field] ?? null;
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
}
