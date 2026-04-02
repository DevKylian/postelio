<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=360, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            width: 360px; height: 360px;
            overflow: hidden;
            background: #111827;
        }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body>
    <div style="width: 360px; height: 360px; overflow: hidden; position: relative;">
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
        @endif
    </div>
</body>
</html>
