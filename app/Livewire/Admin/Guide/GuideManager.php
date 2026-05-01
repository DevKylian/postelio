<?php

namespace App\Livewire\Admin\Guide;

use App\Models\FreeGuide;
use App\Models\FreeGuideLead;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

#[Layout('layouts.app')]
class GuideManager extends Component
{
    use WithFileUploads, WithPagination;

    public $title = '';
    public $sous_titre = '';
    public $contenu_html = '';
    public $meta_description = '';
    public $status = 'draft';
    public $published_at;
    public $featured_image_upload;
    public $existing_featured_image = '';
    public $json_pasted = '';
    public $showAIPromptModal = false;
    public $requires_email = true;

    public $isEditing = false;
    public $editingId = null;
    public $showForm = false;
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected function rules()
    {
        return [
            'title'                 => 'required|string|max:255',
            'sous_titre'            => 'nullable|string|max:180',
            'contenu_html'          => 'required|string',
            'meta_description'      => 'nullable|string|max:160',
            'status'                => 'required|in:draft,published',
            'published_at'          => 'nullable|date',
            'featured_image_upload' => 'nullable|image|max:2048',
            'requires_email'        => 'boolean',
        ];
    }

    public function mount(): void
    {
        $this->published_at = Carbon::now()->format('Y-m-d');
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetFields();
        }
    }

    public function openAIModal(): void
    {
        $this->showAIPromptModal = true;
        $this->json_pasted = '';
    }

    public function closeAIModal(): void
    {
        $this->showAIPromptModal = false;
        $this->json_pasted = '';
    }

    public function resetFields(): void
    {
        $this->title                  = '';
        $this->sous_titre             = '';
        $this->contenu_html           = '';
        $this->meta_description       = '';
        $this->status                 = 'draft';
        $this->published_at           = Carbon::now()->format('Y-m-d');
        $this->featured_image_upload  = null;
        $this->existing_featured_image = '';
        $this->requires_email         = true;
        $this->isEditing              = false;
        $this->editingId              = null;
        $this->json_pasted            = '';
    }

    public function processPastedJson(): void
    {
        $this->validate(['json_pasted' => 'required|string']);

        $data = json_decode($this->json_pasted, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            $this->resetFields();
            $this->title           = $data['titre_guide'] ?? ($data['titre'] ?? ($data['title'] ?? ''));
            $this->sous_titre      = $data['sous_titre'] ?? ($data['subtitle'] ?? '');
            $this->meta_description = $data['meta_description'] ?? '';
            $this->contenu_html    = $data['contenu_html'] ?? ($data['content'] ?? '');
            $this->status          = 'draft';

            $this->isEditing          = false;
            $this->editingId          = null;
            $this->showForm           = true;
            $this->showAIPromptModal  = false;

            $this->dispatch('notify', 'Guide importé avec succès.');
        } else {
            $this->dispatch('notify', 'Erreur : Le texte collé n\'est pas un JSON valide.');
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title'            => $this->title,
            'sous_titre'       => $this->sous_titre,
            'contenu_html'     => $this->contenu_html,
            'meta_description' => $this->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($this->contenu_html), 155),
            'status'           => $this->status,
            'published_at'     => $this->status === 'published' ? ($this->published_at ?? now()) : null,
            'requires_email'   => $this->requires_email,
        ];

        if ($this->featured_image_upload) {
            if ($this->isEditing && $this->existing_featured_image) {
                Storage::disk('public')->delete($this->existing_featured_image);
            }
            $data['featured_image'] = $this->featured_image_upload->store('guides', 'public');
        }

        if ($this->isEditing) {
            $guide = FreeGuide::findOrFail($this->editingId);
            $data['slug'] = FreeGuide::generateSlug($this->title, $this->editingId);
            $guide->update($data);
            $this->dispatch('notify', 'Guide mis à jour.');
        } else {
            $data['user_id'] = Auth::id();
            $data['slug']    = FreeGuide::generateSlug($this->title);
            FreeGuide::create($data);
            $this->dispatch('notify', 'Guide créé.');
        }

        $this->resetFields();
        $this->showForm = false;
    }

    public function edit(int $id): void
    {
        $guide = FreeGuide::findOrFail($id);
        $this->editingId               = $id;
        $this->title                   = $guide->title;
        $this->sous_titre              = $guide->sous_titre ?? '';
        $this->contenu_html            = $guide->contenu_html;
        $this->meta_description        = $guide->meta_description ?? '';
        $this->status                  = $guide->status;
        $this->published_at            = $guide->published_at
            ? Carbon::parse($guide->published_at)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');
        $this->existing_featured_image = $guide->featured_image ?? '';
        $this->requires_email          = $guide->requires_email ?? true;
        $this->isEditing               = true;
        $this->showForm                = true;
    }

    public function delete(int $id): void
    {
        $guide = FreeGuide::findOrFail($id);
        if ($guide->featured_image) {
            Storage::disk('public')->delete($guide->featured_image);
        }
        $guide->delete();
        $this->dispatch('notify', 'Guide supprimé.');
    }

    public function toggleStatus(int $id): void
    {
        $guide = FreeGuide::findOrFail($id);
        if ($guide->status === 'draft') {
            $guide->update(['status' => 'published', 'published_at' => $guide->published_at ?? now()]);
        } else {
            $guide->update(['status' => 'draft']);
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function simplifyGuideCoverTitle(string $title): array
    {
        if (empty(trim($title))) {
            return ['title' => ''];
        }

        $response = OpenAI::chat()->create([
            'model'    => 'gpt-4o',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => "Tu es expert en communication. Génère un titre court et percutant pour la couverture d'un guide gratuit (3 à 6 mots maximum, pas de ponctuation lourde, pas de deux-points, pas de point d'interrogation). Utilise une casse naturelle (Sentence case), PAS DE TOUT-MAJUSCULES. Tu peux utiliser *mot* pour mettre un mot en italique coloré si c'est pertinent. Réponds uniquement en JSON : {\"title\": \"...\"}",
                ],
                [
                    'role'    => 'user',
                    'content' => "Titre du guide : " . $title,
                ],
            ],
            'max_tokens'      => 60,
            'temperature'     => 0.7,
            'response_format' => ['type' => 'json_object'],
        ]);

        $data = json_decode($response->choices[0]->message->content, true);

        return ['title' => $data['title'] ?? $title];
    }

    public function render()
    {
        $guides = FreeGuide::query()
            ->withCount('leads')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $stats = [
            'total_leads' => FreeGuideLead::count(),
            'leads_today' => FreeGuideLead::whereDate('created_at', Carbon::today())->count(),
            'total_views' => FreeGuide::sum('downloads_count'),
            'avg_conversion' => 0
        ];

        if ($stats['total_views'] > 0) {
            $stats['avg_conversion'] = round(($stats['total_leads'] / $stats['total_views']) * 100, 1);
        }

        return view('livewire.admin.guide.guide-manager', compact('guides', 'stats'));
    }
}
