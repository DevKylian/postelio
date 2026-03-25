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
    @if($template === 'lifestyle')
        {{-- INLINE lifestyle rendering — bypasses component system entirely --}}
        @php
            $lifestyleText = strtoupper($rawText ?: 'SUR LE TERRAIN');
            $lifestyleLen = mb_strlen($lifestyleText);
            $lifestyleFontSize = $lifestyleLen > 25 ? '10px' : ($lifestyleLen > 18 ? '11px' : '13px');
            $lifestyleImgUrl = $props['imageUrl'] ?? null;
        @endphp

        @if($visualStyle === 'blueprint')
            {{-- BLUEPRINT lifestyle: blue background, grid, photo frame, text label, white footer --}}
            <div style="width: 360px; height: 360px; background: #0047AB; position: relative; overflow: hidden;">
                {{-- Grid overlay --}}
                <div style="position: absolute; inset: 0; opacity: 0.1; pointer-events: none; background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 30px 30px;"></div>

                {{-- Photo frame: top area --}}
                <div style="position: absolute; top: 20px; left: 20px; right: 20px; height: 230px; background: white; padding: 5px; overflow: hidden;">
                    @if($lifestyleImgUrl)
                        <img src="{{ $lifestyleImgUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    @else
                        <div style="width: 100%; height: 100%; background: #ddd;"></div>
                    @endif
                </div>

                {{-- Text label: centered between photo bottom (255px) and footer top (310px) --}}
                <div style="position: absolute; top: 258px; left: 20px; right: 20px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.3); padding: 6px 16px; max-width: 100%; overflow: hidden;">
                        <span style="color: white; font-size: {{ $lifestyleFontSize }}; font-weight: 900; text-transform: uppercase; letter-spacing: 0.15em; line-height: 1.3; text-align: center; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Courier New', monospace;">{{ $lifestyleText }}</span>
                    </div>
                </div>

                {{-- Footer bar --}}
                <div style="position: absolute; left: 0; right: 0; bottom: 0; height: 50px; background: white; border-top: 2px solid #0047AB; padding: 0 28px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: #0047AB; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.2em; font-family: 'Courier New', monospace; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80%;">{{ $companyLabel }}</span>
                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $brandColor }};"></div>
                </div>
            </div>

        @elseif($visualStyle === 'kraft')
            {{-- KRAFT lifestyle --}}
            <div style="width: 360px; height: 360px; background: #BC9A7E; position: relative; overflow: hidden;">
                {{-- Texture overlay --}}
                <div style="position: absolute; inset: 0; opacity: 0.4; pointer-events: none; mix-blend-mode: multiply; background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>

                {{-- Photo area --}}
                <div style="position: absolute; top: 28px; left: 28px; right: 28px; height: 220px; border: 8px solid #1e293b; padding: 10px; background: white; transform: rotate(1deg); overflow: hidden;">
                    @if($lifestyleImgUrl)
                        <img src="{{ $lifestyleImgUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    @else
                        <div style="width: 100%; height: 100%; background: #ddd;"></div>
                    @endif
                </div>

                {{-- Footer --}}
                <div style="position: absolute; left: 0; right: 0; bottom: 0; height: 72px; background: #CBB198; border-top: 6px solid #1e293b; padding: 0 28px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; flex-direction: column; max-width: 200px; overflow: hidden;">
                        <span style="color: #1e293b; font-size: {{ $lifestyleFontSize }}; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $lifestyleText }}</span>
                        <span style="color: rgba(30,41,59,0.4); font-size: 8px; font-weight: 900; text-transform: uppercase; font-family: 'Courier New', monospace; margin-top: 2px;">{{ $companyLabel }}</span>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 50%; border: 4px solid #1e293b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <div style="width: 16px; height: 16px; border-radius: 50%; background-color: {{ $brandColor }};"></div>
                    </div>
                </div>
            </div>

        @elseif($visualStyle === 'glass')
            {{-- GLASS lifestyle --}}
            <div style="width: 360px; height: 360px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
                {{-- Photo --}}
                <div style="position: absolute; top: 20px; left: 20px; right: 20px; height: 230px; border-radius: 16px; overflow: hidden;">
                    @if($lifestyleImgUrl)
                        <img src="{{ $lifestyleImgUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    @else
                        <div style="width: 100%; height: 100%; background: #ddd;"></div>
                    @endif
                </div>

                {{-- Text label --}}
                <div style="position: absolute; top: 258px; left: 20px; right: 20px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.25); border-radius: 12px; padding: 8px 20px; max-width: 100%; overflow: hidden;">
                        <span style="color: white; font-size: {{ $lifestyleFontSize }}; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em; text-align: center; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $lifestyleText }}</span>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="position: absolute; left: 0; right: 0; bottom: 0; height: 50px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 0 28px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: white; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; opacity: 0.9;">{{ $companyLabel }}</span>
                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $brandColor }};"></div>
                </div>
            </div>

        @elseif($visualStyle === 'beton')
            {{-- BETON lifestyle --}}
            <div style="width: 360px; height: 360px; background: #4a4a4a; position: relative; overflow: hidden;">
                {{-- Concrete texture --}}
                <div style="position: absolute; inset: 0; opacity: 0.3; pointer-events: none; background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>

                {{-- Photo --}}
                <div style="position: absolute; top: 20px; left: 20px; right: 20px; height: 230px; overflow: hidden;">
                    @if($lifestyleImgUrl)
                        <img src="{{ $lifestyleImgUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    @else
                        <div style="width: 100%; height: 100%; background: #ddd;"></div>
                    @endif
                </div>

                {{-- Text label --}}
                <div style="position: absolute; top: 258px; left: 20px; right: 20px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); padding: 6px 16px; max-width: 100%; overflow: hidden;">
                        <span style="color: white; font-size: {{ $lifestyleFontSize }}; font-weight: 900; text-transform: uppercase; letter-spacing: 0.15em; text-align: center; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $lifestyleText }}</span>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="position: absolute; left: 0; right: 0; bottom: 0; height: 50px; background: rgba(0,0,0,0.3); padding: 0 28px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: white; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; opacity: 0.8;">{{ $companyLabel }}</span>
                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $brandColor }};"></div>
                </div>
            </div>

        @else
            {{-- STANDARD lifestyle --}}
            <div style="width: 360px; height: 360px; background: #1a1a2e; position: relative; overflow: hidden;">
                {{-- Photo --}}
                <div style="position: absolute; top: 20px; left: 20px; right: 20px; height: 230px; border-radius: 8px; overflow: hidden;">
                    @if($lifestyleImgUrl)
                        <img src="{{ $lifestyleImgUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    @else
                        <div style="width: 100%; height: 100%; background: #ddd;"></div>
                    @endif
                </div>

                {{-- Text label --}}
                <div style="position: absolute; top: 258px; left: 20px; right: 20px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <div style="padding: 6px 16px; max-width: 100%; overflow: hidden;">
                        <span style="color: white; font-size: {{ $lifestyleFontSize }}; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em; text-align: center; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $lifestyleText }}</span>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="position: absolute; left: 0; right: 0; bottom: 0; height: 50px; padding: 0 28px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: white; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; opacity: 0.7;">{{ $companyLabel }}</span>
                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $brandColor }};"></div>
                </div>
            </div>
        @endif

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
