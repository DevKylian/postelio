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
        $identity   = $this->identityInstruction($options);

        $prompt = $identity . "\n\n";

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

        // ── 5. Emoji rules (Critical Improvement) ──
        $prompt .= "RÈGLE FONDAMENTALE SUR LES ÉMOJIS :\n"
            . "- N'utilise JAMAIS d'émojis 'étoiles' (⭐, 🌟, ✨, 💫, 🤩) même si l'artisan mentionne une note ou '5 étoiles' dans son vocal.\n"
            . "- Si une note est mentionnée, laisse-la sous forme de texte (ex: '5/5' ou '5 étoiles') sans aucun émoji.\n"
            . "- Les émojis autorisés sont UNIQUEMENT ceux de structure (📍, ✔️, 🔹, 📞, 📧, 👌).\n"
            . "- Ne surcharge JAMAIS le texte d'émojis : un par paragraphe maximum.\n\n";

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
        $prompt .= "  \"caption\": \"" . $captionSpec['purpose']
            . " ({$captionSpec['idealChars'][0]}-{$captionSpec['idealChars'][1]} caractères idéal, max {$captionSpec['maxChars']}).\",\n";

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

        $prompt .= "}\n\n";

        // ── 9. Critical Global Constraints (High Priority) ──
        $hashtagInstruction = $this->hashtagInstruction($includeHashtags, $options);
        $captionStructure = empty($options['simplified']) ? $this->captionStructureInstruction($options) : "";
        
        if ($captionStructure || $hashtagInstruction) {
            $prompt .= "CONSIGNES IMPÉRATIVES POUR LE CHAMP 'caption' :\n"
                . $captionStructure . "\n"
                . $hashtagInstruction . "\n"
                . "!!! ATTENTION !!! : Les hashtags (Partie 7) DOIVENT être inclus À L'INTÉRIEUR de la valeur du champ 'caption'. Ne les oublie JAMAIS.\n\n";
        }

        $prompt .= "Réponds UNIQUEMENT par l'objet JSON ci-dessus, sans aucun texte avant ou après.";

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
     * Build the hashtag instruction string (Part 7 of the structure).
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

        return "STRUCTURE DES HASHTAGS (Partie 7) : Ajoute exactement entre 5 et 8 hashtags maximum à la fin de la légende, séparés par une ligne vide. "
            . "Trie-les par pertinence : "
            . "1. Métier" . ($tradeTag ? " (priorité : {$tradeTag})" : "") . " "
            . "> 2. Action spécifique "
            . "> 3. Matériau/Technique "
            . "> 4. Ville/Secteur OBLIGATOIRE : " . ($locationTag ? "Tu DOIS utiliser {$locationTag}." : "Ajoute un hashtag de ville (ex: #Lannion).") . " "
            . "> 5. #Postelio (indispensable à la fin).";
    }

    /**
     * Define the mandatory 7-part structure for Instagram captions.
     */
    private function captionStructureInstruction(array $options): string
    {
        $phone = $options['company_phone'] ?? null;
        $email = $options['company_email'] ?? null;
        
        $ctaPart = "6. L'Appel à l'action (Le CTA de conversion) : Une phrase d'invitation au contact.";
        if ($phone || $email) {
            $ctaPart .= " Ajoute obligatoirement les coordonnées suivantes sur une nouvelle ligne :";
            if ($phone) $ctaPart .= "\n📞 {$phone}";
            if ($email) $ctaPart .= "\n📧 {$email}";
        } else {
            $ctaPart .= " Ne mets AUCUNE coordonnée (ni téléphone, ni email) si elles ne sont pas fournies dans le contexte.";
        }

        $city = $options['company_city'] ?? null;
        $locationPart = "2. La Localisation (Le SEO Local) : Une simple mention de la ville ou du secteur pour attirer l'algorithme local d'Instagram.";
        if ($city) {
            $locationPart .= " Applique IMPÉRATIVEMENT la phrase suivante en utilisant la ville de l'identité ci-dessus : 📍 Intervention sur le secteur de {$city}.";
        } else {
            $locationPart .= " (ex: 📍 Intervention sur le secteur de [Ville]).";
        }

        $clientName = $options['clientName'] ?? null;
        $clientPart = $clientName ? " (mentionne le client : {$clientName})" : "";

        return "\nSTRUCTURE OBLIGATOIRE DE LA LÉGENDE (Respecte ces 7 points dans l'ordre, c'est CRUCIAL) :\n"
            . "1. L'Accroche visuelle (Le Hook) : Un émoji lié au métier + une phrase courte annonçant la fin du projet ou le type de réalisation{$clientPart}.\n"
            . "{$locationPart}\n"
            . "3. Le Contexte et la Technique (Le Corps du texte) : Un court paragraphe (2-3 phrases) qui explique ce qui a été fait, les matériaux ou marques utilisés, la spécificité technique. Intègre ici les éléments clés du mémo vocal.\n"
            . "4. Les Bénéfices pour le client (La Preuve de valeur) : Une courte phrase d'introduction puis une liste à puces de 3 ou 4 points (avec des emojis ✔️ ou 🔹). Traduis la technique en avantages concrets pour le client final. INTERDIT d'utiliser des étoiles ⭐ pour la liste.\n"
            . "5. La Conclusion humaine (La Fierté) : Une phrase chaleureuse sur le résultat final ou la satisfaction du client (ex: \"Une superbe transformation pour moderniser cette pièce de vie ! 👌\"). Ne rajoute AUCUN émoji décoratif supplémentaire.\n"
            . "{$ctaPart}\n"
            . "7. Le Bloc de Hashtags : Ajoute OBLIGATOIREMENT le bloc de hashtags juste après une ligne vide. Ne l'oublie JAMAIS. Voir la consigne de hashtags ci-dessous.\n";
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
        $identity     = $this->identityInstruction($options);

        $prompt = $identity . "\n\n" . $noHallucinateGlobal . $noMarkdownPrefix . $noRefuseRule . $textZoneRule;
        
        $imgConstraint = 'max 80 car.';
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
                $prompt .= "{$customPrompt}"
                    . "\n\nCONTRAINTE TEXTE IMAGE : Le champ image_text doit faire {$imgConstraint}. Au-delà, le texte sera tronqué."
                    . "\n\n{$toneInstruction}";
            } else {
                $prompt .= "Tu es un expert en marketing pour artisans. {$toneInstruction}{$extraNote}";
            }
        } else {
            $prompt .= "Tu es un expert en marketing pour artisans. Ton but est de générer une légende Instagram courte (150 caractères max) et un titre d'image concis (maximum 80 caractères).";
        }

        $prompt .= "\n\n" . $this->captionStructureInstruction($options);
        $prompt .= "\n\n" . $this->hashtagInstruction($includeHashtags, $options);
        
        $prompt .= "\n\nRÉPONDS UNIQUEMENT PAR CE JSON :\n";
        $prompt .= "{\n  \"caption\": \"La légende suivant STRICTEMENT la structure ci-dessus.\",\n  \"image_text\": \"Titre accrocheur ({$imgConstraint})\"\n}";

        return $prompt;
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
        \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] ContentRefinerService::extractFields started');
        
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

        try {
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] Calling OpenAI for extraction...');
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => $prompt],
                    ['role' => 'user', 'content' => "Transcript : " . $transcript],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0,
            ]);
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] OpenAI Response received');

            $data = json_decode($response->choices[0]->message->content, true);
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] JSON decoded', ['data' => $data]);

            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[AUDIO DEBUG] OpenAI Extraction Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return [];
        }
    }

    /**
     * Build the identity block for the AI.
     */
    private function identityInstruction(array $options): string
    {
        $name = $options['company_name'] ?? 'Inconnu';
        $city = $options['company_city'] ?? 'France';
        $trade = $options['company_trade'] ?? 'Artisan';
        $phone = $options['company_phone'] ?? null;
        $email = $options['company_email'] ?? null;

        $str = "═══ IDENTITÉ DE L'ARTISAN (CRITIQUE) ═══\n"
            . "- ENTREPRISE : {$name}\n"
            . "- VILLE/SECTEUR : {$city}\n"
            . "- MÉTIER : {$trade}";
        
        if ($phone) $str .= "\n- TÉLÉPHONE : {$phone}";
        if ($email) $str .= "\n- EMAIL : {$email}";

        return $str;
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
