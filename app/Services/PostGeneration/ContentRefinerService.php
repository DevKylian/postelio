<?php

namespace App\Services\PostGeneration;

use OpenAI\Laravel\Facades\OpenAI;

class ContentRefinerService
{
    /**
     * Refine raw transcription into a percutant post content based on template.
     */
    public function refine(string $transcript, string $template, int $photoCount = 0): string
    {
        $prompt    = $this->getPromptForTemplate($template, $photoCount);
        $maxTokens = in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique']) ? 1200 : 150;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => "Voici le contenu à transformer : " . $transcript],
            ],
            'max_tokens' => $maxTokens,
            'temperature' => 0.7,
            'response_format' => in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique']) 
                ? ['type' => 'json_object'] 
                : null,
        ]);

        $content = trim($response->choices[0]->message->content, '" ');

        // Clean common AI prefixes
        $prefixes = ['caption:', 'légende:', 'post:', 'titre:'];
        foreach ($prefixes as $prefix) {
            if (str_starts_with(mb_strtolower($content), $prefix)) {
                $content = trim(mb_substr($content, mb_strlen($prefix)));
            }
        }

        return $content;
    }

    private function getPromptForTemplate(string $template, int $photoCount = 0): string
    {
        $jsonCarouselInstruction = "Tu dois répondre UNIQUEMENT par un objet JSON avec cette structure :
{
  \"caption\": \"La légende Instagram complète du post (300-500 caractères, style expert, hashtags à la fin)\",
  \"slides\": [\"Texte court pour le slide 1\", \"Texte court pour le slide 2\", ...]
}";

        return match ($template) {
            'review' => "Tu es un expert en marketing pour artisans. Ta mission est d'extraire le positif d'un avis client et de le transformer en une phrase courte, percutante et émotionnelle de 100 caractères maximum. Ne réponds que par la phrase formatée.",

            'project' => "Tu es un expert en marketing pour artisans. Ta mission est de résumer un projet de chantier en une phrase courte et inspirante de 120 caractères maximum. Ne réponds que par la phrase formatée.",

            'faq' => "Tu es un expert en marketing pour artisans. Ta mission est d'extraire une question pertinente posée par un client ou un conseil expert à partir du texte, et de la transformer en une question courte (style FAQ) de 100 caractères maximum. Ne réponds que par la question formatée avec un point d'interrogation.",

            'lifestyle' => "Tu es un expert en marketing pour artisans. Ta mission est de créer une légende courte, inspirante et 'lifestyle' qui met en avant l'ambiance ou la passion de ton métier à partir de l'audio de 120 caractères maximum. Ne réponds que par la phrase formatée.",

            'quality_focus' => "Tu es un expert en marketing pour artisans. À partir de la description vocale, rédige une courte légende Instagram (120 caractères max) qui explique pourquoi ce matériau ou matériel garantit un meilleur resultats ou une meilleure durabilité. Ton percutant, expert. Ne réponds que par la légende formatée.",

            'conseil_saisonnier' => "Tu es un expert en marketing pour artisans. À partir du mémo vocal, rédige un conseil saisonnier pratique et utile pour les clients en 1 phrase percutante de 120 caractères maximum. Commence par un verbe d'action concret (ex: 'Vérifiez', 'Anticipez', 'Pensez à', 'Préparez'). Ton expert, bienveillant et direct. Ne réponds que par la phrase formatée, sans guillemets.",

            'erreur_a_eviter' => "Tu es un expert en marketing pour artisans. À partir du mémo vocal, identifie une erreur fréquente commise par les clients ou propriétaires et formule-la en une phrase courte et impactante de 110 caractères maximum. Style alerte directe (ex: 'Attendre que la fuite empire avant d'appeler.', 'Peindre sur une surface humide.'). Ton alarmant mais utile. Ne réponds que par la phrase formatée, sans guillemets.",

            'chantier_detail' => "Tu es un expert en marketing pour artisans. À partir de la description vocale d'un chantier, crée un post carrousel. Nous avons {$photoCount} photos. $jsonCarouselInstruction. Le premier slide est la couverture (titre accrocheur). Les slides suivants décrivent les détails du chantier correspondant aux photos.",

            'step_by_step' => "Tu es un expert en marketing pour artisans. À partir du vocal décrivant un processus, crée un post carrousel 'étape par étape'. Nous avons {$photoCount} photos. $jsonCarouselInstruction. Le premier slide est le titre du process. Les slides suivants sont les étapes (Step 1, Step 2, etc.) correspondant aux photos. maximum 60 caractères par slide.",

            'guide_pedagogique' => "Tu es un expert en marketing pour artisans. À partir du vocal, crée un post carrousel éducatif. $jsonCarouselInstruction. Le premier slide est une question/accroche forte. Les slides suivants (4 à 7 slides) développent les conseils. Le dernier slide doit être un appel à l'action. maximum 100 caractères par slide.",

            default => "Tu es un rédacteur web pro. Transforme ce texte en une légende courte (150 chars max) pour un post Instagram. Pas d'emojis ni de formatage markdown (pas de **). Ne réponds que par la phrase formatée.",
        };
    }
}
