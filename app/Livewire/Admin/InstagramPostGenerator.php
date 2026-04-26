<?php

namespace App\Livewire\Admin;

use App\Models\InstagramGeneratedPost;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use OpenAI\Laravel\Facades\OpenAI;
use Spatie\Browsershot\Browsershot;

#[Layout('layouts.app')]
class InstagramPostGenerator extends Component
{
    use WithFileUploads;

    // ── Form ──────────────────────────────────────────────────────────────────
    public string $topic    = '';
    public string $template = 'annonce';
    public string $tone     = 'professionnel';

    // ── State ─────────────────────────────────────────────────────────────────
    public bool    $generating   = false;
    public bool    $downloading  = false;
    public ?string $errorMessage = null;
    public bool    $hasContent   = false;
    public string  $caption      = '';
    public array   $history      = [];
    public array   $slides       = [];
    public int     $currentSlideIndex = 0;
    public bool    $show_swipe = false;

    // ── Image uploads ─────────────────────────────────────────────────────────
    public $uploadImage1 = null; // image gauche (avantapres) ou screenshot (appmockup)
    public $uploadImage2 = null; // image droite (avantapres only)

    // ── Editable fields: annonce ──────────────────────────────────────────────
    public string $tag         = '';
    public string $headline    = '';
    public string $subheadline = '';
    public string $cta         = '';

    // ── Editable fields: conseil ──────────────────────────────────────────────
    public string $number    = '';
    public string $tip_title = '';
    public string $tip_body  = '';
    public string $hashtags  = '';

    // ── Editable fields: stats ────────────────────────────────────────────────
    public string $stat             = '';
    public string $stat_label       = '';
    public string $stat_description = '';
    public string $insight          = '';

    // ── Editable fields: cta ─────────────────────────────────────────────────
    public string $cta_hook    = '';
    public string $description = '';
    public string $url         = 'postelio.fr';

    // ── Editable fields: temoignage ───────────────────────────────────────────
    public string $quote      = '';
    public string $author     = '';
    public string $profession = '';
    public int    $stars      = 5;

    public string $avantages_headline = '';
    public string $avantages_tag      = '';
    public string $avantages_body     = '';
    public array  $items              = ['', '', '', ''];

    // ── Editable fields: question ─────────────────────────────────────────────
    public string $question        = '';
    public string $question_teaser = '';
    public string $question_tag    = '';

    // ── Editable fields: comparaison ──────────────────────────────────────────
    public string $comparaison_headline = '';
    public array  $col_sans             = [];
    public array  $col_avec             = [];

    // ── Editable fields: avantapres ───────────────────────────────────────────
    public string $avantapres_headline = '';
    public string $avantapres_tag      = '';

    // ── Editable fields: appmockup ────────────────────────────────────────────
    public string $appmockup_tag         = '';
    public string $appmockup_headline    = '';
    public string $appmockup_subheadline = '';

    // ── Editable fields: feature-app ──────────────────────────────────────────
    public string $feature_tag         = '';
    public string $feature_name        = '';
    public string $feature_description = '';
    public string $feature_cta         = '';

    // ── Editable fields: educatif ─────────────────────────────────────────────
    public string $educatif_tag     = '';
    public string $educatif_title   = '';
    public string $educatif_body    = '';
    public string $educatif_insight = '';

    // ── Editable fields: hero ─────────────────────────────────────────────────
    public string $hero_tag      = '';
    public string $hero_headline = '';
    public string $hero_body     = '';

    // ── Editable fields: article-blog ─────────────────────────────────────────
    public string $article_blog_tag     = 'NOUVEL ARTICLE';
    public string $article_blog_title   = '';
    public string $article_blog_excerpt = '';
    public string $article_blog_cta     = 'Lire l\'article';
    public string $article_blog_url     = 'postelio.fr/blog';

    // ── Editable fields: chantier ─────────────────────────────────────────────
    public string $photo_tag      = 'RÉALISATION';
    public string $photo_headline = '';
    public string $photo_caption  = '';

    // ── Editable fields: focus ────────────────────────────────────────────────
    public string $focus_tag         = 'FOCUS SOLUTION';
    public string $focus_title       = '';
    public string $focus_description = '';
    public string $focus_benefit     = '';

    // ── Editable fields: promo ───────────────────────────────────────────────
    public string $promo_tag         = '';
    public string $promo_headline    = '';
    public string $promo_discount    = '';
    public string $promo_subheadline = '';
    public string $promo_cta         = '';

    // ── Editable fields: showcase ──────────────────────────────────────────────
    public string $showcase_tag = 'RÉSULTAT FINAL';
    public string $showcase_caption = '';

    // ── Image-based templates ─────────────────────────────────────────────────
    private const IMAGE_TEMPLATES = ['avantapres', 'appmockup', 'feature-app', 'article-blog', 'chantier', 'focus', 'showcase'];

    public function mount(): void
    {
        $this->history = InstagramGeneratedPost::where('user_id', auth()->id())
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn($item) => [
                'id'       => $item->id,
                'template' => $item->template,
                'topic'    => html_entity_decode($item->topic ?? '', ENT_QUOTES, 'UTF-8'),
                'data'     => $item->content_data,
                'caption'  => $item->caption,
                'time'     => $item->created_at->format('H:i'),
                'date'     => $item->created_at->format('d/m'),
            ])
            ->toArray();
    }

    public function resetFields(): void
    {
        $this->tag = $this->headline = $this->subheadline = $this->cta = '';
        $this->number = $this->tip_title = $this->tip_body = $this->hashtags = '';
        $this->stat = $this->stat_label = $this->stat_description = $this->insight = '';
        $this->cta_hook = $this->description = '';
        $this->url = 'postelio.fr';
        $this->quote = $this->author = $this->profession = '';
        $this->stars = 5;
        $this->avantages_headline = $this->avantages_tag = $this->avantages_body = '';
        $this->items = ['', '', '', ''];
        $this->question = $this->question_teaser = $this->question_tag = '';
        $this->comparaison_headline = '';
        $this->col_sans = ['', '', '', '', ''];
        $this->col_avec = ['', '', '', '', ''];
        $this->avantapres_tag = $this->avantapres_headline = '';
        $this->appmockup_tag = $this->appmockup_headline = $this->appmockup_subheadline = '';
        $this->feature_tag = $this->feature_name = $this->feature_description = $this->feature_cta = '';
        $this->educatif_tag = $this->educatif_title = $this->educatif_body = $this->educatif_insight = '';
        $this->hero_tag = $this->hero_headline = $this->hero_body = '';
        $this->article_blog_tag = 'NOUVEL ARTICLE';
        $this->article_blog_title = $this->article_blog_excerpt = $this->article_blog_cta = '';
        $this->article_blog_url = 'postelio.fr/blog';
        $this->promo_tag = $this->promo_headline = $this->promo_discount = $this->promo_subheadline = $this->promo_cta = '';
        $this->focus_tag = 'FOCUS SOLUTION';
        $this->focus_title = $this->focus_description = $this->focus_benefit = '';
        $this->showcase_tag = 'RÉSULTAT FINAL';
        $this->showcase_caption = '';
        $this->uploadImage1 = null;
        $this->uploadImage2 = null;
        $this->hasContent   = false;
        $this->errorMessage = null;
        $this->caption      = '';
        $this->slides       = [];
        $this->currentSlideIndex = 0;
    }

    public function updatedTemplate(): void
    {
        $this->resetFields();
    }

    public function updatedUploadImage1(): void
    {
        $this->validateOnly('uploadImage1', [
            'uploadImage1' => 'nullable|image|max:8192',
        ]);
        // Once image uploaded on image-based templates, mark as having content
        if (in_array($this->template, self::IMAGE_TEMPLATES)) {
            $this->hasContent = true;
        }
    }

    public function updatedUploadImage2(): void
    {
        $this->validateOnly('uploadImage2', [
            'uploadImage2' => 'nullable|image|max:8192',
        ]);
    }

    public function generate(): void
    {
        $this->validate([
            'topic'    => 'required|min:3',
            'template' => 'required|in:annonce,conseil,stats,cta,temoignage,avantages,question,comparaison,avantapres,appmockup,educatif,hero,article-blog,feature-app,story-article-blog,story-temoignage,promo,chantier,focus,showcase',
            'tone'     => 'required|in:professionnel,accessible,dynamique,inspirant',
        ]);

        $this->generating   = true;
        $this->hasContent   = false;
        $this->errorMessage = null;

        try {
            $response = OpenAI::chat()->create([
                'model'           => 'gpt-4o',
                'messages'        => [
                    ['role' => 'system', 'content' => $this->buildSystemPrompt()],
                    ['role' => 'user',   'content' => $this->buildUserPrompt()],
                ],
                'max_tokens'      => 600,
                'temperature'     => 0.6,
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = trim($response->choices[0]->message->content);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Illuminate\Support\Facades\Log::error('InstagramPostGenerator: Invalid AI Response', [
                    'json_error' => json_last_error_msg(),
                    'raw_content' => $content
                ]);
                throw new \Exception("Réponse AI invalide. L'IA a rencontré un problème de formatage.");
            }

            $this->fillFields($data);
            $this->hasContent = true;

        } catch (\Throwable $e) {
            $this->errorMessage = 'Erreur lors de la génération : ' . $e->getMessage();
        } finally {
            $this->generating = false;
        }
    }

    public function restoreHistory(int $index): void
    {
        if (!isset($this->history[$index])) {
            return;
        }
        $item           = $this->history[$index];
        $this->template = $item['template'];
        $this->topic    = html_entity_decode($item['topic'] ?? '', ENT_QUOTES, 'UTF-8');
        $this->caption  = $item['caption'] ?? '';
        $this->fillFields($item['data']);
        $this->hasContent = true;
    }

    public function saveToHistory(): void
    {
        if (!$this->hasContent) {
            return;
        }

        $post = InstagramGeneratedPost::create([
            'user_id'      => auth()->id(),
            'template'     => $this->template,
            'topic'        => $this->topic,
            'content_data' => $this->buildDataArray(),
            'caption'      => $this->caption,
        ]);

        array_unshift($this->history, [
            'id'       => $post->id,
            'template' => $post->template,
            'topic'    => $post->topic,
            'data'     => $post->content_data,
            'caption'  => $post->caption,
            'time'     => $post->created_at->format('H:i'),
            'date'     => $post->created_at->format('d/m'),
        ]);

        if (count($this->history) > 15) {
            array_pop($this->history);
        }

        $this->dispatch('post-saved');
    }

    private function buildDataArray(): array
    {
        if (!empty($this->slides)) {
            return [
                'slides'            => $this->slides,
                'instagram_caption' => $this->caption,
            ];
        }

        return match ($this->template) {
            'annonce' => [
                'tag'         => $this->tag,
                'headline'    => $this->headline,
                'subheadline' => $this->subheadline,
                'cta'         => $this->cta,
            ],
            'conseil' => [
                'number'    => $this->number,
                'tip_title' => $this->tip_title,
                'tip_body'  => $this->tip_body,
                'hashtags'  => $this->hashtags,
            ],
            'stats' => [
                'stat'             => $this->stat,
                'stat_label'       => $this->stat_label,
                'stat_description' => $this->stat_description,
                'insight'          => $this->insight,
            ],
            'cta' => [
                'hook'        => $this->cta_hook,
                'description' => $this->description,
                'cta'         => $this->cta,
                'url'         => $this->url,
            ],
            'temoignage', 'story-temoignage' => [
                'quote'      => $this->quote,
                'author'     => $this->author,
                'profession' => $this->profession,
                'stars'      => $this->stars,
            ],
            'avantages' => [
                'headline' => $this->avantages_headline,
                'tag'      => $this->avantages_tag,
                'body'     => $this->avantages_body,
                'items'    => $this->items,
            ],
            'question' => [
                'question' => $this->question,
                'teaser'   => $this->question_teaser,
                'tag'      => $this->question_tag,
            ],
            'comparaison' => [
                'headline' => $this->comparaison_headline,
                'col_sans' => $this->col_sans,
                'col_avec' => $this->col_avec,
            ],
            'avantapres' => [
                'headline' => $this->avantapres_headline,
                'tag'      => $this->avantapres_tag,
            ],
            'appmockup' => [
                'tag'         => $this->appmockup_tag,
                'headline'    => $this->appmockup_headline,
                'subheadline' => $this->appmockup_subheadline,
            ],
            'feature-app' => [
                'feature_tag'         => $this->feature_tag,
                'feature_name'        => $this->feature_name,
                'feature_description' => $this->feature_description,
                'feature_cta'         => $this->feature_cta,
            ],
            'educatif' => [
                'tag'     => $this->educatif_tag,
                'title'   => $this->educatif_title,
                'body'    => $this->educatif_body,
                'insight' => $this->educatif_insight,
            ],
            'hero' => [
                'tag'      => $this->hero_tag,
                'headline' => $this->hero_headline,
                'body'     => $this->hero_body,
            ],
            'article-blog', 'story-article-blog' => [
                'tag'     => $this->article_blog_tag,
                'title'   => $this->article_blog_title,
                'excerpt' => $this->article_blog_excerpt,
                'cta'     => $this->article_blog_cta,
                'url'     => $this->article_blog_url,
            ],
            'chantier' => [
                'tag'      => $this->photo_tag,
                'headline' => $this->photo_headline,
                'caption'  => $this->photo_caption,
            ],
            'promo' => [
                'tag'         => $this->promo_tag,
                'headline'    => $this->promo_headline,
                'discount'    => $this->promo_discount,
                'subheadline' => $this->promo_subheadline,
                'cta'         => $this->promo_cta,
            ],
            'focus' => [
                'tag'         => $this->focus_tag,
                'title'       => $this->focus_title,
                'description' => $this->focus_description,
                'benefit'     => $this->focus_benefit,
            ],
            'showcase' => [
                'tag'     => $this->showcase_tag,
                'caption' => $this->showcase_caption,
            ],
            default => [],
        };
    }

    public function downloadPng(): mixed
    {
        if (!$this->hasContent) {
            return null;
        }

        if (in_array($this->template, self::IMAGE_TEMPLATES) && !$this->uploadImage1) {
            $this->errorMessage = 'Veuillez uploader au moins une image avant de générer le PNG.';
            return null;
        }

        $this->downloading = true;

        try {
            // Case 1: Carousel (Multi-Slide)
            if (count($this->slides) > 1) {
                $zip = new \ZipArchive();
                $zipFileName = storage_path('app/public/postelio-carousel-' . now()->format('Ymd-His') . '.zip');
                
                if ($zip->open($zipFileName, \ZipArchive::CREATE) !== TRUE) {
                    throw new \RuntimeException("Impossible de créer le fichier ZIP.");
                }

            $isStory  = str_starts_with($this->template, 'story-');
            $frameH   = $isStory ? 640 : 450;

            foreach ($this->slides as $i => $slide) {
                $slideData = $this->prepareSlideData($slide, $i);

                // Embed images if relevant
                if ($this->uploadImage1) $slideData['image1_b64'] = $this->fileToDataUri($this->uploadImage1);
                if ($this->uploadImage2) $slideData['image2_b64'] = $this->fileToDataUri($this->uploadImage2);

                // Render the slide using the common layout
                $html = view('render.postelio-instagram', [
                    'template' => $this->template,
                    'data'     => $slideData,
                ])->render();

                    $instance = Browsershot::html($html)
                        ->windowSize(360, $frameH)
                        ->deviceScaleFactor(3)
                        ->clip(0, 0, 360, $frameH)
                        ->noSandbox()
                        ->waitUntilNetworkIdle()
                        ->delay(500)
                        ->timeout(60);

                    $chromePath = env('BROWSERSHOT_CHROME_PATH');
                    if ($chromePath) {
                        $instance->setChromePath($chromePath);
                    }

                    $png = $instance->screenshot();

                    $zip->addFromString('slide-' . ($i + 1) . '.png', $png);
                }

                $zip->close();
                return response()->download($zipFileName)->deleteFileAfterSend(true);
            }

            // Case 2: Single Slide (Regular)
            $isStory = str_starts_with($this->template, 'story-');
            $frameH  = $isStory ? 640 : 450;

            $data = $this->buildDataArray();
            if ($this->uploadImage1) $data['image1_b64'] = $this->fileToDataUri($this->uploadImage1);
            if ($this->uploadImage2) $data['image2_b64'] = $this->fileToDataUri($this->uploadImage2);

            $html = view('render.postelio-instagram', [
                'template' => $this->template,
                'data'     => $data,
            ])->render();

            $instance = Browsershot::html($html)
                ->windowSize(360, $frameH)
                ->deviceScaleFactor(3)
                ->clip(0, 0, 360, $frameH)
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->delay(500)
                ->timeout(60);

            $chromePath = env('BROWSERSHOT_CHROME_PATH');
            if ($chromePath) {
                $instance->setChromePath($chromePath);
            }

            $png = $instance->screenshot();

            $filename = 'postelio-instagram-' . $this->template . '-' . now()->format('Ymd-His') . '.png';
            return response()->streamDownload(fn() => print($png), $filename, ['Content-Type' => 'image/png']);

        } catch (\Throwable $e) {
            $this->errorMessage = 'Erreur lors du rendu PNG : ' . $e->getMessage();
            return null;
        } finally {
            $this->downloading = false;
        }
    }

    private function prepareSlideData(array $slide, int $index = 0): array
    {
        // Universal keys mapping for the render view
        $title = $slide['title'] ?? $slide['headline'] ?? $slide['tip_title'] ?? $slide['stat_label'] ?? '';
        $body  = $slide['body']  ?? $slide['subheadline'] ?? $slide['tip_body'] ?? $slide['stat_description'] ?? '';
        $tag   = $slide['tag']   ?? $slide['label'] ?? '';
        $cta   = $slide['cta']   ?? '';

        $autoNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
        $total = count($this->slides);
        $showSwipe = ($total > 1 && $index < $total - 1);
        if (empty($tag) && $this->template === 'conseil') $tag = 'CONSEIL PRO';

        return match($this->template) {
            'annonce' => [
                'tag' => $tag, 'headline' => $title, 'subheadline' => $body, 'cta' => $cta, 'show_swipe' => $showSwipe,
            ],
            'conseil' => [
                'number' => $slide['number'] ?? $autoNumber, 'tip_title' => $title, 'tip_body' => $body, 'hashtags' => $slide['hashtags'] ?? '', 'show_swipe' => $showSwipe,
            ],
            'stats' => [
                'stat' => $slide['stat'] ?? '', 'stat_label' => $title, 'stat_description' => $body, 'insight' => $slide['insight'] ?? '',
            ],
            'cta' => [
                'hook' => $title, 'description' => $body, 'cta' => $cta, 'url' => $slide['url'] ?? 'postelio.fr',
            ],
            'temoignage' => [
                'quote' => $body, 'author' => $slide['author'] ?? '', 'profession' => $slide['profession'] ?? '', 'stars' => $slide['stars'] ?? 5,
            ],
            'avantages' => [
                'headline' => $title, 'tag' => $tag, 'items' => !empty($slide['items']) ? $slide['items'] : $this->items, 'body' => $slide['body'] ?? $this->avantages_body,
            ],
            'question' => [
                'question' => $title, 'teaser' => $body, 'tag' => $tag,
            ],
            'educatif' => [
                'tag' => $tag, 'title' => $title, 'body' => $body, 'insight' => $slide['insight'] ?? '',
            ],
            'hero' => [
                'tag' => $tag, 'headline' => $title, 'body' => $body,
            ],
            'comparaison' => [
                'headline' => $title, 
                'col_sans' => !empty($slide['col_sans']) ? $slide['col_sans'] : ['Ancien système', 'Perte de temps', 'Compliqué', 'Pas de visibilité', 'Résultats aléatoires'],
                'col_avec' => !empty($slide['col_avec']) ? $slide['col_avec'] : ['Postelio AI', 'Gain de temps', 'Intuitif', 'Visibilité accrue', 'Résultats mesurables'],
            ],
            'avantapres' => [
                'tag' => $tag, 'headline' => $title,
                'image1_b64' => $slide['image1_b64'] ?? null,
                'image2_b64' => $slide['image2_b64'] ?? null,
            ],
            'appmockup' => [
                'tag' => $tag, 'headline' => $title, 'subheadline' => $body,
                'image1_b64' => $slide['image1_b64'] ?? null,
            ],
            'feature-app' => [
                'feature_tag'         => $slide['feature_tag'] ?? $tag,
                'feature_name'        => $slide['feature_name'] ?? $title,
                'feature_description' => $slide['feature_description'] ?? $body,
                'feature_cta'         => $slide['feature_cta'] ?? $cta,
                'image1_b64'          => $slide['image1_b64'] ?? null,
            ],
            'chantier' => [
                'tag' => $tag, 'headline' => $title, 'caption' => $body,
                'image_url' => $slide['image1_b64'] ?? null,
            ],
            'promo' => [
                'tag' => $tag, 'headline' => $title, 'discount' => $slide['discount'] ?? '', 'subheadline' => $body, 'cta' => $cta,
            ],
            'focus' => [
                'tag'         => $tag,
                'title'       => $title,
                'description' => $body,
                'benefit'     => $slide['benefit'] ?? '',
                'image_url'   => $slide['image1_b64'] ?? null,
            ],
            'showcase' => [
                'tag'       => $tag,
                'caption'   => $slide['instagram_caption'] ?? '',
                'image_url' => $slide['image1_b64'] ?? null,
                'post_url'  => $slide['image2_b64'] ?? null,
            ],
            default => $slide,
        };
    }

    // ── Fill helpers ──────────────────────────────────────────────────────────

    private function fillFields(array $data): void
    {
        $data = $this->recursiveDecode($data);

        // Robust Extraction: check root first, with fallback keys
        $rawCaption = $data['instagram_caption'] ?? 
                      $data['caption'] ?? 
                      $data['légende'] ?? 
                      $data['description'] ?? 
                      $data['text'] ?? '';
        
        $this->caption = $this->normalizeCaption($rawCaption);

        // Story templates never support carousel – flatten to first slide if AI ignores the rule
        if (str_starts_with($this->template, 'story-') && isset($data['slides']) && is_array($data['slides'])) {
            $first = $data['slides'][0] ?? [];
            $data = array_merge($first, ['instagram_caption' => $rawCaption ?: ($first['instagram_caption'] ?? '')]);
            unset($data['slides']);
        }

        // Handle Carousel (Multi-Slide) response
        if (isset($data['slides']) && is_array($data['slides'])) {
            $this->slides = $data['slides'];
            $this->currentSlideIndex = 0;
            
            // Hardening: check for caption inside the first slide if missing at root
            if (empty($this->caption)) {
                $this->caption = $this->slides[0]['instagram_caption'] ?? $this->slides[0]['caption'] ?? '';
            }

            // Fill the first slide fields for immediate preview
            $this->fillSlide($this->slides[0]);
        } else {
            $this->slides = [];
            // Handle Single-Slide response (legacy)
            match ($this->template) {
                'annonce'     => $this->fillAnnonce($data),
                'conseil'     => $this->fillConseil($data),
                'stats'       => $this->fillStats($data),
                'cta'         => $this->fillCta($data),
                'temoignage', 'story-temoignage' => $this->fillTemoignage($data),
                'avantages'   => $this->fillAvantages($data),
                'question'    => $this->fillQuestion($data),
                'comparaison' => $this->fillComparaison($data),
                'avantapres'  => $this->fillAvantapres($data),
                'appmockup'   => $this->fillAppmockup($data),
                'educatif'    => $this->fillEducatif($data),
                'hero'        => $this->fillHero($data),
                'article-blog', 'story-article-blog' => $this->fillArticleBlog($data),
                'feature-app'  => $this->fillFeatureApp($data),
                'promo'        => $this->fillPromo($data),
                'chantier'     => $this->fillChantier($data),
                'focus'        => $this->fillFocus($data),
                'showcase'     => $this->fillShowcase($data),
            };
        }
    }

    public function nextSlide(): void
    {
        if ($this->currentSlideIndex < count($this->slides) - 1) {
            $this->currentSlideIndex++;
            $this->fillSlide($this->slides[$this->currentSlideIndex]);
        }
    }

    public function prevSlide(): void
    {
        if ($this->currentSlideIndex > 0) {
            $this->currentSlideIndex--;
            $this->fillSlide($this->slides[$this->currentSlideIndex]);
        }
    }

    private function fillSlide(array $slide): void
    {
        // Source of Truth
        $title = $slide['title'] ?? $slide['headline'] ?? $slide['tip_title'] ?? $slide['stat_label'] ?? '';
        $body  = $slide['body']  ?? $slide['subheadline'] ?? $slide['tip_body'] ?? $slide['stat_description'] ?? '';
        $tag   = $slide['tag']   ?? $slide['label'] ?? '';
        $cta   = $slide['cta']   ?? '';

        // Auto-Numbering and Tag Hardening
        if (empty($tag)) {
            $tag = match($this->template) {
                'annonce'   => 'À NE PAS MANQUER',
                'avantages' => 'POINT CLÉ',
                'conseil'   => 'CONSEIL PRO',
                default      => 'INFO',
            };
        }
        $autoNumber = str_pad($this->currentSlideIndex + 1, 2, '0', STR_PAD_LEFT);
        $total = count($this->slides);
        $this->show_swipe = ($total > 1 && $this->currentSlideIndex < $total - 1);

        $this->tag         = $tag;
        $this->headline    = $title;
        $this->subheadline = $body;
        $this->cta         = $cta;
        $this->number      = $slide['number'] ?? $autoNumber;
        $this->tip_title   = $title; 
        $this->tip_body    = $body;
        $this->items       = $slide['items'] ?? [];
        $this->col_sans    = $slide['col_sans'] ?? [];
        $this->col_avec    = $slide['col_avec'] ?? [];
        $this->col_sans    = $slide['col_sans'] ?? ['SANS Postelio'];
        $this->col_avec    = $slide['col_avec'] ?? ['AVEC Postelio'];

        // Template-specific naming overrides
        $this->cta_hook           = $title;
        $this->avantages_headline = $title;
        $this->avantages_tag      = $tag;
        $this->avantages_body     = $body;
        $this->question           = $title; 
        $this->question_teaser    = $body;
        $this->question_tag       = $tag;
        $this->comparaison_headline = $title;
        $this->avantapres_headline = $title;
        $this->avantapres_tag = $tag;
        $this->appmockup_tag = $tag;
        $this->appmockup_headline = $title;
        $this->appmockup_subheadline = $body;
        $this->educatif_tag = $tag;
        $this->educatif_title = $title;
        $this->educatif_body = $body;
        $this->educatif_insight = $slide['insight'] ?? '';
        $this->hero_tag = $tag;
        $this->hero_headline = $title;
        $this->hero_body = $body;
        
        // For stats
        $this->stat = $slide['stat'] ?? $this->stat;
        $this->stat_label = $title;
        $this->stat_description = $body;
        $this->insight = $slide['insight'] ?? $this->insight;

        // For chantier
        $this->photo_tag = $tag;
        $this->photo_headline = $title;
        $this->photo_caption = $body;

        // For feature-app
        $this->feature_name        = $slide['feature_name']        ?? $title;
        $this->feature_description = $slide['feature_description'] ?? $body;
        $this->feature_cta         = $slide['feature_cta']         ?? $cta;

        // For promo
        $this->promo_tag         = $tag;
        $this->promo_headline    = $title;
        $this->promo_discount    = $slide['discount'] ?? '';
        $this->promo_subheadline = $body;
        $this->promo_cta         = $cta;

        // For focus
        $this->focus_tag         = $tag;
        $this->focus_title       = $title;
        $this->focus_description = $body;
        $this->focus_benefit     = $slide['benefit'] ?? $this->focus_benefit;

        // For showcase
        $this->showcase_tag      = $tag;
    }

    private function fillShowcase(array $d): void
    {
        $this->showcase_tag     = $d['tag']     ?? 'RÉSULTAT FINAL';
        $this->showcase_caption = $d['caption'] ?? '';
    }

    private function fillFocus(array $d): void
    {
        $this->focus_tag         = $d['tag']         ?? 'FOCUS SOLUTION';
        $this->focus_title       = $d['title']       ?? $d['headline'] ?? '';
        $this->focus_description = $d['body']        ?? $d['description'] ?? '';
        $this->focus_benefit     = $d['benefit']     ?? '';
    }

    private function fillChantier(array $d): void
    {
        $this->photo_tag      = $d['tag']      ?? 'RÉALISATION';
        $this->photo_headline = $d['title']    ?? $d['headline'] ?? '';
        $this->photo_caption  = $d['body']     ?? $d['caption']  ?? '';
    }

    private function fillAnnonce(array $d): void
    {
        $this->tag         = !empty($d['tag']) ? $d['tag'] : 'NOUVEAUTÉ';
        $this->headline    = $d['title']       ?? $d['headline'] ?? '';
        $this->subheadline = $d['body']        ?? $d['subheadline'] ?? '';
        $this->cta         = $d['cta']         ?? 'En savoir plus →';
    }

    private function fillConseil(array $d): void
    {
        $this->number    = $d['number']    ?? '01';
        $this->tip_title = $d['tip_title'] ?? '';
        $this->tip_body  = $d['tip_body']  ?? '';
        $this->hashtags  = $d['hashtags']  ?? '#artisan #instagram #conseil';
    }

    private function fillStats(array $d): void
    {
        $this->stat             = $d['stat']             ?? '';
        $this->stat_label       = $d['stat_label']       ?? $d['title'] ?? '';
        $this->stat_description = $d['stat_description'] ?? $d['body']  ?? '';
        $this->insight          = $d['insight']          ?? '';
    }

    private function fillCta(array $d): void
    {
        $this->cta_hook    = $d['title']       ?? $d['hook'] ?? '';
        $this->description = $d['body']       ?? $d['description'] ?? '';
        $this->cta         = $d['cta']        ?? 'Commencer gratuitement →';
        $this->url         = $d['url']        ?? 'postelio.fr';
    }

    private function fillTemoignage(array $d): void
    {
        $this->quote      = $d['quote'] ?? $d['body'] ?? $d['title'] ?? '';
        $this->author     = $d['author']     ?? 'Marc D.';
        $this->profession = $d['profession'] ?? 'Artisan';
        $this->stars      = (int) ($d['stars'] ?? 5);
    }

    private function fillAvantages(array $d): void
    {
        $this->avantages_headline = $d['title']    ?? $d['headline'] ?? '';
        $this->avantages_tag      = $d['tag']      ?? 'POINTS FORTS';
        $this->avantages_body     = $d['body']     ?? '';
        $items = (array) ($d['items'] ?? []);
        $this->items = array_pad(array_slice($items, 0, 4), 4, '');
    }

    private function fillQuestion(array $d): void
    {
        $this->question        = $d['title'] ?? $d['question'] ?? '';
        $this->question_teaser = $d['body']  ?? $d['teaser']   ?? '';
        $this->question_tag    = $d['tag']   ?? 'VOTRE AVIS ?';
    }

    private function fillComparaison(array $d): void
    {
        $this->comparaison_headline = $d['headline'] ?? 'La différence est réelle.';
        $this->col_sans             = array_slice((array) ($d['col_sans'] ?? []), 0, 5);
        $this->col_avec             = array_slice((array) ($d['col_avec'] ?? []), 0, 5);
    }

    private function fillAvantapres(array $d): void
    {
        $this->avantapres_headline = $d['headline'] ?? 'Transformation complète';
        $this->avantapres_tag      = $d['tag']      ?? 'AVANT / APRÈS';
    }

    private function fillAppmockup(array $d): void
    {
        $this->appmockup_tag         = $d['tag']         ?? 'EN ACTION';
        $this->appmockup_headline    = $d['headline']    ?? '';
        $this->appmockup_subheadline = $d['subheadline'] ?? '';
    }

    private function fillEducatif(array $d): void
    {
        $this->educatif_tag     = $d['tag']     ?? 'POINT EXPERT';
        $this->educatif_title   = $d['title']   ?? '';
        $this->educatif_body    = $d['body']    ?? '';
        $this->educatif_insight = $d['insight'] ?? 'Leçon du jour';
    }

    private function fillHero(array $data): void
    {
        $this->hero_tag      = $data['tag']      ?? 'OFFICIEL';
        $this->hero_headline = $data['title']    ?? $data['headline'] ?? '';
        $this->hero_body     = $data['body']     ?? '';
    }

    private function fillArticleBlog(array $d): void
    {
        $this->article_blog_tag     = $d['tag']     ?? 'NOUVEL ARTICLE';
        $this->article_blog_title   = $d['title']   ?? $d['headline'] ?? '';
        $this->article_blog_excerpt = $d['excerpt'] ?? $d['body']     ?? '';
        $this->article_blog_cta     = $d['cta']     ?? 'Lire l\'article';
        $this->article_blog_url     = $d['url']     ?? 'postelio.fr/blog';
    }

    private function fillFeatureApp(array $d): void
    {
        $this->feature_tag         = $d['feature_tag']         ?? $d['tag']   ?? 'FONCTIONNALITÉ';
        $this->feature_name        = $d['feature_name']        ?? $d['title'] ?? '';
        $this->feature_description = $d['feature_description'] ?? $d['body']  ?? '';
        $this->feature_cta         = $d['feature_cta']         ?? $d['cta']   ?? 'Essayer gratuitement';
    }

    private function fillPromo(array $d): void
    {
        $this->promo_tag         = $d['tag']      ?? 'OFFRE LIMITÉE';
        $this->promo_discount    = $d['discount'] ?? '';
        $this->promo_subheadline = $d['body']     ?? $d['subheadline'] ?? '';
        $this->promo_cta         = $d['cta']      ?? 'En profiter';

        $headline = $d['title'] ?? $d['headline'] ?? '';
        
        // Anti-redundancy: if the headline contains the discount string, try to remove it
        if (!empty($this->promo_discount) && !empty($headline)) {
            $discountClean = preg_quote($this->promo_discount, '/');
            $newHeadline = trim(preg_replace('/\b' . $discountClean . '\b/i', '', $headline));
            
            // Also clean common leading/trailing separators like " : " or " - "
            $newHeadline = trim($newHeadline, " \t\n\r\0\x0B:-");
            
            if (!empty($newHeadline)) {
                $headline = $newHeadline;
            }
        }
        
        $this->promo_headline = $headline;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function normalizeCaption(string $caption): string
    {
        if (empty($caption)) return '';

        // 1. Normalisation initiale
        $caption = str_replace(["\r\n", "\r"], "\n", $caption);

        // 2. Découpage par phrase pour les paragraphes denses de l'accroche
        $caption = preg_replace('/(?<=[.!?])\s+(?=[A-ZÀ-ÖØ-Þ])/u', "\n\n", $caption);

        // 3. Identification et regroupement des hashtags
        $lines = explode("\n", $caption);
        $cleanLines = [];
        $hashtags = [];
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            // Extraire les hashtags (qu'ils soient seuls ou collés)
            if (str_contains($trimmed, '#')) {
                // Si la ligne contient du texte avant le #, on garde le texte et on extrait le #
                if (preg_match_all('/#\w+/u', $trimmed, $matches)) {
                    foreach ($matches[0] as $tag) {
                        $hashtags[] = $tag;
                    }
                    $textBefore = trim(preg_replace('/#\w+/u', '', $trimmed));
                    if (!empty($textBefore)) {
                        $cleanLines[] = $textBefore;
                    }
                } else {
                    $cleanLines[] = $trimmed;
                }
            } else {
                $cleanLines[] = $trimmed;
            }
        }

        // 4. Reconstruction avec aération forcée
        $finalLines = [];
        $currentBuffer = "";

        foreach ($cleanLines as $i => $line) {
            if (empty($currentBuffer)) {
                $currentBuffer = $line;
                continue;
            }

            // Si la ligne commence par une minuscule, c'est une suite de phrase : on fusionne avec un espace
            if (preg_match('/^[a-zà-ÿ0-9]/u', $line) && !str_starts_with($line, '✅') && $currentBuffer !== '—' && $line !== '—') {
                $currentBuffer .= ' ' . $line;
            } else {
                // Sinon, c'est un nouveau bloc
                $finalLines[] = $currentBuffer;

                // On décide si on met un saut de ligne double (paragraphe) ou simple
                $skipBlank = (
                    (str_contains($currentBuffer, 'Postelio |') && !str_starts_with($line, '✅')) || // Titre vers baseline
                    (str_starts_with($currentBuffer, '✅') && str_starts_with($line, '✅')) ||       // Liste de checkmarks
                    $currentBuffer === '—' || // Après le tiret
                    $line === '—'          // Avant le tiret
                );

                if (!$skipBlank) {
                    $finalLines[] = ""; // Saut de ligne double (paragraphe)
                }

                $currentBuffer = $line;
            }
        }
        
        if (!empty($currentBuffer)) {
            $finalLines[] = $currentBuffer;
        }

        // Ajouter les hashtags regroupés à la fin
        if (!empty($hashtags)) {
            $finalLines[] = ""; // Espace avant les hashtags
            $finalLines[] = implode(' ', array_unique($hashtags));
        }

        $caption = implode("\n", $finalLines);

        // 5. Nettoyage final
        $caption = preg_replace("/\n{3,}/", "\n\n", $caption);

        return trim($caption);
    }

    private function fileToDataUri($file): string
    {
        $mime = $file->getMimeType();
        $data = base64_encode(file_get_contents($file->getRealPath()));
        return "data:{$mime};base64,{$data}";
    }

    // ── AI Prompt builders ────────────────────────────────────────────────────

    private function buildSystemPrompt(): string
    {
        $toneDesc = match ($this->tone) {
            'professionnel' => 'expert, persuasif et sérieux',
            'accessible'    => 'chaleureux, encourageant et complice',
            'dynamique'     => 'très direct, percutant et axé sur les résultats',
            'inspirant'     => 'ambitieux, visionnaire et motivant',
            default         => 'professionnel',
        };

        $templateDesc = match ($this->template) {
            'annonce' => <<<PROMPT
                Post "Annonce". CARROUSEL OPTIONNEL (2-4 slides). 
                JSON : { "tag": "...", "title": "...", "body": "...", "cta": "...", "instagram_caption": "..." }
                PROMPT,
            'conseil' => <<<PROMPT
                Post "Conseil". CARROUSEL CONSEILLÉ (3-5 slides).
                JSON : { "title": "...", "body": "...", "tag": "...", "instagram_caption": "..." }
                PROMPT,
            'avantages' => <<<PROMPT
                Post "Avantages / Point Clé". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Structure JSON : { "tag": "POINT CLÉ", "title": "...", "items": ["Point 1 (7-10 mots)", "Point 2", "Point 3", "Point 4"], "body": "Développement si besoin (15-20 mots)", "instagram_caption": "..." }
                RÈGLE : La liste "items" doit TOUJOURS contenir exactement 4 points percutants.
                PROMPT,
            'comparaison' => <<<PROMPT
                Post "Comparaison". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Structure JSON : { "title": "...", "col_sans": ["Point 1", "Point 2", "Point 3", "Point 4", "Point 5"], "col_avec": ["Avantage 1", "Avantage 2", "Avantage 3", "Avantage 4", "Avantage 5"], "instagram_caption": "..." }
                PROMPT,
            'stats' => <<<PROMPT
                Post "Stats". IMAGE UNIQUE (Interdit : Pas de carrousel).
                OBJECTIF : La suite [Stat] + [Sujet] + [Action] doit être une phrase française fluide et naturelle.
                JSON : { 
                    "stat": "3x", 
                    "title": "PLUS D'ATTENTION RETENUE", 
                    "body": "pour les comptes pros avec une identité visuelle cohérente.", 
                    "insight": "Votre image pro attire et captive.", 
                    "instagram_caption": "..." 
                }
                RÈGLE D'OR : Le [Sujet] (title) doit compléter le [Stat] immédiatement.
                - BON : [30%] [DE SIGNATURES EN PLUS] [grâce à une présence régulière.]
                - BON : [87%] [DES CLIENTS] [vérifient votre profil avant de signer.]
                - MAUVAIS : [30%] [POSTER RÉGULIÈREMENT] [AUGMENTE vos devis.] (Phrase brisée)
                RÈGLE : L'insight doit être une conclusion courte (5-7 mots).
                CONSEIL : Sois concret pour un artisan (ex: "votre planning sera complet").
                PROMPT,
            'cta' => <<<PROMPT
                Post "Vente". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                JSON : { "title": "Accroche", "body": "Description", "cta": "Bouton", "url": "postelio.fr", "instagram_caption": "..." }
                PROMPT,
            'temoignage' => <<<PROMPT
                Post "Avis". IMAGE UNIQUE (Interdit : Pas de carrousel).
                JSON : { "quote": "...", "author": "...", "profession": "...", "stars": 5, "instagram_caption": "..." }
                PROMPT,
            'story-temoignage' => <<<PROMPT
                Story Instagram "Avis client" (format 9:16 portrait). IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Objectif : mettre en valeur un témoignage client percutant en story Instagram.
                JSON : { "quote": "Citation courte et impactante (50-120 car. max)", "author": "Prénom N.", "profession": "Métier — Ville", "stars": 5, "instagram_caption": "..." }
                RÈGLE : La citation doit être courte, émotionnelle et crédible. Pas de jargon marketing.
                PROMPT,
            'question' => <<<PROMPT
                Post "Question". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Structure JSON : { "title": "La question posée (6-10 mots)", "body": "Le contexte ou l'amorce (12-15 mots)", "tag": "...", "instagram_caption": "..." }
                PROMPT,
            'educatif' => <<<PROMPT
                Post "Éducatif". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                JSON : { "tag": "DÉCRYPTAGE", "title": "Le saviez-vous ?", "body": "Développement pédagogique (25-35 mots, utilise des listes à puces si besoin, aéré)", "insight": "La leçon clé à retenir (12-18 mots)", "instagram_caption": "..." }
                RÈGLE : Ni trop court, ni trop long. Remplis bien l'espace visuel sans déborder.
                PROMPT,
            'hero' => <<<PROMPT
                Post "Branding / Lancement". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Design minimaliste, centré, avec LOGO au top.
                JSON : { "tag": "Badge court (2-3 mots)", "title": "Headline impactante (5-8 mots)", "body": "Message brandé (12-18 mots)", "instagram_caption": "..." }
                RÈGLE : Le "tag" doit être un badge court et cohérent avec le titre (ex: "NOUVEAU", "EXCELLENCE", "VOTRE ALLIÉ").
                PROMPT,
            'avantapres' => <<<PROMPT
                Post "Avant/Après". IMAGE UNIQUE (Interdit : Pas de carrousel).
                JSON : { "title": "Texte", "tag": "...", "instagram_caption": "..." }
                PROMPT,
            'appmockup' => <<<PROMPT
                Post "Démo App". IMAGE UNIQUE (Interdit : Pas de carrousel).
                JSON : { "title": "Titre", "body": "Sous-titre", "tag": "...", "instagram_caption": "..." }
                PROMPT,
            'article-blog' => <<<PROMPT
                Post "Promotion d'article de blog". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Objectif : donner envie de lire l'article en mettant en avant son titre et un extrait accrocheur.
                JSON : {
                    "tag": "NOUVEL ARTICLE",
                    "title": "Titre de l'article (tel quel ou légèrement simplifié, max 65 car.)",
                    "excerpt": "Accroche courte qui résume la valeur de l'article (25-40 mots, sans spoiler la conclusion)",
                    "cta": "Lire l'article",
                    "url": "postelio.fr/blog",
                    "instagram_caption": "Caption engageante avec le lien en bio, 2-3 hashtags pertinents"
                }
                RÈGLE : Le titre doit rester fidèle à l'article. L'extrait doit créer de la curiosité sans être vague.
                PROMPT,
            'story-article-blog' => <<<PROMPT
                Story Instagram "Annonce d'article de blog" (format 9:16 portrait). IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Objectif : capter l'attention en 2 secondes et donner envie de swipe up / lien en bio.
                JSON : {
                    "tag": "NOUVEL ARTICLE",
                    "title": "Titre percutant (max 55 car., direct et fort)",
                    "excerpt": "Accroche ultra-courte (15-25 mots max) qui intrigue sans révéler la conclusion",
                    "cta": "Lire l'article",
                    "url": "postelio.fr/blog",
                    "instagram_caption": "Caption courte avec emoji, lien en bio, 2-3 hashtags"
                }
                RÈGLE : Le titre doit être encore plus court et percutant que pour un post classique. L'extrait est facultatif mais très efficace.
                PROMPT,
            'feature-app' => <<<PROMPT
                Post "Mise en avant d'une fonctionnalité Postelio". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Objectif : présenter UNE fonctionnalité précise de Postelio avec son bénéfice concret pour l'artisan.
                JSON : {
                    "feature_tag": "FONCTIONNALITÉ" (ou "NOUVEAUTÉ", "EN ACTION", "ASTUCE PRO" selon le contexte),
                    "feature_name": "Nom court et mémorable de la fonctionnalité (3-6 mots, ex: 'Générateur de Posts IA')",
                    "feature_description": "Bénéfice concret pour l'artisan (20-30 mots). Commence par un verbe d'action. Termine par un bénéfice chiffré si possible.",
                    "feature_cta": "Texte du bouton (2-4 mots percutants)",
                    "instagram_caption": "..."
                }
                RÈGLE : feature_name doit donner envie de cliquer. feature_description doit parler RÉSULTAT, pas technicité.
                PROMPT,
            'promo' => <<<PROMPT
                Post "Promotion". FONDS ORANGE/JAUNE BRANDING. IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Objectif : annoncer une promotion exceptionnelle sur le site.
                JSON : {
                    "tag": "OFFRE LIMITÉE" (ou "VENTE FLASH", "PROMO EXCLUSIVE"),
                    "title": "Headline attractive (4-7 mots). INTERDICTION : Ne pas inclure le pourcentage ici car il sera affiché en GROS via le champ 'discount'.",
                    "discount": "Pourcentage de remise (ex: -30%, -50%, Jusqu'à -40%)",
                    "body": "Sous-titre explicatif sur la durée ou la nature de l'offre (10-15 mots)",
                    "cta": "Texte du bouton (ex: Voir l'offre, J'en profite)",
                    "instagram_caption": "..."
                }
                RÈGLE : PAS DE CODE PROMO. L'offre est directe sur le site.
                RÈGLE D'OR : Le 'title' doit être un bénéfice ou une accroche (ex: 'Équipez-vous pour le printemps', 'Boostez votre visibilité'), PAS la valeur de la remise.
                PROMPT,
            'chantier' => <<<PROMPT
                Post "Photo de chantier / Réalisation". IMAGE UNIQUE OBLIGATOIRE (Interdit : Pas de carrousel).
                Objectif : montrer concrètement le travail de l'artisan sur le terrain.
                JSON : {
                    "tag": "RÉALISATION" (ou "CHANTIER EN COURS", "TRANSFORMATION"),
                    "title": "Titre du projet (4-6 mots, ex: 'Rénovation sol à Paris')",
                    "caption": "Détails sur l'intervention (15-20 mots). Doit valoriser le savoir-faire.",
                    "instagram_caption": "..."
                }
                RÈGLE : Le ton doit être fier et professionnel.
                PROMPT,
            'focus' => <<<PROMPT
                Post "Focus Postelio (Expertise)". POINT DE VUE : Postelio s'adresse à l'artisan ou à son client.
                Objectif : montrer comment Postelio apporte une solution concrète via un exemple métier.
                JSON : {
                    "tag": "FOCUS SOLUTION" (ou "L'OEIL POSTELIO", "RÉSULTAT PRO"),
                    "title": "Titre orienté succès (4-6 mots, ex: '3h gagnées par jour')",
                    "description": "Explication sur comment Postelio a aidé (15-20 mots).",
                    "benefit": "Un chiffre ou bénéfice court (ex: '+40% de visibilité')",
                    "instagram_caption": "..."
                }
                RÈGLE : Le narrateur est Postelio. Le ton est expert, visionnaire et rassurant.
                PROMPT,
            'showcase' => <<<PROMPT
                Post "Showcase Postelio". POINT DE VUE : Postelio montre un exemple de réussite.
                Objectif : Présenter un post généré par l'IA de façon premium.
                JSON : {
                    "tag": "RÉSULTAT FINAL" (ou "DÉMO PRO", "AVANT / APRÈS IA"),
                    "title": "Titre accrocheur (ex: 'Un post pro en 10s')",
                    "instagram_caption": "..."
                }
                RÈGLE : Le ton est fier et technologique.
                PROMPT,
            default => '',
        };

        return <<<PROMPT
            Tu es l'assistant marketing personnel des ARTISANS et des PROS DU BÂTIMENT (menuisiers, plombiers, électriciens, peintres, paysagistes, etc.).
            Ton unique mission : convaincre ces artisans que Postelio transforme leur Instagram en une machine à signer des chantiers sérieux, sans qu'ils y passent leurs soirées.

            CONIGNE DE TON (CRITICAL) :
            - Langage TERRAIN : Parle de "sciure", "d'outillage", de "devis", de "finitions", de "réception de chantier".
            - PAS DE "BULLSHIT CORPORATE" : Évite les termes comme "opportunité stratégique", "synergie", "écosystème".
            - EMPATHIE : Tu comprends qu'ils sont fatigués le soir, qu'ils aiment leur métier mais détestent les réseaux sociaux.
            - VALEUR : Tout doit tourner autour de : Gagner du temps, paraître pro, et attirer des clients qui ne négocient pas les prix.

            Ton ton général est $toneDesc.

            Règles de STRUCTURE (CRITICAL) :
            - Réponds UNIQUEMENT en JSON valide.
            - POLITIQUES DE SLIDES :
                - SI stats, cta, temoignage, question, avantapres, appmockup, comparaison, educatif, feature-app, article-blog, story-article-blog, story-temoignage, promo, chantier, focus, showcase : INTERDIT DE FAIRE UN CARROUSEL. Réponds avec un seul objet JSON simple à la racine.
                - SI conseil, annonce, avantages : CARROUSEL AUTORISÉ (tableau "slides": [ ... ]).
            - "title" : Titre principal (4-8 mots). OBLIGATOIRE.
            - "body" : Texte descriptif (15-25 mots).
            - "tag" : Petit label en MAJUSCULES (1-3 mots).
            - "cta" : Texte du bouton (2-4 mots). Obligatoire si demandé dans la structure.
            - PAS D'ENTITÉS HTML (pas de &#039;). Utilise des caractères normaux.
            - TOUTES LES VALEURS DOIVENT ÊTRE REMPLIES. PAS DE VALEURS VIDES.
            - AÉRE TON TEXTE DE MANIÈRE EXTRÊME (CRITICAL) : Utilise systématiquement la séquence de caractères \\n\\n (double antislash n) entre CHAQUE idée. 
            - NE METS JAMAIS de vrais sauts de ligne physiques à l'intérieur des valeurs du JSON.
            - Chaque point d'une liste doit être sur sa propre ligne (en utilisant \\n).
            - SI Comparaison : Les listes "col_sans" et "col_avec" doivent avoir EXACTEMENT 5 points percutants.
            - "instagram_caption" (CRITICAL) : Toujours présent à la racine du JSON.
                STRUCTURE OBLIGATOIRE avec la séquence \\n\\n entre chaque bloc :

                [ACCROCHE] — 1 phrase forte, seule sur sa ligne.

                [DÉVELOPPEMENT] — 2 à 3 phrases MAX. Chaque phrase sur sa propre ligne, séparées par \n\n. Jamais plus de 1 phrase par paragraphe.

                [SIGNATURE] — Copie-colle exactement ce bloc, précédé de \n\n—\n :
                —
                Postelio | L'assistant digital des pros du bâtiment.
                Mettez en valeur votre savoir-faire technique, sans y passer vos soirées.
                ✅ 30 secondes chrono depuis l'utilitaire
                ✅ Une vitrine premium qui prouve votre niveau
                ✅ Des clients sérieux qui ne négocient plus les prix

                👉 Testez gratuitement dès maintenant sur Postelio.fr

                [HASHTAGS] — Précédés de \n\n. Une dizaine de hashtags très utilisés par les artisans (ex: #menuiserie, #plomberie, #renovation, #artisanat, #chantier, #btp, #travaux, #deco, #outillage, #amenagement) et TOUJOURS finir par #Postelio.

                EXEMPLE DE RENDU ATTENDU (respecte exactement cette aération très large) :
                "Tu travailles dur. Mais est-ce que tes clients le voient vraiment ?\n\nUn artisan invisible sur les réseaux laisse des chantiers aux concurrents.\n\nPostelio te donne une vitrine pro en 30 secondes.\n\n—\nPostelio | L'assistant digital des pros du bâtiment.\n...\n\n#btp #artisan #renovation #Postelio"

            $templateDesc
PROMPT;
    }

    private function buildUserPrompt(): string
    {
        return "Sujet du post : {$this->topic}\n\nPense à un HOOK très fort et un CTA vers Postelio. Génère le JSON maintenant.";
    }

    private function recursiveDecode(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->recursiveDecode($value);
            }
            return $data;
        }

        if (is_string($data)) {
            $prev = '';
            $curr = $data;
            // Multi-pass decode to handle double/triple encoding
            for ($i = 0; $i < 3; $i++) {
                $curr = html_entity_decode($curr, ENT_QUOTES, 'UTF-8');
            }
            return $curr;
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.admin.instagram-post-generator');
    }
}
