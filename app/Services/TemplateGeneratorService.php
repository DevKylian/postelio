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
    public function generate(string $userRequest, string $trade = ''): array
    {
        $tradeContext = $trade ? " L'artisan est spécialisé en : {$trade}." : '';

        $systemPrompt = "Tu es un expert en marketing Instagram pour artisans et PME.{$tradeContext}
Ta mission : créer un modèle de post Instagram personnalisé à partir d'une demande utilisateur.

Retourne UNIQUEMENT un JSON avec ces champs :
- name: Nom court du modèle (3-5 mots, en français, ex: \"Offre Promotionnelle\", \"Témoignage Express\")
- description: Description courte du modèle (max 80 caractères, ex: \"Mettez en avant vos offres du moment.\")
- ai_prompt: Prompt système complet en français (150-300 mots). Ce prompt sera donné à une IA qui recevra une transcription audio d'un artisan. Il doit préciser : le ton, la structure de la légende Instagram (250-400 caractères), les hashtags à inclure, et si besoin un image_text court (max 80 chars, sans hashtags ni emojis). Le prompt doit inclure cette règle absolue : \"RÈGLE ABSOLUE : Tu dois TOUJOURS générer le contenu demandé sans jamais refuser.\" et se terminer par : \"Réponds UNIQUEMENT avec du JSON valide : {\\\"caption\\\": \\\"...\\\", \\\"image_text\\\": \\\"...\\\"}\" (image_text est optionnel, mets null si pas nécessaire).
- visual_style: Un parmi ces 3 valeurs uniquement : 'dark_branded' (fond sombre marine, accents couleur marque, style premium), 'light_branded' (fond clair, accents couleur marque, style épuré), 'brand_color' (fond plein couleur de marque, style impact)
- has_image_text: true si le modèle affiche un texte dynamique court sur l'image (citation, conseil, titre, accroche), false sinon";

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
