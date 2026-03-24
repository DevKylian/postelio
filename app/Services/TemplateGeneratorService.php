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
            'single'   => "\n\nIMPORTANT — Ce modèle utilise UNE photo fournie par l'artisan comme FOND d'image. La photo occupe tout l'espace visuel. Un bandeau de marque est affiché en bas (logo + nom société).",
            'multi'    => "\n\nIMPORTANT — Ce modèle utilise PLUSIEURS photos (2 à 4) qui doivent être affichées sur UN SEUL slide (ex: grille, avant/après). Tu DOIS impérativement utiliser le 'layout_type': 'dynamic' pour créer une mise en page qui intègre ces images (via {{ \$beforeImage }}, {{ \$afterImage }} ou {{ \$image }}).",
            'carousel' => "\n\nIMPORTANT — Ce modèle utilise PLUSIEURS photos (2 à 5) fournies par l'artisan pour créer un carrousel Instagram. Chaque photo est affichée plein cadre sur son propre slide.",
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
- 'standard'   → Titre à gauche, texte dessous (classique)
- 'centered'   → Tout est centré verticalement et horizontalement (impactant)
- 'quote'      → Texte entre de grosses guillemets décoratives (apostrophes)
- 'bold_title' → En-tête de couleur très large avec titre géant, texte dessous
- 'split'      → Moitié haute colorée, moitié basse claire
- 'minimal'    → Très épuré, fond blanc, texte aéré
- 'dynamic'    → LIBERTÉ TOTALE. Choisis ce mode si les autres ne conviennent pas ou si l'utilisateur demande un design spécifique. Tu dois alors générer le code HTML dans 'dynamic_content'.";

        $dynamicGuidance = "Si layout_type est 'dynamic', tu DOIS fournir 'dynamic_content' :
- Utilise du HTML avec Tailwind CSS.
- Variables disponibles (à injecter en Blade) : {{ \$brandColor }}, {{ \$displayText }}, {{ \$name }}, {{ \$currentStyle['text'] }}, {{ \$currentStyle['title'] }}, {{ \$companyLabel }}, {{ \$logoUrl }}, {{ \$beforeImage }}, {{ \$afterImage }}, {{ \$image }}.
- Exemple : \"<div class='flex-1 flex flex-col justify-center p-8'><h2 class='text-4xl font-black mb-4' style='color: {{ \$brandColor }}'>{{ \$name }}</h2><p class='text-lg' style='color: {{ \$currentStyle['text'] }}'>{{ \$displayText }}</p></div>\"
- Pour un Avant/Après : Utilise {{ \$beforeImage }} et {{ \$afterImage }} dans des balises <img />.
";

        $systemPrompt = "Tu es un expert créatif en marketing Instagram pour artisans et PME.
Ta mission : créer un modèle de post Instagram personnalisé, original et adapté EXACTEMENT à la demande.{$photoContext}

IMPORTANT — Innove dans le format : structure narrative, accroche, longueur, ton et style visuel doivent être adaptés spécifiquement à cette demande. Ne reproduis pas un format générique standard. Chaque modèle doit avoir une identité propre et originale.

{$styleGuidance}

{$layoutGuidance}

{$dynamicGuidance}

Retourne UNIQUEMENT un JSON avec ces champs :
- name: Nom court du modèle
- description: Description courte du modèle (max 80 caractères)
- ai_prompt: Prompt système complet en français (150-300 mots). Doit inclure les règles anti-hallucination.
- visual_style: 'dark_branded', 'light_branded', 'brand_color'
- layout_type: 'standard', 'centered', 'quote', 'bold_title', 'split', 'minimal', 'dynamic'
- dynamic_content: string (HTML/Tailwind si layout_type est 'dynamic', sinon null)
- {$hasImageTextRule}";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Demande de modèle : {$userRequest}"],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 1.1,
            'max_tokens' => 1200,
        ]);

        $data = json_decode($response->choices[0]->message->content, true);

        $validStyles  = ['dark_branded', 'light_branded', 'brand_color'];
        $validLayouts = ['standard', 'centered', 'quote', 'bold_title', 'split', 'minimal', 'dynamic'];

        return [
            'name'           => trim($data['name'] ?? 'Modèle Personnalisé'),
            'description'    => trim($data['description'] ?? ''),
            'ai_prompt'      => trim($data['ai_prompt'] ?? ''),
            'visual_style'   => in_array($data['visual_style'] ?? '', $validStyles) ? $data['visual_style'] : 'dark_branded',
            'layout_type'    => in_array($data['layout_type'] ?? '', $validLayouts) ? $data['layout_type'] : 'standard',
            'has_image_text' => (bool) ($data['has_image_text'] ?? false),
            'dynamic_content'=> $data['dynamic_content'] ?? null,
        ];
    }

    /**
     * Refine an existing template based on user feedback.
     */
    public function refine(array $currentTemplate, string $adjustmentRequest, string $trade = ''): array
    {
        $styleGuidance = "Pour visual_style, choisis selon la nature du contenu :
- 'dark_branded' → contenu premium, professionnel, soigné
- 'light_branded' → contenu clair, pédagogique, accessible
- 'brand_color' → contenu fort, impactant, promotionnel";

        $layoutGuidance = "Pour layout_type, choisis la mise en page qui correspond le mieux au concept :
- 'standard'   → Titre à gauche, texte dessous (classique)
- 'centered'   → Tout est centré verticalement et horizontalement (impactant)
- 'quote'      → Texte entre de grosses guillemets décoratives (apostrophes)
- 'bold_title' → En-tête de couleur très large avec titre géant, texte dessous
- 'split'      → Moitié haute colorée, moitié basse claire
- 'minimal'    → Très épuré, fond blanc, texte aéré
- 'dynamic'    → LIBERTÉ TOTALE. Tu DOIS utiliser ce mode si l'utilisateur demande de BOUGER, REPOSITIONNER, REDIMENSIONNER ou AJOUTER des éléments visuels précis. C'est le seul mode qui permet de modifier la structure HTML.";

        $dynamicGuidance = "Si layout_type est 'dynamic' ou si l'utilisateur demande un changement structurel (ex: 'déplace le titre', 'mets le texte en haut') :
1. Tu DOIS générer le code HTML complet dans 'dynamic_content'.
2. Utilise Tailwind CSS. Le canvas est carré.
3. Variables Blade : {{ \$brandColor }}, {{ \$displayText }}, {{ \$name }}, {{ \$currentStyle['text'] }}, {{ \$currentStyle['title'] }}, {{ \$companyLabel }}, {{ \$logoUrl }}, {{ \$beforeImage }}, {{ \$afterImage }}, {{ \$image }}.
4. Design Premium : Utilise des marges (p-8 ou p-10), des tailles de police impactantes (text-3xl pour les titres, text-xl pour le texte).
5. Structure conseillée :
   \"<div class='flex-1 flex flex-col p-10 justify-between h-full w-full'>
        <div class='[POSITION_DEMANDÉE]'>
            <h2 class='font-black uppercase tracking-tight' style='color: {{ \$currentStyle['title'] }}; font-size: 1.8rem;'>{{ \$name }}</h2>
        </div>
        <div class='[POSITION_DEMANDÉE]'>
            <p class='font-bold leading-relaxed' style='color: {{ \$currentStyle['text'] }}; font-size: 1.2rem;'>{{ \$displayText }}</p>
        </div>
    </div>\"";

        $layoutRefs = "RÉFÉRENCES DES MODÈLES STANDARDS (pour base de modification en mode 'dynamic') :
- 'standard' structure :
  \"<div class='absolute left-0 top-0 bottom-0 w-1.5' style='background-color: {{ \$brandColor }}'></div>
   <div class='absolute inset-0 flex flex-col justify-between px-10 py-10'>
     <div>
       <h2 class='font-black uppercase leading-none mb-4' style='color: {{ \$currentStyle['title'] }}; font-size: 1.4rem;'>{{ \$name }}</h2>
       <div class='w-8 h-0.5 mb-6 rounded-full' style='background-color: {{ \$brandColor }}; opacity: 0.6;'></div>
       <p class='font-bold' style='color: {{ \$currentStyle['text'] }}; font-size: 1rem;'>{{ \$displayText }}</p>
     </div>
   </div>\"
- 'centered' structure :
  \"<div class='absolute inset-0 flex flex-col items-center justify-center px-10 text-center'>
     <div class='w-12 h-1 mb-6 rounded-full' style='background-color: {{ \$brandColor }}'></div>
     <h2 class='font-black uppercase mb-4' style='color: {{ \$currentStyle['title'] }}; font-size: 1.5rem;'>{{ \$name }}</h2>
     <p style='color: {{ \$currentStyle['text'] }}; font-size: 0.9rem;'>{{ \$displayText }}</p>
   </div>\"";
        // CONTINUITY ENHANCEMENT: If the current template is NOT dynamic, 
        // we provide the AI with the EXACT HTML it would need to replicate the current look.
        $effectiveDynamicContent = $currentTemplate['dynamic_content'] ?? null;
        if (($currentTemplate['layout_type'] ?? 'standard') !== 'dynamic') {
            $effectiveDynamicContent = $this->getEquivalentHtml($currentTemplate['layout_type'] ?? 'standard');
        }

        $systemPrompt = "Tu es un expert créatif en marketing Instagram. Ta mission : AJUSTER un modèle de post existant.
RÈGLE D'OR : Si l'utilisateur demande un changement VISUEL ou de POSITION (ex: 'déplace', 'monte', 'descend', 'agrandis'), tu DOIS impérativement passer en 'layout_type': 'dynamic'.

PRIORITÉ À LA CONTINUITÉ : L'utilisateur déteste quand tu changes tout le design sans raison.
1. Si le modèle actuel est 'standard', 'centered', etc., et que tu passes en 'dynamic', tu DOIS utiliser le 'Contenu dynamique actuel' fourni ci-dessous (qui est la réplique exacte du modèle standard) et ne modifier que la partie demandée.
2. Si le modèle est déjà 'dynamic', modifie LÉGÈREMENT le code existant fourni plus bas.
3. Ne change jamais le style visuel (couleurs) sauf demande expresse.

Modèle actuel :
- Nom : {$currentTemplate['name']}
- Type actuel : {$currentTemplate['layout_type']}
- Style visuel : {$currentTemplate['visual_style']}
- Contenu dynamique actuel (HTML) : " . ($effectiveDynamicContent ?? 'Aucun') . "
- Prompt actuel : {$currentTemplate['ai_prompt']}

{$layoutRefs}
{$styleGuidance}
{$layoutGuidance}
{$dynamicGuidance}

Retourne UNIQUEMENT un JSON avec tous les champs :
- name: Nom ajusté
- description: Description ajustée (max 80 car.)
- ai_prompt: Prompt système ajusté
- visual_style: 'dark_branded' | 'light_branded' | 'brand_color'
- layout_type: 'standard' | 'centered' | 'quote' | 'bold_title' | 'split' | 'minimal' | 'dynamic'
- photo_mode: 'none' | 'single' | 'multi' | 'carousel'
- has_image_text: boolean
- dynamic_content: string (HTML/Tailwind obligatoire si layout_type est 'dynamic')";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "AJUSTEMENT DEMANDÉ : {$adjustmentRequest}"],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.7,
        ]);

        $data = json_decode($response->choices[0]->message->content, true);

        $validStyles  = ['dark_branded', 'light_branded', 'brand_color'];
        $validLayouts = ['standard', 'centered', 'quote', 'bold_title', 'split', 'minimal', 'dynamic'];
        $validPhotos  = ['none', 'single', 'multi', 'carousel'];

        return [
            'name'           => trim($data['name'] ?? $currentTemplate['name']),
            'description'    => trim($data['description'] ?? $currentTemplate['description']),
            'ai_prompt'      => trim($data['ai_prompt'] ?? $currentTemplate['ai_prompt']),
            'visual_style'   => in_array($data['visual_style'] ?? '', $validStyles) ? $data['visual_style'] : $currentTemplate['visual_style'],
            'layout_type'    => in_array($data['layout_type'] ?? '', $validLayouts) ? $data['layout_type'] : $currentTemplate['layout_type'],
            'photo_mode'     => in_array($data['photo_mode'] ?? '', $validPhotos) ? $data['photo_mode'] : ($currentTemplate['photo_mode'] ?? 'none'),
            'has_image_text' => (bool) ($data['has_image_text'] ?? $currentTemplate['has_image_text']),
            'dynamic_content'=> $data['dynamic_content'] ?? ($currentTemplate['dynamic_content'] ?? null),
        ];
    }

    /**
     * Get the HTML equivalent of a standard layout to provide as a base for dynamic refinement.
     */
    protected function getEquivalentHtml(string $layoutType): ?string
    {
        return match ($layoutType) {
            'standard' =>
                "<div class='absolute left-0 top-0 bottom-0 w-1.5' style='background-color: {{ \$brandColor }}'></div>
                <div class='absolute inset-0 flex flex-col justify-between px-10 py-10'>
                    <div>
                        <h2 class='font-black uppercase leading-none mb-4' style='color: {{ \$currentStyle['title'] }}; font-size: 1.4rem;'>{{ \$name }}</h2>
                        <div class='w-8 h-0.5 mb-6 rounded-full' style='background-color: {{ \$brandColor }}; opacity: 0.6;'></div>
                        <p class='font-bold' style='color: {{ \$currentStyle['text'] }}; font-size: 1rem;'>{{ \$displayText }}</p>
                    </div>
                </div>",

            'centered' =>
                "<div class='absolute inset-0 flex flex-col items-center justify-center px-10 text-center'>
                    <div class='w-12 h-1 mb-6 rounded-full' style='background-color: {{ \$brandColor }}'></div>
                    <h2 class='font-black uppercase mb-4' style='color: {{ \$currentStyle['title'] }}; font-size: 1.5rem;'>{{ \$name }}</h2>
                    <p style='color: {{ \$currentStyle['text'] }}; font-size: 0.9rem;'>{{ \$displayText }}</p>
                </div>",

            'quote' =>
                "<div class='absolute inset-x-8 top-12'>
                    <div class='flex items-center gap-3'>
                        <div class='w-8 h-0.5' style='background-color: {{ \$brandColor }}'></div>
                        <span class='text-[10px] font-bold uppercase tracking-widest' style='color: {{ \$brandColor }}'>{{ \$name }}</span>
                    </div>
                </div>
                <div class='absolute inset-0 flex flex-col items-center justify-center px-12 text-center pt-8'>
                    <span class='text-6xl font-serif opacity-30 leading-none mb-2' style='color: {{ \$brandColor }}'>\"</span>
                    <p class='font-bold italic leading-relaxed' style='color: {{ \$currentStyle['text'] }}; font-size: 1.1rem;'>{{ \$displayText }}</p>
                    <span class='text-6xl font-serif opacity-30 leading-none mt-4 self-end' style='color: {{ \$brandColor }}'>\"</span>
                </div>",

            default => null,
        };
    }
}
