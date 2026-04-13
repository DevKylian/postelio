<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    @php
        $aspectRatio = $post->template_options['aspect_ratio'] ?? '1:1';
        $canvasWidth = 360;
        $canvasHeight = ($aspectRatio === '4:5') ? 450 : 360;
    @endphp
    <meta name="viewport" content="width={{ $canvasWidth }}, initial-scale=1">
    @vite(['resources/css/app.css'])
    <style>
        body {
            margin: 0;
            padding: 0;
            width: {{ $canvasWidth }}px;
            height: {{ $canvasHeight }}px;
            background: #ffffff;
            overflow: hidden;
        }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body>
    <div class="font-montserrat" style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px; overflow: hidden; position: relative;">
    @php
        use Illuminate\Support\Facades\Storage;
        
        $company = $post->company;
        $brandColor = $company->brand_color ?? '#F59E0B';
        $accentColor = $company->secondary_color ?? '#0F172A';
        
        $getContrastColor = function($hex) {
            $hex = str_replace('#', '', $hex);
            if (strlen($hex) == 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
            return ($yiq >= 186) ? '#1E293B' : '#FFFFFF';
        };
        $brandTextColor = $getContrastColor($brandColor);
        $accentTextColor = $getContrastColor($accentColor);
        
        $companyLabel = $company->name;
        
        $logoUrl = $company->logo_path ? asset(Storage::url($company->logo_path)) : null;
        
        $template = $post->template;
        $preview = false;
        $parsedIndex = filter_var($slideIndex, FILTER_VALIDATE_INT);
        $slideIndexFinal = $parsedIndex !== false ? (int)$slideIndex : null;
        
        $contentData = json_decode($post->refined_content, true);
        $rawText = is_array($contentData) ? ($contentData['title'] ?? $contentData['image_text'] ?? $contentData['caption'] ?? '') : ($post->refined_content ?? '');
        $rawText = preg_replace('/#\S+/', '', $rawText);
        $rawText = trim($rawText);

        // ── Render-time smart truncation (hardcoded, no config/cache dependency) ──
        $imageTextMaxChars = match($template) {
            'lifestyle' => 30,
            'project' => 40,
            'quality_focus' => 50,
            default => null,
        };

        if ($imageTextMaxChars && mb_strlen($rawText) > $imageTextMaxChars) {
            $danglingWords = ['de', 'du', 'des', 'le', 'la', 'les', 'un', 'une',
                'sur', 'sous', 'dans', 'en', 'et', 'ou', 'à', 'au', 'aux',
                'pour', 'par', 'avec', 'sans', 'vers', 'ce', 'cette', 'ces',
                'son', 'sa', 'ses', 'qui', 'que', 'dont'];

            // 1. Cut at clause separator (: — –) if found
            $sub = mb_substr($rawText, 0, $imageTextMaxChars);
            $clauseBreak = max(
                mb_strrpos($sub, ' :') ?: 0, mb_strrpos($sub, ' —') ?: 0,
                mb_strrpos($sub, ' –') ?: 0, mb_strrpos($sub, ' -') ?: 0,
            );

            if ($clauseBreak > $imageTextMaxChars * 0.4) {
                $rawText = trim(mb_substr($rawText, 0, $clauseBreak));
            } else {
                // 2. Cut at last space, then strip dangling words
                $lastSpace = mb_strrpos($sub, ' ');
                if ($lastSpace) {
                    $rawText = trim(mb_substr($rawText, 0, $lastSpace));
                    for ($i = 0; $i < 5; $i++) {
                        $lastWord = mb_strtolower(preg_replace('/.*\s+/u', '', $rawText));
                        // Strip dangling words AND half-hyphenated words (e.g. "savoir-")
                        if ((in_array($lastWord, $danglingWords) || mb_substr($rawText, -1) === '-')
                            && mb_strrpos($rawText, ' ') !== false) {
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
        \Illuminate\Support\Facades\Log::info('RENDER DEBUG', [
            'post_id' => $post->id,
            'template' => $template,
            'visual_style' => $visualStyle,
            'component' => $componentName,
            'text_length' => mb_strlen($rawText),
            'text_preview' => mb_substr($rawText, 0, 50),
        ]);
        
        // Prepare common props
        $commonProps = [
            'brandColor' => $brandColor,
            'brandTextColor' => $brandTextColor,
            'companyLabel' => $companyLabel,
            'logoUrl' => $logoUrl,
            'preview' => $preview,
            'accentColor' => $accentColor,
            'accentTextColor' => $accentTextColor,
            'slideIndex' => $slideIndexFinal,
            'phone' => $post->template_options['company_phone'] ?? $company->phone,
            'email' => $post->template_options['company_email'] ?? $company->email,
            'website' => $post->template_options['company_website'] ?? $company->website,
            'trade' => $post->template_options['company_trade'] ?? $company->trade,
            'city' => $post->template_options['company_city'] ?? $company->city,
            'visualStyle' => $visualStyle,
            'exporting' => true,
        ];

        // Prepare specialized data early for count calculation
        if (in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique'])) {
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
        }

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
                'customerName' => $contentData['customer_name'] ?? ($contentData['customerName'] ?? ($contentData['clientName'] ?? 'Client')),
            ]);
        } elseif ($template === 'faq') {
            $faqSlides = (is_array($contentData) && isset($contentData['slides']) && is_array($contentData['slides']))
                ? $contentData['slides']
                : [];
            $answerIndex = ($slideIndexFinal !== null && $slideIndexFinal > 0) ? $slideIndexFinal - 1 : 0;
            $faqAnswer = $faqSlides[$answerIndex] ?? 'La réponse arrive bientôt...';
            $props = array_merge($commonProps, [
                'question' => $rawText ?: 'Une question fréquente ?',
                'answer' => $faqAnswer,
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
            $props = array_merge($commonProps, [
                'title' => $title ?? '',
                'slides' => $slides ?? [],
                'slideContents' => $slideTexts ?? [],
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
            $props = array_merge($commonProps, [
                'template' => $ct,
                'text' => $rawText,
                'imageBeforeUrl' => $post->image_before_path ? asset(Storage::url($post->image_before_path)) : null,
                'imageAfterUrl' => $post->image_after_path ? asset(Storage::url($post->image_after_path)) : null,
                'imageUrl' => $post->image_path ? asset(Storage::url($post->image_path)) : null,
            ]);
            $componentName = 'post-templates.custom';
        } else {
            $props = null;
        }
    @endphp

    @if($template === 'lifestyle')
        <x-post-templates.lifestyle
            :brand-color="$props['brandColor']"
            :company-label="$props['companyLabel']"
            :logo-url="$props['logoUrl']"
            :preview="$props['preview']"
            :slide-index="$props['slideIndex']"
            :text="$props['text']"
            :image-url="$props['imageUrl']"
            :trade="$props['trade']"
            :city="$props['city']"
            :phone="$props['phone']"
            :email="$props['email']"
            :website="$props['website']"
            :brand-text-color="$brandTextColor"
            :visual-style="$visualStyle"
            :accent-color="$props['accentColor']"
            :accent-text-color="$props['accentTextColor']"
            :exporting="true"
        />

    @elseif($props)
        <x-dynamic-component :component="$componentName"
            :brand-color="$props['brandColor'] ?? $brandColor"
            :company-label="$props['companyLabel'] ?? $companyLabel"
            :logo-url="$props['logoUrl'] ?? $logoUrl"
            :preview="$props['preview'] ?? $preview"
            :slide-index="$props['slideIndex'] ?? $slideIndexFinal"
            :text="$props['text'] ?? null"
            :customer-name="$props['customerName'] ?? null"
            :question="$props['question'] ?? null"
            :answer="$props['answer'] ?? null"
            :image-url="$props['imageUrl'] ?? null"
            :image-before-url="$props['imageBeforeUrl'] ?? null"
            :image-after-url="$props['imageAfterUrl'] ?? null"
            :title="$props['title'] ?? null"
            :slides="$props['slides'] ?? null"
            :slide-contents="$props['slideContents'] ?? null"
            :template="$props['template'] ?? null"
            :trade="$props['trade'] ?? null"
            :city="$props['city'] ?? null"
            :phone="$props['phone'] ?? null"
            :email="$props['email'] ?? null"
            :website="$props['website'] ?? null"
            :brand-text-color="$brandTextColor"
            :accent-color="$props['accentColor'] ?? $accentColor"
            :accent-text-color="$props['accentTextColor'] ?? $accentTextColor"
            :exporting="$props['exporting'] ?? false"
        />
    @else
        <div style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px; background: {{ $brandColor }}; color: {{ $brandTextColor }}; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 16px; padding: 40px; text-align: center; font-family: 'Montserrat', sans-serif; font-weight: 900; line-height: 1.2;">
            {{ $rawText }}
        </div>
    @endif
    </div>

</body>
</html>
