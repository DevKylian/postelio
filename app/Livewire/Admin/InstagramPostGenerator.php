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

    // ── Editable fields: educatif ─────────────────────────────────────────────
    public string $educatif_tag     = '';
    public string $educatif_title   = '';
    public string $educatif_body    = '';
    public string $educatif_insight = '';

    // ── Editable fields: hero ─────────────────────────────────────────────────
    public string $hero_tag      = '';
    public string $hero_headline = '';
    public string $hero_body     = '';

    // ── Editable fields: chantier ─────────────────────────────────────────────
    public string $photo_tag      = 'RÉALISATION';
    public string $photo_headline = '';
    public string $photo_caption  = '';

    // ── Image-based templates ─────────────────────────────────────────────────
    private const IMAGE_TEMPLATES = ['avantapres', 'appmockup'];

    public function mount(): void
    {
        $this->history = InstagramGeneratedPost::where('user_id', auth()->id())
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn($item) => [
                'id'       => $item->id,
                'template' => $item->template,
                'topic'    => $item->topic,
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
        $this->col_sans = ['', '', '', ''];
        $this->col_avec = ['', '', '', ''];
        $this->avantapres_tag = $this->avantapres_headline = '';
        $this->educatif_tag = $this->educatif_title = $this->educatif_body = $this->educatif_insight = '';
        $this->hero_tag = $this->hero_headline = $this->hero_body = '';
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
            'template' => 'required|in:annonce,conseil,stats,cta,temoignage,avantages,question,comparaison,avantapres,appmockup,educatif,hero',
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

            $data = json_decode(trim($response->choices[0]->message->content), true);

            if (!is_array($data)) {
                throw new \RuntimeException('Réponse AI invalide.');
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
        $this->topic    = $item['topic'];
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
            'temoignage' => [
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
            'chantier' => [
                'tag'      => $this->photo_tag,
                'headline' => $this->photo_headline,
                'caption'  => $this->photo_caption,
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

                    $png = Browsershot::html($html)
                        ->windowSize(360, 450)
                        ->deviceScaleFactor(3)
                        ->clip(0, 0, 360, 450)
                        ->noSandbox()
                        ->waitUntilNetworkIdle()
                        ->delay(500)
                        ->timeout(60)
                        ->screenshot();

                    $zip->addFromString('slide-' . ($i + 1) . '.png', $png);
                }

                $zip->close();
                return response()->download($zipFileName)->deleteFileAfterSend(true);
            }

            // Case 2: Single Slide (Regular)
            $data = $this->buildDataArray();
            if ($this->uploadImage1) $data['image1_b64'] = $this->fileToDataUri($this->uploadImage1);
            if ($this->uploadImage2) $data['image2_b64'] = $this->fileToDataUri($this->uploadImage2);

            $html = view('render.postelio-instagram', [
                'template' => $this->template,
                'data'     => $data,
            ])->render();

            $png = Browsershot::html($html)
                ->windowSize(360, 450)
                ->deviceScaleFactor(3)
                ->clip(0, 0, 360, 450)
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->delay(500)
                ->timeout(60)
                ->screenshot();

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
                'col_sans' => !empty($slide['col_sans']) ? $slide['col_sans'] : ['Ancien système', 'Perte de temps', 'Compliqué'],
                'col_avec' => !empty($slide['col_avec']) ? $slide['col_avec'] : ['Postelio AI', 'Gain de temps', 'Intuitif'],
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
            'chantier' => [
                'tag' => $tag, 'headline' => $title, 'caption' => $body,
                'image_url' => $slide['image1_b64'] ?? null,
            ],
            default => $slide,
        };
    }

    // ── Fill helpers ──────────────────────────────────────────────────────────

    private function fillFields(array $data): void
    {
        // Robust Extraction: check root first, with fallback keys
        $this->caption = $data['instagram_caption'] ?? 
                         $data['caption'] ?? 
                         $data['légende'] ?? 
                         $data['description'] ?? 
                         $data['text'] ?? '';

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
                'temoignage'  => $this->fillTemoignage($data),
                'avantages'   => $this->fillAvantages($data),
                'question'    => $this->fillQuestion($data),
                'comparaison' => $this->fillComparaison($data),
                'avantapres'  => $this->fillAvantapres($data),
                'appmockup'   => $this->fillAppmockup($data),
                'educatif'    => $this->fillEducatif($data),
                'hero'        => $this->fillHero($data),
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
        $this->quote      = $d['quote']      ?? '';
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
        $this->col_sans             = array_slice((array) ($d['col_sans'] ?? []), 0, 4);
        $this->col_avec             = array_slice((array) ($d['col_avec'] ?? []), 0, 4);
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
        $this->hero_tag      = $data['tag'] ?? 'OFFICIEL';
        $this->hero_headline = $data['headline'] ?? '';
        $this->hero_body     = $data['body'] ?? '';
    }

    private function fillChantier(array $data): void
    {
        $this->photo_tag      = $data['tag'] ?? 'RÉALISATION';
        $this->photo_headline = $data['headline'] ?? '';
        $this->photo_caption  = $data['caption'] ?? '';
    }

    // ── Image helper ──────────────────────────────────────────────────────────

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
                Structure JSON : { "title": "...", "col_sans": ["Point 1", "Point 2", "Point 3"], "col_avec": ["Avantage 1", "Avantage 2", "Avantage 3"], "instagram_caption": "..." }
                PROMPT,
            'stats' => <<<PROMPT
                Post "Stats". IMAGE UNIQUE (Interdit : Pas de carrousel).
                OBJECTIF : La suite [Stat] + [Sujet] + [Action] doit être une phrase française fluide et naturelle.
                JSON : { 
                    "stat": "30%", 
                    "title": "DE DEVIS SIGNÉS EN PLUS", 
                    "body": "pour les artisans qui publient 2 fois par semaine.", 
                    "insight": "Ne laissez plus vos chantiers aux concurrents.", 
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
                Design minimaliste, centré, avec LOGO au top (Pas de tag texte). 
                JSON : { "title": "Headline (5-8 mots)", "body": "Message brandé (12-18 mots)", "instagram_caption": "..." }
                PROMPT,
            'avantapres' => <<<PROMPT
                Post "Avant/Après". IMAGE UNIQUE (Interdit : Pas de carrousel).
                JSON : { "title": "Texte", "tag": "...", "instagram_caption": "..." }
                PROMPT,
            'appmockup' => <<<PROMPT
                Post "Démo App". IMAGE UNIQUE (Interdit : Pas de carrousel).
                JSON : { "title": "Titre", "body": "Sous-titre", "tag": "...", "instagram_caption": "..." }
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
                - SI stats, cta, temoignage, question, avantapres, appmockup, comparaison, educatif : INTERDIT DE FAIRE UN CARROUSEL. Réponds avec un seul objet JSON simple à la racine.
                - SI conseil, annonce, avantages : CARROUSEL AUTORISÉ (tableau "slides": [ ... ]).
            - "title" : Titre principal (4-8 mots). OBLIGATOIRE.
            - "body" : Texte descriptif (15-25 mots).
            - "tag" : Petit label en MAJUSCULES (1-3 mots).
            - "cta" : Texte du bouton (2-4 mots). Obligatoire si demandé dans la structure.
            - PAS D'ENTITÉS HTML (pas de &#039;). Utilise des caractères normaux.
            - TOUTES LES VALEURS DOIVENT ÊTRE REMPLIES. PAS DE VALEURS VIDES.
            - AÉRE TON TEXTE (CRITICAL) : Utilise des retours à la ligne (`\n`) pour aérer le corps du texte. INTERDIT de faire des blocs de plus de 2 lignes sans saut.
            - SI Comparaison : Les listes "col_sans" et "col_avec" doivent avoir EXACTEMENT 3 ou 4 points percutants.
            - "instagram_caption" (CRITICAL) : Toujours présent à la racine du JSON.
                STRUCTURE OBLIGATOIRE :
                1. Une accroche percutante.
                2. 2-3 phrases de développement sur le sujet.
                3. SIGNATURE (Copie-colle exactement ce bloc) :
                —
                Postelio | L'assistant digital des pros du bâtiment.
                Mettez en valeur votre savoir-faire technique, sans y passer vos soirées.
                ✅ 30 secondes chrono depuis l'utilitaire
                ✅ Une vitrine premium qui prouve votre niveau
                ✅ Des clients sérieux qui ne négocient plus les prix

                👉 Testez gratuitement dès maintenant sur Postelio.fr

                4. HASHTAGS (Important: Laisse au moins 2 sauts de ligne avant cette partie pour bien l'espacer du texte) :
                Une dizaine de hashtags globaux très utilisés par les artisans (ex: #menuiserie, #plomberie, #renovation, #artisanat, #chantier, #btp, #travaux, #deco, #outillage, #amenagement) et TOUJOURS finir par #Postelio.

            $templateDesc
PROMPT;
    }

    private function buildUserPrompt(): string
    {
        return "Sujet du post : {$this->topic}\n\nPense à un HOOK très fort et un CTA vers Postelio. Génère le JSON maintenant.";
    }

    public function render()
    {
        return view('livewire.admin.instagram-post-generator');
    }
}
