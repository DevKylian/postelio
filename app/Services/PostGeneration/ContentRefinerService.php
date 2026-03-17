<?php

namespace App\Services\PostGeneration;

use OpenAI\Laravel\Facades\OpenAI;

class ContentRefinerService
{
    /**
     * Refine raw transcription into a percutant post content based on template.
     */
    public function refine(string $transcript, string $template): string
    {
        $prompt = $this->getPromptForTemplate($template);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => "Voici le contenu à transformer : " . $transcript],
            ],
            'max_tokens' => 100,
            'temperature' => 0.7,
        ]);

        return trim($response->choices[0]->message->content, '" ');
    }

    private function getPromptForTemplate(string $template): string
    {
        return match ($template) {
            'review' => "Tu es un expert en marketing pour artisans. Ta mission est d'extraire le positif d'un avis client et de le transformer en une phrase courte, percutante et émotionnelle de 100 caractères maximum. Ne réponds que par la phrase formatée.",
            'project' => "Tu es un expert en marketing pour artisans. Ta mission est de résumer un projet de chantier en une phrase courte et inspirante de 120 caractères maximum. Ne réponds que par la phrase formatée.",
            'faq' => "Tu es un expert en marketing pour artisans. Ta mission est d'extraire une question pertinente posée par un client ou un conseil expert à partir du texte, et de la transformer en une question courte (style FAQ) de 100 caractères maximum. Ne réponds que par la question formatée avec un point d'interrogation.",
            'lifestyle' => "Tu es un expert en marketing pour artisans. Ta mission est de créer une légende courte, inspirante et 'lifestyle' qui met en avant l'ambiance ou la passion de ton métier à partir de l'audio de 120 caractères maximum. Ne réponds que par la phrase formatée.",
            default => "Tu es un rédacteur web pro. Transforme ce texte en une légende courte (150 chars max) pour un post Instagram. Ne réponds que par la phrase formatée.",
        };
    }
}
