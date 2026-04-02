<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Spatie\Browsershot\Browsershot;

#[Layout('layouts.app')]
class InstagramPostGenerator extends Component
{
    // ── Form ──────────────────────────────────────────────────────────────────
    public string $topic    = '';
    public string $template = 'annonce';
    public string $tone     = 'professionnel';

    // ── State ─────────────────────────────────────────────────────────────────
    public bool    $generating   = false;
    public bool    $downloading  = false;
    public ?string $errorMessage = null;
    public bool    $hasContent   = false;

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
    public string $hook        = '';
    public string $description = '';
    public string $url         = 'postelio.fr';

    // ── Editable fields: temoignage ───────────────────────────────────────────
    public string $quote      = '';
    public string $author     = '';
    public string $profession = '';
    public int    $stars      = 5;

    // ── Editable fields: avantages ────────────────────────────────────────────
    public string $avantages_headline = '';
    public string $avantages_tag      = '';
    public array  $items              = [];

    // ── Editable fields: question ─────────────────────────────────────────────
    public string $question        = '';
    public string $question_teaser = '';
    public string $question_tag    = '';

    // ── Editable fields: comparaison ──────────────────────────────────────────
    public string $comparaison_headline = '';
    public array  $col_sans             = [];
    public array  $col_avec             = [];

    // ── History ───────────────────────────────────────────────────────────────
    public array $history = [];

    public function resetFields(): void
    {
        $this->tag = $this->headline = $this->subheadline = $this->cta = '';
        $this->number = $this->tip_title = $this->tip_body = $this->hashtags = '';
        $this->stat = $this->stat_label = $this->stat_description = $this->insight = '';
        $this->hook = $this->description = '';
        $this->url = 'postelio.fr';
        $this->quote = $this->author = $this->profession = '';
        $this->stars = 5;
        $this->avantages_headline = $this->avantages_tag = '';
        $this->items = [];
        $this->question = $this->question_teaser = $this->question_tag = '';
        $this->comparaison_headline = '';
        $this->col_sans = $this->col_avec = [];
        $this->hasContent   = false;
        $this->errorMessage = null;
    }

    public function updatedTemplate(): void
    {
        $this->resetFields();
    }

    public function generate(): void
    {
        $this->validate([
            'topic'    => 'required|min:5|max:300',
            'template' => 'required|in:annonce,conseil,stats,cta,temoignage,avantages,question,comparaison',
            'tone'     => 'required|in:professionnel,accessible,dynamique,inspirant',
        ]);

        $this->generating   = true;
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

            array_unshift($this->history, [
                'template' => $this->template,
                'topic'    => $this->topic,
                'data'     => $data,
                'time'     => now()->format('H:i'),
            ]);
            $this->history = array_slice($this->history, 0, 10);

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
        $this->fillFields($item['data']);
        $this->hasContent = true;
    }

    public function downloadPng(): mixed
    {
        if (!$this->hasContent) {
            return null;
        }

        $this->downloading = true;

        try {
            $html = view('render.postelio-instagram', [
                'template' => $this->template,
                'data'     => $this->buildDataArray(),
            ])->render();

            $png = Browsershot::html($html)
                ->windowSize(360, 360)
                ->deviceScaleFactor(3)
                ->clip(0, 0, 360, 360)
                ->noSandbox()
                ->timeout(60)
                ->screenshot();

            $filename = 'postelio-instagram-' . $this->template . '-' . now()->format('Ymd-His') . '.png';

            return response()->streamDownload(function () use ($png) {
                echo $png;
            }, $filename, ['Content-Type' => 'image/png']);

        } catch (\Throwable $e) {
            $this->errorMessage = 'Erreur lors du rendu PNG : ' . $e->getMessage();
            return null;
        } finally {
            $this->downloading = false;
        }
    }

    // ── Fill helpers ──────────────────────────────────────────────────────────

    private function fillFields(array $data): void
    {
        match ($this->template) {
            'annonce'     => $this->fillAnnonce($data),
            'conseil'     => $this->fillConseil($data),
            'stats'       => $this->fillStats($data),
            'cta'         => $this->fillCta($data),
            'temoignage'  => $this->fillTemoignage($data),
            'avantages'   => $this->fillAvantages($data),
            'question'    => $this->fillQuestion($data),
            'comparaison' => $this->fillComparaison($data),
        };
    }

    private function fillAnnonce(array $d): void
    {
        $this->tag         = $d['tag']         ?? 'NOUVEAUTÉ';
        $this->headline    = $d['headline']    ?? '';
        $this->subheadline = $d['subheadline'] ?? '';
        $this->cta         = $d['cta']         ?? 'Commencer gratuitement';
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
        $this->stat_label       = $d['stat_label']       ?? 'des artisans';
        $this->stat_description = $d['stat_description'] ?? '';
        $this->insight          = $d['insight']          ?? 'Et si vous étiez différent ?';
    }

    private function fillCta(array $d): void
    {
        $this->hook        = $d['hook']        ?? '';
        $this->description = $d['description'] ?? '';
        $this->cta         = $d['cta']         ?? 'Commencer gratuitement →';
        $this->url         = $d['url']         ?? 'postelio.fr';
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
        $this->avantages_headline = $d['headline'] ?? '';
        $this->avantages_tag      = $d['tag']      ?? 'POURQUOI POSTELIO ?';
        $this->items              = array_slice((array) ($d['items'] ?? []), 0, 4);
    }

    private function fillQuestion(array $d): void
    {
        $this->question        = $d['question'] ?? '';
        $this->question_teaser = $d['teaser']   ?? '';
        $this->question_tag    = $d['tag']       ?? 'BONNE QUESTION';
    }

    private function fillComparaison(array $d): void
    {
        $this->comparaison_headline = $d['headline'] ?? 'La différence est réelle.';
        $this->col_sans             = array_slice((array) ($d['col_sans'] ?? []), 0, 4);
        $this->col_avec             = array_slice((array) ($d['col_avec'] ?? []), 0, 4);
    }

    private function buildDataArray(): array
    {
        return match ($this->template) {
            'annonce' => [
                'tag' => $this->tag, 'headline' => $this->headline,
                'subheadline' => $this->subheadline, 'cta' => $this->cta,
            ],
            'conseil' => [
                'number' => $this->number, 'tip_title' => $this->tip_title,
                'tip_body' => $this->tip_body, 'hashtags' => $this->hashtags,
            ],
            'stats' => [
                'stat' => $this->stat, 'stat_label' => $this->stat_label,
                'stat_description' => $this->stat_description, 'insight' => $this->insight,
            ],
            'cta' => [
                'hook' => $this->hook, 'description' => $this->description,
                'cta' => $this->cta, 'url' => $this->url,
            ],
            'temoignage' => [
                'quote' => $this->quote, 'author' => $this->author,
                'profession' => $this->profession, 'stars' => $this->stars,
            ],
            'avantages' => [
                'headline' => $this->avantages_headline,
                'tag'      => $this->avantages_tag,
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
            default => [],
        };
    }

    // ── AI Prompt builders ────────────────────────────────────────────────────

    private function buildSystemPrompt(): string
    {
        $toneDesc = match ($this->tone) {
            'professionnel' => 'professionnel, crédible et rassurant',
            'accessible'    => 'accessible, chaleureux et bienveillant',
            'dynamique'     => 'dynamique, direct et percutant',
            'inspirant'     => 'inspirant, ambitieux et motivant',
            default         => 'professionnel',
        };

        $templateDesc = match ($this->template) {
            'annonce' => <<<PROMPT
                Post de type "Annonce / Feature". Présente une fonctionnalité ou avantage clé de Postelio.
                JSON requis :
                { "tag": "label MAJUSCULES max 20 car.", "headline": "titre percutant max 55 car.", "subheadline": "phrase descriptive max 100 car.", "cta": "texte bouton max 25 car." }
                PROMPT,
            'conseil' => <<<PROMPT
                Post de type "Conseil pratique" pour artisans sur la communication Instagram.
                JSON requis :
                { "number": "numéro 2 chiffres ex: 01", "tip_title": "titre conseil max 40 car.", "tip_body": "corps conseil 100-150 car. avec chiffre concret", "hashtags": "3-4 hashtags" }
                PROMPT,
            'stats' => <<<PROMPT
                Post de type "Statistique choc" sur la communication digitale des artisans.
                JSON requis :
                { "stat": "chiffre impactant ex: 73%", "stat_label": "sujet max 20 car.", "stat_description": "explication max 110 car.", "insight": "conclusion accrocheuse max 45 car." }
                PROMPT,
            'cta' => <<<PROMPT
                Post de type "Appel à l'action" pour convertir à l'essai gratuit de Postelio.
                JSON requis :
                { "hook": "accroche douleur artisan max 65 car.", "description": "bénéfice Postelio max 120 car.", "cta": "texte bouton max 30 car.", "url": "postelio.fr" }
                PROMPT,
            'temoignage' => <<<PROMPT
                Post de type "Témoignage client". Simule un avis authentique d'un artisan satisfait de Postelio.
                Le témoignage doit sonner vrai et naturel, parler d'un bénéfice concret (clients, temps, visibilité).
                JSON requis :
                {
                  "quote": "témoignage authentique 120-160 car., première personne, sans marketing",
                  "author": "prénom + initiale du nom ex: Marc D.",
                  "profession": "métier + ville ex: Plombier — Lyon",
                  "stars": 5
                }
                PROMPT,
            'avantages' => <<<PROMPT
                Post de type "Liste d'avantages". Présente 4 bénéfices clés de Postelio pour les artisans.
                JSON requis :
                {
                  "tag": "label court MAJUSCULES max 25 car. ex: POURQUOI POSTELIO ?",
                  "headline": "titre accrocheur max 45 car.",
                  "items": ["bénéfice 1 max 45 car.", "bénéfice 2 max 45 car.", "bénéfice 3 max 45 car.", "bénéfice 4 max 45 car."]
                }
                PROMPT,
            'question' => <<<PROMPT
                Post de type "Question engageante" (TOFU) pour interpeller les artisans et déclencher des commentaires.
                La question doit être directe, pointer une douleur réelle, et donner envie de répondre.
                JSON requis :
                {
                  "tag": "label court max 20 car. ex: BONNE QUESTION",
                  "question": "question directe et percutante max 90 car.",
                  "teaser": "amorce de réponse intrigante max 110 car., donne envie de lire la suite"
                }
                PROMPT,
            'comparaison' => <<<PROMPT
                Post de type "Comparaison Sans vs Avec Postelio" (BOFU). Montre le contraste avant/après.
                Chaque colonne doit avoir exactement 4 points courts et percutants.
                JSON requis :
                {
                  "headline": "titre court et accrocheur max 35 car.",
                  "col_sans": ["point négatif 1 max 38 car.", "point négatif 2 max 38 car.", "point négatif 3 max 38 car.", "point négatif 4 max 38 car."],
                  "col_avec": ["point positif 1 max 38 car.", "point positif 2 max 38 car.", "point positif 3 max 38 car.", "point positif 4 max 38 car."]
                }
                PROMPT,
            default => '',
        };

        return <<<PROMPT
            Tu es un expert en marketing digital pour les artisans et petites entreprises du bâtiment.
            Tu crées du contenu Instagram professionnel pour l'app Postelio.
            Postelio aide les artisans à générer des posts Instagram pro en 2 minutes grâce à l'IA.
            Ton ton est $toneDesc.

            Règles absolues :
            - Écris UNIQUEMENT en français
            - AUCUN markdown (pas de **, *, #, etc.)
            - Texte concis et impactant, adapté aux artisans français
            - Respecte EXACTEMENT les limites de caractères
            - Réponds UNIQUEMENT avec le JSON valide demandé, sans commentaire

            $templateDesc
            PROMPT;
    }

    private function buildUserPrompt(): string
    {
        return "Sujet du post : {$this->topic}\n\nGénère le contenu JSON maintenant.";
    }

    public function render()
    {
        return view('livewire.admin.instagram-post-generator');
    }
}
