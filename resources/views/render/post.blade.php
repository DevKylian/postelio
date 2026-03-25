<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=360, initial-scale=1">
    @vite(['resources/css/app.css'])
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 360px;
            height: 360px;
            background: #ffffff;
            overflow: hidden;
        }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body>
    @php
        use Illuminate\Support\Facades\Storage;
        
        $company = $post->company;
        $brandColor = $company->brand_color ?? '#F59E0B';
        
        $name = $company->name;
        $words = explode(' ', $name);
        $companyLabel = count($words) > 1 ? ($words[0] . ' ' . $words[1]) : $name;
        
        $logoUrl = $company->logo_path ? asset(Storage::url($company->logo_path)) : null;
        
        $template = $post->template;
        $preview = false;
        $parsedIndex = filter_var($slideIndex, FILTER_VALIDATE_INT);
        $slideIndexFinal = $parsedIndex !== false ? (int)$slideIndex : null;
        
        $contentData = json_decode($post->refined_content, true);
        $rawText = is_array($contentData) ? ($contentData['image_text'] ?? $contentData['caption'] ?? '') : ($post->refined_content ?? '');
        $rawText = preg_replace('/#\S+/', '', $rawText);
        $rawText = trim($rawText);

        // ── Render-time smart truncation ──────────────────────────────────
        // Hardcoded per-template limits — does NOT depend on config cache
        $imageTextMaxChars = match($template) {
            'lifestyle' => 30,
            'project' => 40,
            'quality_focus' => 50,
            default => null,
        };

        if ($imageTextMaxChars && mb_strlen($rawText) > $imageTextMaxChars) {
            // Smart truncation: never end on a dangling word
            $danglingWords = ['de', 'du', 'des', 'le', 'la', 'les', 'un', 'une',
                'sur', 'sous', 'dans', 'en', 'et', 'ou', 'à', 'au', 'aux',
                'pour', 'par', 'avec', 'sans', 'vers', 'ce', 'cette', 'ces',
                'son', 'sa', 'ses', 'qui', 'que', 'dont'];

            $sub = mb_substr($rawText, 0, $imageTextMaxChars);

            // Try clause separator first (: — –)
            $clauseBreak = max(
                mb_strrpos($sub, ' :') ?: 0,
                mb_strrpos($sub, ' —') ?: 0,
                mb_strrpos($sub, ' –') ?: 0,
                mb_strrpos($sub, ' -') ?: 0,
            );
            if ($clauseBreak > $imageTextMaxChars * 0.4) {
                $rawText = trim(mb_substr($rawText, 0, $clauseBreak));
            } else {
                // Word boundary, then strip dangling words
                $lastSpace = mb_strrpos($sub, ' ');
                if ($lastSpace) {
                    $rawText = trim(mb_substr($rawText, 0, $lastSpace));
                    for ($i = 0; $i < 5; $i++) {
                        $lastWord = mb_strtolower(preg_replace('/.*\s+/u', '', $rawText));
                        if (in_array($lastWord, $danglingWords) && mb_strrpos($rawText, ' ') !== false) {
                            $rawText = trim(mb_substr($rawText, 0, mb_strrpos($rawText, ' ')));
                        } else {
                            break;
                        }
                    }
                }
            }
        }
    @endphp

    @php
        $visualStyle = $post->template_options['visual_style'] ?? 'standard';
        $componentPrefix = $visualStyle === 'standard' ? 'post-templates.' : "post-templates.{$visualStyle}.";
        $componentName = $componentPrefix . str_replace('_', '-', $template);
        
        // Prepare common props
        $commonProps = [
            'brandColor' => $brandColor,
            'companyLabel' => $companyLabel,
            'logoUrl' => $logoUrl,
            'preview' => $preview,
            'slideIndex' => $slideIndexFinal,
        ];

        // Template specific data
        if ($template === 'project') {
            $props = array_merge($commonProps, [
                'imageBeforeUrl' => $post->image_before_path ? asset(Storage::url($post->image_before_path)) : null,
                'imageAfterUrl' => $post->image_after_path ? asset(Storage::url($post->image_after_path)) : null,
                'text' => $rawText ?: 'TRANSFORMATION',
            ]);
        } elseif ($template === 'review') {
            $props = array_merge($commonProps, [
                'text' => $rawText,
                'customerName' => $contentData['customerName'] ?? ($contentData['clientName'] ?? 'Client'),
            ]);
        } elseif ($template === 'faq') {
            $props = array_merge($commonProps, [
                'question' => $rawText,
            ]);
        } elseif (in_array($template, ['lifestyle', 'quality_focus'])) {
            $imgUrl = $post->image_path ? asset(Storage::url($post->image_path)) : ($post->image_before_path ? asset(Storage::url($post->image_before_path)) : null);
            $props = array_merge($commonProps, [
                'imageUrl' => $imgUrl,
                'text' => $rawText,
            ]);
        } elseif (in_array($template, ['conseil_saisonnier', 'erreur_a_eviter'])) {
            $props = array_merge($commonProps, [
                'text' => $rawText,
            ]);
        } elseif (in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique'])) {
            $slideTexts = [];
            if (is_array($contentData) && isset($contentData['slides'])) {
                $slideTexts = array_map(fn($s) => is_string($s) ? $s : ($s['text'] ?? ($s['content'] ?? '')), $contentData['slides']);
            }
            $title = is_array($contentData) && isset($contentData['title']) ? $contentData['title'] : ($slideTexts[0] ?? $rawText);
            
            $photos = $post->extra_images ?? [];
            if(!empty($post->image_path)) {
                array_unshift($photos, $post->image_path);
            }
            $slides = empty($photos) ? [null, null, null] : array_map(fn($p) => asset(Storage::url($p)), $photos);
            
            if ($template === 'step_by_step' && count($slides) >= 1 && $slides[0] !== null) {
                array_unshift($slides, null); // Cover slide
            }

            $props = array_merge($commonProps, [
                'title' => $title,
                'slides' => $slides,
                'slideContents' => $slideTexts,
            ]);
        } elseif ($template === 'custom') {
            $ctId = $post->template_options['custom_template_id'] ?? null;
            $ct = \App\Models\CustomTemplate::find($ctId);
            if (!$ct) {
                $ct = (object)[
                    'name' => $post->template_options['custom_template_name'] ?? 'Modèle',
                    'visual_style' => $post->template_options['custom_visual_style'] ?? 'dark_branded',
                    'layout_type' => $post->template_options['custom_layout_type'] ?? 'standard',
                    'photo_mode' => $post->template_options['custom_photo_mode'] ?? 'none',
                ];
            }
            $props = [
                'template' => $ct,
                'brandColor' => $brandColor,
                'companyLabel' => $companyLabel,
                'logoUrl' => $logoUrl,
                'preview' => false,
                'text' => $rawText,
                'slideIndex' => $slideIndexFinal,
                'imageBeforeUrl' => $post->image_before_path ? asset(Storage::url($post->image_before_path)) : null,
                'imageAfterUrl' => $post->image_after_path ? asset(Storage::url($post->image_after_path)) : null,
                'imageUrl' => $post->image_path ? asset(Storage::url($post->image_path)) : null,
            ];
            $componentName = 'post-templates.custom';
        } else {
            $props = null;
        }
    @endphp

    <div style="width: 360px; height: 360px; overflow: hidden; position: relative;">
    @if($props)
        <x-dynamic-component :component="$componentName"
            :brand-color="$props['brandColor'] ?? $brandColor"
            :company-label="$props['companyLabel'] ?? $companyLabel"
            :logo-url="$props['logoUrl'] ?? $logoUrl"
            :preview="$props['preview'] ?? $preview"
            :slide-index="$props['slideIndex'] ?? $slideIndexFinal"
            :text="$props['text'] ?? null"
            :customer-name="$props['customerName'] ?? null"
            :question="$props['question'] ?? null"
            :image-url="$props['imageUrl'] ?? null"
            :image-before-url="$props['imageBeforeUrl'] ?? null"
            :image-after-url="$props['imageAfterUrl'] ?? null"
            :title="$props['title'] ?? null"
            :slides="$props['slides'] ?? null"
            :slide-contents="$props['slideContents'] ?? null"
            :template="$props['template'] ?? null"
        />
    @else
        <div style="width: 360px; height: 360px; background: {{ $brandColor }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; padding: 40px; text-align: center;">
            {{ $rawText }}
        </div>
    @endif
    </div>

</body>
</html>
