<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=360, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @php $renderHeight = str_starts_with($template, 'story-') ? 640 : 450; @endphp
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            width: 360px; height: {{ $renderHeight }}px;
            overflow: hidden;
            background: #111827;
        }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body>
    <div style="width: 360px; height: {{ $renderHeight }}px; overflow: hidden; position: relative;">
        @php
            // Base64 logic for export reliability
            $base64Image = null;
            $image_url = $data['image_url'] ?? $data['image1_b64'] ?? null;
            if ($image_url && str_contains($image_url, 'livewire-tmp')) {
                try {
                    $pathParts = explode('/', parse_url($image_url, PHP_URL_PATH));
                    $fileName = end($pathParts);
                    $localPath = storage_path('app/livewire-tmp/' . $fileName);
                    if (file_exists($localPath)) {
                        $type = pathinfo($localPath, PATHINFO_EXTENSION);
                        $data_file = @file_get_contents($localPath);
                        if ($data_file) {
                            $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data_file);
                        }
                    }
                } catch (\Exception $e) {}
            }
            
            // In case image_url is already base64 (passed by controller during export)
            if ($image_url && str_starts_with($image_url, 'data:image')) {
                $base64Image = $image_url;
            }
        @endphp

        @if($template === 'annonce')
            <x-postelio-instagram.annonce
                :tag="$data['tag'] ?? 'NOUVEAUTÉ'"
                :headline="$data['headline'] ?? ''"
                :subheadline="$data['subheadline'] ?? ''"
                :cta="$data['cta'] ?? 'Commencer gratuitement'"
            />
        @elseif($template === 'conseil')
            <x-postelio-instagram.conseil
                :number="$data['number'] ?? '01'"
                :tip_title="$data['tip_title'] ?? ''"
                :tip_body="$data['tip_body'] ?? ''"
                :hashtags="$data['hashtags'] ?? '#artisan #instagram'"
                :show_swipe="$data['show_swipe'] ?? false"
            />
        @elseif($template === 'stats')
            <x-postelio-instagram.stats
                :stat="$data['stat'] ?? '87%'"
                :stat_label="$data['stat_label'] ?? 'des artisans'"
                :stat_description="$data['stat_description'] ?? ''"
                :insight="$data['insight'] ?? 'Et si vous étiez différent ?'"
            />
        @elseif($template === 'cta')
            <x-postelio-instagram.cta
                :hook="$data['hook'] ?? ''"
                :description="$data['description'] ?? ''"
                :cta="$data['cta'] ?? 'Commencer gratuitement →'"
                :url="$data['url'] ?? 'postelio.fr'"
            />
        @elseif($template === 'temoignage')
            <x-postelio-instagram.temoignage
                :quote="$data['quote'] ?? ''"
                :author="$data['author'] ?? 'Marc D.'"
                :profession="$data['profession'] ?? 'Artisan'"
                :stars="(int)($data['stars'] ?? 5)"
            />
        @elseif($template === 'avantages')
            <x-postelio-instagram.avantages
                :headline="$data['headline'] ?? ''"
                :tag="$data['tag'] ?? 'POURQUOI POSTELIO ?'"
                :items="$data['items'] ?? []"
            />
        @elseif($template === 'question')
            <x-postelio-instagram.question
                :question="$data['question'] ?? ''"
                :teaser="$data['teaser'] ?? ''"
                :tag="$data['tag'] ?? 'BONNE QUESTION'"
            />
        @elseif($template === 'comparaison')
            <x-postelio-instagram.comparaison
                :headline="$data['headline'] ?? 'La différence est réelle.'"
                :col_sans="$data['col_sans'] ?? []"
                :col_avec="$data['col_avec'] ?? []"
            />
        @elseif($template === 'chantier')
            <x-postelio-instagram.chantier
                :tag="$data['tag'] ?? 'RÉALISATION'"
                :headline="$data['headline'] ?? ''"
                :caption="$data['caption'] ?? ''"
                :image_url="$data['image_url'] ?? $data['image1_b64'] ?? null"
            />
        @elseif($template === 'avantapres')
            <x-postelio-instagram.avantapres
                :image_before_url="$data['image1_b64'] ?? null"
                :image_after_url="$data['image2_b64'] ?? null"
                :headline="$data['headline'] ?? 'Transformation complète'"
                :tag="$data['tag'] ?? 'AVANT / APRÈS'"
            />
        @elseif($template === 'appmockup')
            <x-postelio-instagram.appmockup
                :image_url="$data['image_url'] ?? $data['image1_b64'] ?? null"
                :tag="$data['tag'] ?? 'EN ACTION'"
                :headline="$data['headline'] ?? ''"
                :subheadline="$data['subheadline'] ?? ''"
            />
        @elseif($template === 'educatif')
            <x-postelio-instagram.educatif
                :tag="$data['tag'] ?? 'POINT EXPERT'"
                :title="$data['title'] ?? ''"
                :body="$data['body'] ?? ''"
                :insight="$data['insight'] ?? 'Leçon'"
            />
        @elseif($template === 'hero')
            <x-postelio-instagram.hero
                :tag="$data['tag'] ?? 'OFFICIEL'"
                :headline="$data['headline'] ?? ''"
                :body="$data['body'] ?? ''"
            />
        @elseif($template === 'article-blog')
            <x-postelio-instagram.article-blog
                :tag="$data['tag'] ?? 'NOUVEL ARTICLE'"
                :title="$data['title'] ?? ''"
                :excerpt="$data['excerpt'] ?? ''"
                :cta="$data['cta'] ?? 'Lire l\'article'"
                :url="$data['url'] ?? 'postelio.fr/blog'"
                :image_url="$data['image_url'] ?? $data['image1_b64'] ?? null"
            />
        @elseif($template === 'story-article-blog')
            <x-postelio-instagram.story-article-blog
                :tag="$data['tag'] ?? 'NOUVEL ARTICLE'"
                :title="$data['title'] ?? ''"
                :excerpt="$data['excerpt'] ?? ''"
                :cta="$data['cta'] ?? 'Lire l\'article'"
                :url="$data['url'] ?? 'postelio.fr/blog'"
                :image_url="$data['image_url'] ?? $data['image1_b64'] ?? null"
            />
        @elseif($template === 'feature-app')
            <x-postelio-instagram.feature-app
                :image_url="$data['image_url'] ?? $data['image1_b64'] ?? null"
                :feature_tag="$data['feature_tag'] ?? 'FONCTIONNALITÉ'"
                :feature_name="$data['feature_name'] ?? ''"
                :feature_description="$data['feature_description'] ?? ''"
                :feature_cta="$data['feature_cta'] ?? 'Essayer gratuitement'"
            />
        @endif
    </div>
</body>
</html>
