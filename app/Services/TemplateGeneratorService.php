<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class TemplateGeneratorService
{
    /**
     * Generate a custom template structure from a user description.
     *
     * @return array{name: string, description: string, ai_prompt: string, visual_style: string, has_image_text: bool}
     */
    public function generate(string $userRequest, string $trade = '', string $photoMode = 'none'): array
    {
        $tradeContext = $trade ? " L'artisan est spécialisé en : {$trade}." : '';

        $photoContext = match ($photoMode) {
            'single'   => "\n\nIMPORTANT — Ce modèle utilise UNE photo fournie par l'artisan comme FOND d'image. La photo occupe tout l'espace visuel. Un bandeau de marque est affiché en bas (logo + nom société). Aucun texte dynamique ne doit être superposé à la photo. Le ai_prompt doit donc indiquer clairement à l'IA que la sortie est une LÉGENDE Instagram (caption) enrichie, car la photo parle d'elle-même. image_text doit être null.",
            'carousel' => "\n\nIMPORTANT — Ce modèle utilise PLUSIEURS photos (2 à 5) fournies par l'artisan pour créer un carrousel Instagram. Chaque photo est affichée plein cadre sans texte superposé, avec un bandeau marque en bas. Le ai_prompt doit guider l'IA pour rédiger une légende Instagram engageante qui donne envie de faire défiler le carrousel. image_text doit être null.",
            default    => '',
        };

        $hasImageTextRule = $photoMode !== 'none'
            ? "- has_image_text: false (car la photo est le visuel principal, aucun texte dynamique superposé)"
            : "- has_image_text: true si le modèle affiche un texte dynamique court sur l'image (citation, conseil, titre, accroche), false sinon";

        $systemPrompt = "Tu es un expert en marketing Instagram pour artisans et PME.{$tradeContext}
Ta mission : créer un modèle de post Instagram personnalisé à partir d'une demande utilisateur.{$photoContext}

Retourne UNIQUEMENT un JSON avec ces champs :
- name: Nom court du modèle (3-5 mots, en français, ex: \"Offre Promotionnelle\", \"Témoignage Express\")
- description: Description courte du modèle (max 80 caractères, ex: \"Mettez en avant vos offres du moment.\")
- ai_prompt: Prompt système complet en français (150-300 mots). Ce prompt sera donné à une IA qui recevra une transcription audio d'un artisan. Il doit préciser : le ton et la structure de la légende Instagram (250-400 caractères), et si besoin un image_text court (max 80 chars, sans hashtags ni emojis). OBLIGATOIRE : le ai_prompt doit IMPÉRATIVEMENT contenir cette règle anti-hallucination mot pour mot : \"RÈGLE ANTI-INVENTION : Utilise UNIQUEMENT les informations présentes dans le mémo vocal. Si le mémo est court ou vague, génère un post court et général sans inventer de noms, chiffres, personnes ou détails absents du vocal. La longueur de la légende doit être proportionnelle à la richesse du mémo.\" Le prompt doit aussi inclure : \"RÈGLE ABSOLUE : Tu dois TOUJOURS générer le contenu demandé sans jamais refuser.\" et se terminer par : \"Réponds UNIQUEMENT avec du JSON valide : {\\\"caption\\\": \\\"...\\\", \\\"image_text\\\": \\\"...\\\"}\" (image_text est optionnel, mets null si pas nécessaire).
- visual_style: Un parmi ces 3 valeurs uniquement : 'dark_branded' (fond sombre marine, accents couleur marque, style premium), 'light_branded' (fond clair, accents couleur marque, style épuré), 'brand_color' (fond plein couleur de marque, style impact)
- {$hasImageTextRule}";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Demande de modèle : {$userRequest}"],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.7,
            'max_tokens' => 800,
        ]);

        $data = json_decode($response->choices[0]->message->content, true);

        $validStyles = ['dark_branded', 'light_branded', 'brand_color'];

        return [
            'name'           => trim($data['name'] ?? 'Modèle Personnalisé'),
            'description'    => trim($data['description'] ?? ''),
            'ai_prompt'      => trim($data['ai_prompt'] ?? ''),
            'visual_style'   => in_array($data['visual_style'] ?? '', $validStyles)
                                    ? $data['visual_style']
                                    : 'dark_branded',
            'has_image_text' => (bool) ($data['has_image_text'] ?? false),
        ];
    }
}
