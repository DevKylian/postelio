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
        $prompt    = $this->getPromptForTemplate($template);
        $maxTokens = in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique']) ? 400 : 100;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => "Voici le contenu à transformer : " . $transcript],
            ],
            'max_tokens' => $maxTokens,
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

            'quality_focus' => "Tu es un expert en marketing pour artisans. À partir de la description vocale, rédige une courte légende Instagram (120 caractères max) qui explique pourquoi ce matériau ou matériel garantit un meilleur résultat ou une meilleure durabilité. Ton percutant, expert. Ne réponds que par la légende formatée.",

            'chantier_detail' => "Tu es un expert en marketing pour artisans. À partir de la description vocale d'un chantier, rédige une légende Instagram complète (300 caractères max) qui raconte l'histoire du projet, les contraintes rencontrées et la solution apportée. Style narratif et professionnel. Termine par 2-3 hashtags pertinents. Ne réponds que par la légende.",

            'step_by_step' => "Tu es un expert en marketing pour artisans. À partir du vocal de l'artisan décrivant son processus de travail, structure une légende Instagram sous forme de liste ordonnée (Étape 1, Étape 2, etc.) de 3 à 5 étapes. Chaque étape : 1 ligne max. Termine par un appel à l'action court. Ne réponds que par la légende structurée.",

            'guide_pedagogique' => "Tu es un expert en marketing pour artisans. À partir du vocal, rédige une légende Instagram éducative (300 caractères max) qui apporte de la valeur au lecteur et positionne l'artisan comme expert conseil. Commence par une accroche question, développe 2-3 bénéfices clés, termine par un CTA. Ne réponds que par la légende.",

            default => "Tu es un rédacteur web pro. Transforme ce texte en une légende courte (150 chars max) pour un post Instagram. Ne réponds que par la phrase formatée.",
        };
    }
}
