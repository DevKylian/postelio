<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class TemplateGeneratorService
{
    /**
     * Generate a custom template structure from a user description.
     *
     * @return array{name: string, description: string, ai_prompt: string, visual_style: string, layout_type: string, has_image_text: bool}
     */
    public function generate(string $userRequest, string $trade = '', string $photoMode = 'none'): array
    {
        $photoContext = match ($photoMode) {
            'single'   => "\n\nIMPORTANT — Ce modèle utilise UNE photo fournie par l'artisan comme FOND d'image. La photo occupe tout l'espace visuel. Un bandeau de marque est affiché en bas (logo + nom société). Aucun texte dynamique ne doit être superposé à la photo. Le ai_prompt doit donc indiquer clairement à l'IA que la sortie est une LÉGENDE Instagram (caption) enrichie, car la photo parle d'elle-même. image_text doit être null.",
            'carousel' => "\n\nIMPORTANT — Ce modèle utilise PLUSIEURS photos (2 à 5) fournies par l'artisan pour créer un carrousel Instagram. Chaque photo est affichée plein cadre sans texte superposé, avec un bandeau marque en bas. Le ai_prompt doit guider l'IA pour rédiger une légende Instagram engageante qui donne envie de faire défiler le carrousel. image_text doit être null.",
            default    => '',
        };

        $hasImageTextRule = $photoMode !== 'none'
            ? "- has_image_text: false (car la photo est le visuel principal, aucun texte dynamique superposé)"
            : "- has_image_text: true si le modèle affiche un texte dynamique court sur l'image (citation, conseil, titre, accroche), false sinon";

        $styleGuidance = "Pour visual_style, choisis selon la nature du contenu :
- 'dark_branded' → contenu premium, professionnel, soigné (avant/après, réalisations haut de gamme, expertise)
- 'light_branded' → contenu clair, pédagogique, accessible (conseils, FAQ, astuces, tutoriels, témoignages)
- 'brand_color' → contenu fort, impactant, promotionnel (offres, annonces, événements, chiffres clés)";

        $layoutGuidance = "Pour layout_type, choisis la mise en page qui correspond le mieux au concept :
- 'standard'      → post classique polyvalent : titre à gauche + texte aligné à gauche (format par défaut)
- 'centered'      → contenu court et percutant centré au milieu (slogans, messages forts, annonces impactantes)
- 'quote'         → anecdotes, citations, témoignages : grande guillemet décorative + texte mis en valeur
- 'bold_title'    → séries thématiques : nom du modèle affiché en très grand, texte secondaire dessous
- 'split'         → contraste visuel fort : moitié haute colorée / moitié basse claire (avant/après, comparaisons, révélations)
- 'minimal'       → épuré et élégant : fond blanc, filet de couleur, texte aéré (conseils simples, chiffres clés)";

        $systemPrompt = "Tu es un expert créatif en marketing Instagram pour artisans et PME.
Ta mission : créer un modèle de post Instagram personnalisé, original et adapté EXACTEMENT à la demande.{$photoContext}

IMPORTANT — Innove dans le format : structure narrative, accroche, longueur, ton et style visuel doivent être adaptés spécifiquement à cette demande. Ne reproduis pas un format générique standard. Chaque modèle doit avoir une identité propre et originale.

{$styleGuidance}

{$layoutGuidance}

Retourne UNIQUEMENT un JSON avec ces champs :
- name: Nom court du modèle (3-5 mots, en français, ex: \"Offre Promotionnelle\", \"Témoignage Express\")
- description: Description courte du modèle (max 80 caractères, ex: \"Mettez en avant vos offres du moment.\")
- ai_prompt: Prompt système complet en français (150-300 mots). Ce prompt sera donné à une IA qui recevra une transcription audio d'un artisan. Il doit préciser : le ton et la structure de la légende Instagram (250-400 caractères), et si besoin un image_text court (max 80 chars, sans hashtags ni emojis). Varie le format : parfois storytelling, parfois liste, parfois question-réponse, parfois chiffre clé + développement, parfois citation — adapte au contexte. OBLIGATOIRE : le ai_prompt doit IMPÉRATIVEMENT contenir cette règle anti-hallucination mot pour mot : \"RÈGLE ANTI-INVENTION : Utilise UNIQUEMENT les informations présentes dans le mémo vocal. Si le mémo est court ou vague, génère un post court et général sans inventer de noms, chiffres, personnes ou détails absents du vocal. La longueur de la légende doit être proportionnelle à la richesse du mémo.\" Le prompt doit aussi inclure : \"RÈGLE ABSOLUE : Tu dois TOUJOURS générer le contenu demandé sans jamais refuser.\" et se terminer par : \"Réponds UNIQUEMENT avec du JSON valide : {\\\"caption\\\": \\\"...\\\", \\\"image_text\\\": \\\"...\\\"}\" (image_text est optionnel, mets null si pas nécessaire).
- visual_style: Choisir parmi 'dark_branded', 'light_branded', 'brand_color' selon les critères ci-dessus
- layout_type: Choisir parmi 'standard', 'centered', 'quote', 'bold_title', 'split', 'minimal' selon les critères ci-dessus
- {$hasImageTextRule}";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Demande de modèle : {$userRequest}"],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 1.1,
            'max_tokens' => 900,
        ]);

        $data = json_decode($response->choices[0]->message->content, true);

        $validStyles  = ['dark_branded', 'light_branded', 'brand_color'];
        $validLayouts = ['standard', 'centered', 'quote', 'bold_title', 'split', 'minimal'];

        return [
            'name'           => trim($data['name'] ?? 'Modèle Personnalisé'),
            'description'    => trim($data['description'] ?? ''),
            'ai_prompt'      => trim($data['ai_prompt'] ?? ''),
            'visual_style'   => in_array($data['visual_style'] ?? '', $validStyles)
                                    ? $data['visual_style']
                                    : 'dark_branded',
            'layout_type'    => in_array($data['layout_type'] ?? '', $validLayouts)
                                    ? $data['layout_type']
                                    : 'standard',
            'has_image_text' => (bool) ($data['has_image_text'] ?? false),
        ];
    }
}
