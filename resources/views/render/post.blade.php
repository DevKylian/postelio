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
    <style>
        @php
            $fontRegular = base64_encode(file_get_contents(storage_path('fonts/Montserrat-Regular.ttf')));
            $fontBold    = base64_encode(file_get_contents(storage_path('fonts/Montserrat-Bold.ttf')));
            $fontBlack   = base64_encode(file_get_contents(storage_path('fonts/Montserrat-Black.ttf')));
        @endphp
        /*
         * Montserrat embedded as base64 – completely offline, no CDN dependency.
         * Weight ranges ensure every Tailwind font-weight utility (100–900) maps to
         * the nearest physical file without triggering browser font synthesis.
         *   300–500  → Regular (400)
         *   600–700  → Bold    (700)
         *   800–900  → Black   (900)
         */
        @font-face {
            font-family: 'Montserrat';
            src: url('data:font/ttf;base64,{{ $fontRegular }}') format('truetype');
            font-weight: 100 500;
            font-style: normal;
            font-display: block;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('data:font/ttf;base64,{{ $fontBold }}') format('truetype');
            font-weight: 600 700;
            font-style: normal;
            font-display: block;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('data:font/ttf;base64,{{ $fontBlack }}') format('truetype');
            font-weight: 800 900;
            font-style: normal;
            font-display: block;
        }
    </style>
    @php
        /*
         * ── Inline the compiled Tailwind CSS ─────────────────────────────────────
         * Browsershot renders HTML via ->html() (raw string → temp file). In that
         * context Chromium must fetch any <link> stylesheet over the network. On
         * many production servers (SSL termination at proxy, loopback blocked…)
         * this silently fails, wiping out all Tailwind classes: font-montserrat,
         * font-black, text-sm, etc. Text then falls back to the Ubuntu system font
         * at normal weight → "moins grasse" + wrong line metrics.
         *
         * Fix: read the compiled CSS from the Vite manifest and inline it so there
         * are zero external requests. CDN @import lines are stripped because fonts
         * are already embedded as base64 above.
         *
         * Manifest location:
         *   Vite 5+ (laravel-vite-plugin ≥1)  →  public/build/.vite/manifest.json
         *   Vite 4 / legacy                   →  public/build/manifest.json
         *
         * Fallback: @vite directive (works in dev, where no manifest exists yet).
         */
        $rendererCss   = null;
        $manifestPaths = [
            public_path('build/.vite/manifest.json'),  // Vite 5+ / laravel-vite-plugin v1+
            public_path('build/manifest.json'),         // Vite 4 / legacy
        ];
        foreach ($manifestPaths as $manifestPath) {
            if (!file_exists($manifestPath)) continue;
            try {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssEntry = $manifest['resources/css/app.css'] ?? null;
                if ($cssEntry && !empty($cssEntry['file'])) {
                    $cssPath = public_path('build/' . $cssEntry['file']);
                    if (file_exists($cssPath)) {
                        $raw = file_get_contents($cssPath);
                        // Strip all CDN @import lines – no network calls during rendering
                        $rendererCss = preg_replace(
                            '/@import\s+url\([\'"]?https?:\/\/[^)]+[\'"]?\)\s*(?:layer\([^)]*\))?\s*;/i',
                            '',
                            $raw
                        );
                    }
                }
            } catch (\Throwable $e) {}
            if ($rendererCss !== null) break;
        }
    @endphp
    @if($rendererCss !== null)
        <style>{!! $rendererCss !!}</style>
    @else
        @vite(['resources/css/app.css'])
    @endif
    <style>
            background: #ffffff;
            overflow: hidden;
            /* Remove global padding-top as it doesn't fix internal flex alignment. 
               We will use targeted compensation instead. */
            padding-top: 0; 
            
            /* Antialiased is the most predictable for headless capture. */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: geometricPrecision;
            
            /* Ensure Montserrat is used everywhere by default in this layout */
            font-family: 'Montserrat', sans-serif !important;
        }
        
        /* THE ULTIMATE FIX: Targeted vertical offset for Linux baseline issues 
           We apply a slight push down to centered text elements. 
           Increased to 2.2px as 1.5px was still a bit high. */
        .v-align-fix {
            transform: translateY(2.2px) !important;
        }

        /* Force Grid centering for badges which is more stable than Flex on some OS. */
        .grid-center {
            display: inline-grid !important;
            place-content: center !important;
            align-items: center !important;
        }

        /* Force boldness consistency across platforms - ensuring no system font fallback */
        .font-black { font-weight: 900 !important; font-family: 'Montserrat', sans-serif !important; }
        .font-bold { font-weight: 700 !important; font-family: 'Montserrat', sans-serif !important; }
        
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body>
    <div style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px; overflow: hidden; position: relative;">
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
        
        $name = $company->name;
        $words = explode(' ', $name);
        $companyLabel = count($words) > 1 ? ($words[0] . ' ' . $words[1]) : $name;
        
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

        // Determine total slides for this template (logic must match ImageGeneratorService)
        $totalSlidesBeforeContact = 1;
        if ($template === 'guide_pedagogique') {
            $totalSlidesBeforeContact = is_array($contentData) && isset($contentData['slides']) ? count($contentData['slides']) : 3;
            $totalSlidesBeforeContact += 1;
        } elseif ($template === 'project') {
            $totalSlidesBeforeContact = 3;
        } elseif ($template === 'faq') {
            $faqSlides = (is_array($contentData) && isset($contentData['slides']) && is_array($contentData['slides'])) ? $contentData['slides'] : [];
            $totalSlidesBeforeContact = 1 + count($faqSlides);
        } elseif ($template === 'chantier_detail' || $template === 'step_by_step') {
            $totalSlidesBeforeContact = count($slides ?? []);
        }

        $hasContactInfo = !empty($commonProps['phone']) || !empty($commonProps['email']) || !empty($commonProps['website']);
        $totalSlides = $totalSlidesBeforeContact + ($hasContactInfo ? 1 : 0);
        $isContactSlide = ($slideIndexFinal !== null && $slideIndexFinal >= $totalSlidesBeforeContact && $hasContactInfo);

        // Template specific data
        if ($isContactSlide) {
            $componentName = 'post-templates.contact-slide';
            $props = $commonProps;
        } elseif ($template === 'project') {
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

    <div style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px; overflow: hidden; position: relative;">
    @if($isContactSlide)
         <x-post-templates.contact-slide
            :brand-color="$props['brandColor']"
            :company-label="$props['companyLabel']"
            :phone="$props['phone']"
            :email="$props['email']"
            :website="$props['website']"
            :trade="$props['trade']"
            :city="$props['city']"
            :visual-style="$props['visualStyle']"
            :brand-text-color="$brandTextColor"
            :accent-color="$props['accentColor']"
            :accent-text-color="$props['accentTextColor']"
        />
    @elseif($template === 'lifestyle')
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
            :brand-text-color="$brandTextColor"
            :accent-color="$props['accentColor'] ?? $accentColor"
            :accent-text-color="$props['accentTextColor'] ?? $accentTextColor"
            :exporting="$props['exporting'] ?? false"
        />
    @else
        <div style="width: {{ $canvasWidth }}px; height: {{ $canvasHeight }}px; background: {{ $brandColor }}; color: {{ $brandTextColor }}; display: flex; align-items: center; justify-content: center; font-size: 16px; padding: 40px; text-align: center;">
            {{ $rawText }}
        </div>
    @endif
    </div>

</body>
</html>
