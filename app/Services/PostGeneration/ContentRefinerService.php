<?php

namespace App\Services\PostGeneration;

use OpenAI\Laravel\Facades\OpenAI;

class ContentRefinerService
{
    /**
     * Refine raw transcription into a percutant post content based on template.
     *
     * @param array $options  template_options from the Post model
     */
    public function refine(string $transcript, string $template, int $photoCount = 0, array $options = []): string
    {
        $prompt    = $this->getPromptForTemplate($template, $photoCount, $options);
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

    private function getPromptForTemplate(string $template, int $photoCount = 0, array $options = []): string
    {
        $tone            = $options['tone'] ?? 'professionnel';
        $includeHashtags = $options['includeHashtags'] ?? true;
        $customNote      = $options['customNote'] ?? null;

        $toneInstruction = $this->toneInstruction($tone);
        $hashtagInstruction = $includeHashtags
            ? "Ajoute 3 à 5 hashtags pertinents à la fin du texte."
            : "N'ajoute aucun hashtag.";

        $contextHints = $this->buildContextHints($template, $options);

        $jsonCarouselInstruction = "Tu dois répondre UNIQUEMENT par un objet JSON avec cette structure :
{
  \"caption\": \"La légende Instagram complète du post (300-500 caractères, style expert, hashtags à la fin)\",
  \"slides\": [\"Texte court pour le slide 1\", \"Texte court pour le slide 2\", ...]
}";

        $extraNote = $customNote ? "\nNote additionnelle de l'artisan : {$customNote}" : '';

        return match ($template) {
            'review' => "Tu es un expert en marketing pour artisans. {$toneInstruction} "
                . ($options['clientName'] ? "Le prénom du client est : {$options['clientName']}. " : "")
                . ($options['serviceType'] ? "Le service réalisé est : {$options['serviceType']}. " : "")
                . "Ta mission est d'extraire le positif d'un avis client et de le transformer en une phrase courte, percutante et émotionnelle de 100 caractères maximum. {$hashtagInstruction} Ne réponds que par la phrase formatée.{$extraNote}",

            'project' => "Tu es un expert en marketing pour artisans. {$toneInstruction} "
                . ($options['workCategory'] ? "Type de travaux : {$options['workCategory']}. " : "")
                . ($options['jobDuration'] ? "Durée du chantier : {$options['jobDuration']}. " : "")
                . "Ta mission est de résumer un projet de chantier en une phrase courte et inspirante de 120 caractères maximum. {$hashtagInstruction} Ne réponds que par la phrase formatée.{$extraNote}",

            'faq' => "Tu es un expert en marketing pour artisans. {$toneInstruction} "
                . ($options['faqCategory'] ? "Catégorie de la FAQ : {$options['faqCategory']}. " : "")
                . "Ta mission est d'extraire une question pertinente posée par un client ou un conseil expert et de la transformer en une question courte (style FAQ) de 100 caractères maximum. {$hashtagInstruction} Ne réponds que par la question formatée avec un point d'interrogation.{$extraNote}",

            'lifestyle' => "Tu es un expert en marketing pour artisans. {$toneInstruction} Ta mission est de créer une légende courte, inspirante et 'lifestyle' qui met en avant l'ambiance ou la passion du métier à partir de l'audio de 120 caractères maximum. {$hashtagInstruction} Ne réponds que par la phrase formatée.{$extraNote}",

            'quality_focus' => "Tu es un expert en marketing pour artisans. {$toneInstruction} À partir de la description vocale, rédige une courte légende Instagram (120 caractères max) qui explique pourquoi ce matériau ou matériel garantit un meilleur résultat ou une meilleure durabilité. {$hashtagInstruction} Ne réponds que par la légende formatée.{$extraNote}",

            'conseil_saisonnier' => "Tu es un expert en marketing pour artisans. {$toneInstruction} "
                . ($options['season'] ? "Saison ciblée : {$options['season']}. " : "")
                . "À partir du mémo vocal, rédige un conseil saisonnier pratique et utile en 1 phrase percutante de 120 caractères maximum. Commence par un verbe d'action concret (ex: 'Vérifiez', 'Anticipez', 'Pensez à', 'Préparez'). {$hashtagInstruction} Ne réponds que par la phrase formatée, sans guillemets.{$extraNote}",

            'erreur_a_eviter' => "Tu es un expert en marketing pour artisans. {$toneInstruction} À partir du mémo vocal, identifie une erreur fréquente et formule-la en une phrase courte et impactante de 110 caractères maximum. Style alerte directe (ex: 'Attendre que la fuite empire avant d'appeler.'). {$hashtagInstruction} Ne réponds que par la phrase formatée, sans guillemets.{$extraNote}",

            'chantier_detail' => "Tu es un expert en marketing pour artisans. {$toneInstruction} À partir de la description vocale d'un chantier, crée un post carrousel. Nous avons {$photoCount} photos. $jsonCarouselInstruction. Le premier slide est la couverture (titre accrocheur). Les slides suivants décrivent les détails du chantier correspondant aux photos. {$hashtagInstruction}{$extraNote}",

            'step_by_step' => "Tu es un expert en marketing pour artisans. {$toneInstruction} À partir du vocal décrivant un processus, crée un post carrousel 'étape par étape'. Nous avons {$photoCount} photos. $jsonCarouselInstruction. Le premier slide est le titre du process. Les slides suivants sont les étapes (Step 1, Step 2, etc.) correspondant aux photos. Maximum 60 caractères par slide. {$hashtagInstruction}{$extraNote}",

            'guide_pedagogique' => (function() use ($jsonCarouselInstruction, $toneInstruction, $hashtagInstruction, $options, $extraNote) {
                $targetSlides = isset($options['targetSlides']) ? (int)$options['targetSlides'] : 5;
                $slidesRange  = max(3, $targetSlides - 1) . ' à ' . min(8, $targetSlides + 1);
                return "Tu es un expert en marketing pour artisans. {$toneInstruction} À partir du vocal, crée un post carrousel éducatif. $jsonCarouselInstruction. Le premier slide est une question/accroche forte. Les slides suivants ({$slidesRange} slides) développent les conseils. Le dernier slide doit être un appel à l'action. Maximum 100 caractères par slide. {$hashtagInstruction}{$extraNote}";
            })(),

            default => "Tu es un rédacteur web pro. {$toneInstruction} Transforme ce texte en une légende courte (150 chars max) pour un post Instagram. {$hashtagInstruction} Ne réponds que par la phrase formatée.{$extraNote}",
        };
    }

    private function toneInstruction(string $tone): string
    {
        return match ($tone) {
            'chaleureux' => "Ton chaleureux, humain et proche. Parle comme un artisan passionné qui s'adresse à ses clients avec bienveillance.",
            'expert'     => "Ton expert et technique. Utilise un vocabulaire du métier précis qui inspire confiance et crédibilité.",
            'dynamique'  => "Ton dynamique, vendeur et enthousiaste. Mots percutants, énergie positive, donne envie d'agir.",
            default      => "Ton professionnel, sérieux et direct. Sobre, efficace, valorise le savoir-faire.",
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
