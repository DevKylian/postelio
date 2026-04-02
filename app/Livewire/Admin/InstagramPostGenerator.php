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
    public bool  $generating  = false;
    public bool  $downloading = false;
    public ?string $errorMessage = null;
    public bool  $hasContent  = false;

    // ── Editable generated fields (annonce) ───────────────────────────────────
    public string $tag         = '';
    public string $headline    = '';
    public string $subheadline = '';
    public string $cta         = '';

    // ── Editable generated fields (conseil) ──────────────────────────────────
    public string $number    = '';
    public string $tip_title = '';
    public string $tip_body  = '';
    public string $hashtags  = '';

    // ── Editable generated fields (stats) ────────────────────────────────────
    public string $stat             = '';
    public string $stat_label       = '';
    public string $stat_description = '';
    public string $insight          = '';

    // ── Editable generated fields (cta) ──────────────────────────────────────
    public string $hook        = '';
    public string $description = '';
    public string $url         = 'postelio.fr';

    // ── History ───────────────────────────────────────────────────────────────
    public array $history = [];

    public function resetFields(): void
    {
        $this->tag = $this->headline = $this->subheadline = $this->cta = '';
        $this->number = $this->tip_title = $this->tip_body = $this->hashtags = '';
        $this->stat = $this->stat_label = $this->stat_description = $this->insight = '';
        $this->hook = $this->description = '';
        $this->url = 'postelio.fr';
        $this->hasContent = false;
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
            'template' => 'required|in:annonce,conseil,stats,cta',
            'tone'     => 'required|in:professionnel,accessible,dynamique,inspirant',
        ]);

        $this->generating    = true;
        $this->errorMessage  = null;

        try {
            $systemPrompt = $this->buildSystemPrompt();
            $userPrompt   = $this->buildUserPrompt();

            $response = OpenAI::chat()->create([
                'model'           => 'gpt-4o',
                'messages'        => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
                'max_tokens'      => 500,
                'temperature'     => 0.6,
                'response_format' => ['type' => 'json_object'],
            ]);

            $raw  = trim($response->choices[0]->message->content);
            $data = json_decode($raw, true);

            if (!is_array($data)) {
                throw new \RuntimeException('Réponse AI invalide.');
            }

            $this->fillFields($data);
            $this->hasContent = true;

            // Store in history (keep last 10)
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
            $data = $this->buildDataArray();
            $html = view('render.postelio-instagram', [
                'template' => $this->template,
                'data'     => $data,
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

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function fillFields(array $data): void
    {
        match ($this->template) {
            'annonce' => $this->fillAnnonce($data),
            'conseil' => $this->fillConseil($data),
            'stats'   => $this->fillStats($data),
            'cta'     => $this->fillCta($data),
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

    private function buildDataArray(): array
    {
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
                'hook'        => $this->hook,
                'description' => $this->description,
                'cta'         => $this->cta,
                'url'         => $this->url,
            ],
            default => [],
        };
    }

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
                Tu génères le contenu pour un post Instagram de type "Annonce / Feature".
                Ce post présente une fonctionnalité ou un avantage clé de l'app Postelio.
                Postelio aide les artisans (plombiers, électriciens, carreleurs, peintres, etc.) à créer des posts Instagram professionnels en 2 minutes grâce à l'IA.

                Génère un JSON avec exactement ces champs :
                {
                  "tag": "court label accrocheur en MAJUSCULES, max 20 car. (ex: GAIN DE TEMPS, IA ARTISAN, NOUVEAUTÉ...)",
                  "headline": "titre principal percutant, max 55 car., aucun markdown",
                  "subheadline": "phrase descriptive claire, max 100 car., aucun markdown",
                  "cta": "texte bouton court, max 25 car. (ex: Essayer gratuitement)"
                }
                PROMPT,
            'conseil' => <<<PROMPT
                Tu génères le contenu pour un post Instagram de type "Conseil".
                Ce post donne un conseil pratique aux artisans sur la communication Instagram ou le développement de leur activité.
                Postelio est l'app qui automatise les posts Instagram des artisans.

                Génère un JSON avec exactement ces champs :
                {
                  "number": "numéro du conseil en 2 chiffres (ex: 01, 02...)",
                  "tip_title": "titre du conseil, max 40 car., direct et actionnable",
                  "tip_body": "corps du conseil, 100-150 car., chiffre ou donnée concrète si possible",
                  "hashtags": "3-4 hashtags pertinents séparés par des espaces"
                }
                PROMPT,
            'stats' => <<<PROMPT
                Tu génères le contenu pour un post Instagram de type "Statistique".
                Ce post choque positivement avec un chiffre clé sur la communication digitale des artisans.
                Postelio est la solution pour aider les artisans à être présents sur Instagram.

                Génère un JSON avec exactement ces champs :
                {
                  "stat": "le chiffre principal impactant (ex: 73%, 8/10, 2min...)",
                  "stat_label": "sujet court, max 20 car. (ex: des artisans, des clients, des plombiers...)",
                  "stat_description": "explication de la statistique, max 110 car., créatif mais crédible",
                  "insight": "question ou phrase de conclusion accrocheuse, max 45 car."
                }
                PROMPT,
            'cta' => <<<PROMPT
                Tu génères le contenu pour un post Instagram de type "Appel à l'action" (CTA).
                Ce post vise à convertir les artisans à essayer Postelio gratuitement.
                Postelio est une app qui crée des posts Instagram professionnels pour artisans en 2 minutes avec l'IA.

                Génère un JSON avec exactement ces champs :
                {
                  "hook": "accroche forte et directe, max 65 car., parle à la douleur de l'artisan",
                  "description": "description concise de Postelio, max 120 car., mention du bénéfice clé",
                  "cta": "texte du bouton, max 30 car. (ex: Commencer gratuitement →)",
                  "url": "postelio.fr"
                }
                PROMPT,
            default => '',
        };

        return <<<PROMPT
            Tu es un expert en marketing digital pour les artisans et les petites entreprises du bâtiment.
            Tu crées du contenu Instagram professionnel pour l'app Postelio.
            Ton ton est $toneDesc.

            Règles absolues :
            - Écris UNIQUEMENT en français
            - AUCUN markdown (pas de **, *, #, etc.)
            - Texte concis, impactant, adapté aux artisans français
            - Respecte EXACTEMENT les limites de caractères indiquées
            - Réponds UNIQUEMENT avec le JSON demandé, sans commentaire

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
