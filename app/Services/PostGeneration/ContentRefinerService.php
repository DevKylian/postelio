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

        // Normalize caption formatting (ensure proper line breaks)
        $raw = $this->normalizeCaptionInJson($raw);

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

        // ── 1. Global anti-hallucination & Industry Intelligence rule (LOCKED) ──
        $prompt .= "RÈGLES ABSOLUES DE RÉDACTION - SOUS PEINE D'ÉCHEC :\n"
            . "1. COHÉRENCE DES PRONOMS : Il est STRICTEMENT INTERDIT de mélanger le 'Je' et le 'Nous'. Tu dois être constant. Si le vocal dit 'Je', tout le texte généré (y compris la conclusion) est au singulier. S'il dit 'On' ou 'Nous', tout est au pluriel.\n"
            . "2. GABARIT 'ERREUR À ÉVITER' / DEVIS : Si le vocal parle d'un devis ou conseil, INTERDICTION d'utiliser 'Intervention'. Utilise : '📍 Conseil d'expert - Secteur de [Ville]'.\n"
            . "3. ANTI-HALLUCINATION : Ne cite que les matériaux ou tâches réellement mentionnés dans le vocal.\n"
            . "4. CORRECTION PHONÉTIQUE : Corrige le bois (chêne) vs métal (chaîne), poêle (chauffage) vs poil, etc.\n"
            . "5. NOM D'ENTREPRISE (NOM PROPRE) : Il est STRICTEMENT INTERDIT d'utiliser le nom de l'entreprise dans la TOUTE PREMIÈRE PHRASE du post (l'accroche). Le nom est un Nom Propre INVARIABLE. Il est FORMELLEMENT INTERDIT de l'utiliser comme un verbe (ex: ne dis jamais 'Lorsque vous [Nom]...'). Utilise des verbes réels (rénover, installer, réaliser) et mentionne l'entreprise uniquement plus bas (ex: 'L'équipe de [Nom]...').\n"
            . "6. LOGIQUE ET BON SENS : Le texte doit être naturel. Pas de phrases qui ne veulent rien dire.\n\n";

        // ── 2. Format rules ──
        $prompt .= "RÈGLE DE FORMAT STRICTE :\n"
            . "- Jamais de Markdown : aucun **, aucun *, aucun _, aucun #. Instagram n'en supporte pas.\n"
            . "- Dans la valeur JSON du champ \"caption\", utilise OBLIGATOIREMENT \\n\\n (double saut de ligne) entre chaque section.\n"
            . "- NE JAMAIS écrire la légende en une seule ligne compacte. Chaque section doit être séparée.\n"
            . "- Exemple de structure avec \\n\\n : \"🔌 Accroche...\\n\\n📍 Localisation...\\n\\nCorps du texte...\\n\\n✔️ Bénéfice 1\\n✔️ Bénéfice 2\\n\\n👌 Conclusion...\\n\\n📞 Téléphone\\n📧 Email\\n\\n#hashtag1 #hashtag2\"\n\n";

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
        $prompt .= $this->citySpellingReminder($options);

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

        $city       = $options['company_city']   ?? null;
        $postalCode = $options['company_postal'] ?? null;
        $locationTag = $city ? "#" . preg_replace('/[^a-zA-ZÀ-ÿ0-9]/u', '', $city) : null;
        $locationHint = $locationTag
            ? "Tu DOIS utiliser {$locationTag}" . ($postalCode ? " (code postal {$postalCode})" : "") . "."
            : "Ajoute un hashtag de ville (ex: #Lannion).";

        return "STRUCTURE DES HASHTAGS (Partie 7) : Ajoute entre 5 et 8 hashtags maximum à la fin.\n"
            . "- Priorité 1 : Le type de travaux RÉEL décris dans le vocal (ex: #plomberie si le vocal parle de fuite, même si l'artisan est électricien).\n"
            . "- Priorité 2 : Action ou matériau spécifique cité.\n"
            . "- Priorité 3 : Ville/Secteur OBLIGATOIRE : " . $locationHint . "\n"
            . "- Priorité 4 : #Postelio (indispensable à la fin).";
    }

    /**
     * Define the mandatory 7-part structure for Instagram captions.
     */
    private function captionStructureInstruction(array $options): string
    {
        $phone = $options['company_phone'] ?? null;
        $email = $options['company_email'] ?? null;
        
        $ctaPart = "6. L'Appel à l'action (Le CTA de conversion) : Utilise OBLIGATOIREMENT cette phrase : \"📩 Un projet en tête ? Contactez-nous en message privé.\"";
        if ($phone || $email) {
            $ctaPart .= " Ajoute également les coordonnées suivantes sur une nouvelle ligne :";
            if ($phone) $ctaPart .= "\n📞 {$phone}";
            if ($email) $ctaPart .= "\n📧 {$email}";
        } else {
            $ctaPart .= " Ne mets AUCUNE coordonnée (ni téléphone, ni email) si elles ne sont pas fournies dans le contexte.";
        }

        $city       = $options['company_city']   ?? null;
        $postalCode = $options['company_postal'] ?? null;
        $cityLabel  = $city ? $city . ($postalCode ? " ({$postalCode})" : "") : "[Ville]";

        $locationPart = "2. La Localisation (Le SEO Local) : Une mention de la ville ou du secteur.\n"
            . "RÈGLE ABSOLUE : S'il s'agit d'un devis, d'un conseil ou d'une erreur, il est FORMELLEMENT INTERDIT d'utiliser le mot 'Intervention'. Tu DOIS commencer par '📍 Conseil d'expert - Secteur de {$cityLabel}'. Pour un chantier fini, utilise '📍 Intervention sur le secteur de {$cityLabel}'.";

        $clientName = $options['clientName'] ?? null;
        $clientPart = $clientName ? " (mentionne le client : {$clientName})" : "";

        return "\nSTRUCTURE OBLIGATOIRE DE LA LÉGENDE (Respecte ces 7 points dans l'ordre, séparés par \\n\\n dans le JSON, c'est CRUCIAL) :\n"
            . "1. L'Accroche visuelle (Le Hook) : Un émoji LIÉ AUX TRAVAUX RÉELS (ex: 🚿 pour plomberie, 🛠️ pour bricolage, 🔌 pour électricité) + une phrase courte annonçant la fin du projet ou le type de réalisation{$clientPart}.\n"
            . "RÈGLE CRIMINELLE : Il est STRICTEMENT INTERDIT de mentionner le nom de l'entreprise dans cette première phrase.\n"
            . "Attention : si les travaux décrits ne correspondent pas au métier habituel de l'artisan, utilise l'émoji des travaux (ex: n'utilise pas 🔌 si le vocal parle de changer un robinet).\n"
            . "{$locationPart}\n"
            . "3. Le Contexte et la Technique (Le Corps du texte) : Un court paragraphe (2-3 phrases) qui explique ce qui a été fait, les matériaux ou marques utilisés, la spécificité technique. Intègre ici les éléments clés du mémo vocal.\n"
            . "4. Les Bénéfices pour le client (La Preuve de valeur) : Une courte phrase d'introduction puis une liste à puces de 3 ou 4 points (avec des emojis ✔️ ou 🔹). Traduis la technique en avantages concrets pour le client final. INTERDIT d'utiliser des étoiles ⭐ pour la liste.\n"
            . "5. La Conclusion humaine (La Fierté) : Une phrase chaleureuse unique et SPECIFIQUE au contenu du vocal.\n"
            . "RÈGLE ABSOLUE : Bannis définitivement la phrase générique 'Nous sommes fiers de...' ou 'moderniser cette pièce de vie'. Rédige une conclusion qui reflète l'expertise ou le résultat réel du vocal. Respecte strictement la règle de cohérence Je/Nous définie au début.\n"
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

        $noHallucinateGlobal = "RÈGLES ABSOLUES DE RÉDACTION - SOUS PEINE D'ÉCHEC :\n"
            . "1. COHÉRENCE DES PRONOMS : Il est STRICTEMENT INTERDIT de mélanger le 'Je' et le 'Nous'. Si l'artisan dit 'Je', tout le texte généré DOIT être au singulier. S'il dit 'On' ou 'Nous', tout doit être au pluriel.\n"
            . "2. GABARIT 'ERREUR À ÉVITER' / DEVIS : Si la note vocale parle d'un devis, d'un conseil ou d'un constat, il est FORMELLEMENT INTERDIT d'utiliser le mot 'Intervention'. Tu DOIS commencer par '📍 Conseil d'expert - Secteur de [Ville]'.\n"
            . "3. ANTI-INVENTION : Rédige le post en te basant STRICTEMENT sur le vocal. N'invente aucune prestation absente.\n"
            . "4. CORRECTION PHONÉTIQUE : Corrige systématiquement les homophones (ex: 'chêne' pas 'chaîne', 'poêle' pas 'poil').\n"
            . "5. NOM D'ENTREPRISE (NOM PROPRE) : Le nom de l'entreprise (voir 'IDENTITÉ') est un NOM PROPRE. Il est STRICTEMENT INTERDIT de l'utiliser comme un verbe (ex: NE DIS JAMAIS 'quand vous [Nom] votre cuisine' ou 'nous allons [Nom]...'). Si mentionné, il doit être SUJET (ex: 'L'équipe de [Nom]...').\n"
            . "6. LOGIQUE ET SENS : Le texte doit avoir un sens réel et professionnel. Ne produis pas de phrases absurdes.\n\n";

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
        $prompt .= $this->citySpellingReminder($options);

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
     * Ensure the caption field in the JSON has proper line breaks between sections.
     * The AI sometimes forgets \n separators and writes everything on one line.
     */
    private function normalizeCaptionInJson(string $json): string
    {
        $data = json_decode($json, true);
        if (!is_array($data) || empty($data['caption'])) {
            return $json;
        }

        $caption = $data['caption'];

        // Normalize line endings
        $caption = str_replace(["\r\n", "\r"], "\n", $caption);

        // If caption has very few newlines it is likely compact — reconstruct structure
        if (substr_count($caption, "\n") < 3) {
            // Double newline before location emoji (📍) that appears mid-text
            $caption = preg_replace('/(?<=[^\n]) (📍)/', "\n\n$1", $caption);

            // Double newline before section-level emojis appearing after a period/space mid-text
            foreach (['👌', '📞', '📧', '💡', '🔑', '🏠', '🔧', '⚡', '🪛', '🧱', '🔌'] as $emoji) {
                $caption = preg_replace('/(?<=[^\n]) (' . preg_quote($emoji, '/') . ')/', "\n\n$1", $caption);
            }

            // Single newline before bullet-point emojis (✔️ and 🔹) appearing after a space
            foreach (['✔️', '🔹'] as $emoji) {
                $caption = preg_replace('/(?<=[^\n]) (' . preg_quote($emoji, '/') . ')/', "\n$1", $caption);
            }

            // Double newline before the hashtag block
            $caption = preg_replace('/(?<=[^\n]) (#\w)/', "\n\n$1", $caption);
        }

        // Collapse 3+ consecutive newlines
        $caption = preg_replace("/\n{3,}/", "\n\n", $caption);
        $caption = trim($caption);

        $data['caption'] = $caption;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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

    /**
     * Correct phonetically deformed city names in a Whisper transcript.
     *
     * Whisper sometimes produces heavily distorted renderings of French commune names
     * (e.g. "saint neon maille" instead of "Sainte-Néomaye"). A dedicated, minimal
     * GPT-4o call with the official commune list resolves this reliably before the
     * main post-generation prompt runs.
     *
     * Only called when the company has nearby_cities stored (i.e. city was selected
     * via the autocomplete). Returns the transcript unchanged on any error.
     */
    /**
     * Correct phonetically deformed city names in a Whisper transcript.
     *
     * Always runs when a base city is known. If the stored nearby_cities list is empty,
     * it fetches the communes server-side from the French geo API (cached 24h) using
     * the postal code — so it works even if the user never clicked the autocomplete.
     *
     * @param string      $baseCity     Company's home city (e.g. "Niort")
     * @param array       $nearbyCities Pre-stored list e.g. ["Sainte-Néomaye (79260)", ...]
     * @param string|null $postalCode   Used to fetch nearby cities when list is empty
     */
    public function correctCityNamesInTranscript(
        string $transcript,
        string $baseCity,
        array $nearbyCities = [],
        ?string $postalCode = null
    ): string {
        // If nearby cities not stored yet, fetch them server-side (cached 24h)
        if (empty($nearbyCities) && $postalCode) {
            $nearbyCities = TranscriptionService::fetchNearbyCities($baseCity, $postalCode);
        }

        // Build commune list for the prompt (names only, no postal codes)
        $names = array_map(
            fn($c) => preg_replace('/\s*\(\d+\)$/', '', $c),
            array_slice($nearbyCities, 0, 60)
        );
        $communeList = !empty($names) ? implode(', ', $names) : null;

        $listSection = $communeList
            ? "Communes officielles dans un rayon de 35 km autour de {$baseCity} :\n{$communeList}\n\n"
            : "L'artisan est basé à {$baseCity}.\n\n";

        $prompt = "Tu es un correcteur orthographique spécialisé dans les noms de communes françaises.\n\n"
            . "La transcription automatique Whisper ci-dessous peut contenir des noms de communes "
            . "phonétiquement déformés ou mal découpés.\n"
            . "Exemples typiques d'erreurs Whisper :\n"
            . "- 'saint neon maille' → Sainte-Néomaye\n"
            . "- 'chiré' → Échiré\n"
            . "- 'aifre' → Aiffres\n"
            . "- 'peros' → Perros-Guirec\n\n"
            . $listSection
            . "TÂCHE : Identifie dans la transcription tout mot ou groupe de mots qui correspond "
            . "phonétiquement à une commune française réelle" . ($communeList ? " de la liste ci-dessus" : " du secteur de {$baseCity}") . ", "
            . "et remplace-le par le nom officiel exact.\n"
            . "Ne modifie RIEN d'autre dans le texte.\n"
            . "Si aucun nom de commune n'est déformé, retourne le texte identique.\n\n"
            . "Transcription :\n"
            . $transcript . "\n\n"
            . "Réponds UNIQUEMENT avec la transcription corrigée, sans explication.";

        try {
            $response = OpenAI::chat()->create([
                'model'       => 'gpt-4o',
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'max_tokens'  => 800,
                'temperature' => 0,
            ]);

            $corrected = trim($response->choices[0]->message->content);

            // Safety: if GPT returns something suspiciously short, keep original
            if (mb_strlen($corrected) < mb_strlen($transcript) * 0.5) {
                \Illuminate\Support\Facades\Log::warning('[CityCorrection] Response too short, keeping original');
                return $transcript;
            }

            if ($corrected !== $transcript) {
                \Illuminate\Support\Facades\Log::info('[CityCorrection] Corrected transcript', [
                    'before' => $transcript,
                    'after'  => $corrected,
                ]);
            }

            return $corrected;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[CityCorrection] Failed: ' . $e->getMessage());
            return $transcript;
        }
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
        $name        = $options['company_name']    ?? 'Inconnu';
        $city        = $options['company_city']    ?? 'France';
        $postalCode  = $options['company_postal']  ?? null;
        $context     = $options['company_context'] ?? null; // "79, Deux-Sèvres, Nouvelle-Aquitaine"
        $nearbyCities = $options['company_nearby'] ?? [];   // ["Sainte-Néomaye (79260)", "Aiffres (79230)", ...]
        $trade       = $options['company_trade']   ?? 'Artisan';
        $phone       = $options['company_phone']   ?? null;
        $email       = $options['company_email']   ?? null;

        // Build a rich location string: "Niort (79000) — Deux-Sèvres, Nouvelle-Aquitaine"
        $locationStr = $city;
        if ($postalCode) {
            $locationStr .= " ({$postalCode})";
        }
        if ($context) {
            // context is "79, Deux-Sèvres, Nouvelle-Aquitaine" — strip the dept number prefix
            $contextParts = array_map('trim', explode(',', $context, 2));
            $locationStr .= " — " . ($contextParts[1] ?? $context);
        }

        $str = "═══ IDENTITÉ DE L'ARTISAN (CRITIQUE) ═══\n"
            . "- NOM DE L'ENTREPRISE : {$name}\n"
            . "  (RÈGLE : C'est un NOM PROPRE invariable, pas un verbe. Ne jamais l'utiliser dans l'accroche.)\n"
            . "- VILLE/SECTEUR : {$locationStr}\n"
            . "- MÉTIER : {$trade}";

        if ($phone) $str .= "\n- TÉLÉPHONE : {$phone}";
        if ($email) $str .= "\n- EMAIL : {$email}";

        // Geographic context: provide the real list of nearby communes so the AI can
        // resolve phonetic/approximated city names from Whisper transcriptions.
        $str .= "\n\n═══ GÉOGRAPHIE — COMMUNES DU SECTEUR (CRITIQUE) ═══\n"
            . "L'artisan est basé à {$locationStr}.\n";

        if (!empty($nearbyCities)) {
            $communeList = implode(', ', array_slice($nearbyCities, 0, 50));
            $str .= "Communes dans un rayon de 35 km (liste officielle issue de l'API gouvernementale) :\n"
                . $communeList . "\n\n"
                . "RÈGLE ABSOLUE — Orthographe des villes :\n"
                . "Quand le vocal mentionne une commune sous une forme phonétique ou approximative "
                . "(erreur de transcription Whisper), tu DOIS chercher dans la liste ci-dessus la correspondance "
                . "la plus plausible et utiliser UNIQUEMENT le nom officiel exact.\n"
                . "Exemples de correction attendus :\n"
                . "- 'saint-néomai' ou 'saint néomé' → Sainte-Néomaye\n"
                . "- 'aifre' → Aiffres\n"
                . "- 'chiré' → Échiré\n"
                . "N'écris JAMAIS une orthographe phonétique ou inventée d'une ville. "
                . "Si aucune commune proche ne correspond, utilise {$city} par défaut.";
        } else {
            $str .= "RÈGLE ABSOLUE — Orthographe des villes :\n"
                . "N'écris JAMAIS une orthographe phonétique ou inventée d'une ville. "
                . "Utilise toujours le nom officiel français exact tel qu'il apparaît "
                . "sur les panneaux routiers ou sur La Poste.";
        }

        return $str;
    }

    /**
     * Returns a final reminder about city name spelling, placed at the very end of the prompt
     * so the LLM sees it last (highest recency weight). Repeats the core rule concisely.
     */
    private function citySpellingReminder(array $options): string
    {
        $nearbyCities = $options['company_nearby'] ?? [];
        $baseCity     = $options['company_city']   ?? null;

        if (empty($nearbyCities) && !$baseCity) {
            return '';
        }

        $reminder = "\n- ORTHOGRAPHE DES VILLES (priorité maximale) : Si la transcription contient un nom de "
            . "commune mal orthographié ou phonétique (erreur Whisper), corrige-le impérativement avec le nom officiel exact.";

        if (!empty($nearbyCities)) {
            // Show a compact sample of nearby city names (without postal codes) to make matching easier
            $names = array_map(fn($c) => preg_replace('/\s*\(\d+\)$/', '', $c), array_slice($nearbyCities, 0, 40));
            $reminder .= " Communes officielles du secteur : " . implode(', ', $names) . ".";
        }

        if ($baseCity) {
            $reminder .= " Si aucune commune proche ne correspond, utilise « {$baseCity} » par défaut.";
        }

        $reminder .= " Ne jamais écrire une orthographe phonétique dans le post final.\n";

        return $reminder;
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
