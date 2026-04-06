<?php

namespace App\Livewire\Admin\Blog;

use App\Models\BlogPost;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

#[Layout('layouts.app')]
class BlogManager extends Component
{
    use WithFileUploads, WithPagination;

    public $title = '';
    public $author_name = 'Kylian Dev';
    public $content = '';
    public $excerpt = '';
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $status = 'draft';
    public $published_at;
    public $featured_image_upload;
    public $existing_featured_image = '';
    public $json_pasted = '';
    public $showAIPromptModal = false;

    public $isEditing = false;
    public $editingId = null;
    public $showForm = false;
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'author_name' => 'nullable|string|max:100',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'featured_image_upload' => 'nullable|image|max:2048',
        ];
    }

    public function mount()
    {
        $this->published_at = Carbon::now()->format('Y-m-d');
        $this->author_name = Auth::user()?->name ?? 'Kylian Dev';
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetFields();
        }
    }

    public function openAIModal()
    {
        $this->showAIPromptModal = true;
        $this->json_pasted = '';
    }

    public function closeAIModal()
    {
        $this->showAIPromptModal = false;
        $this->json_pasted = '';
    }

    public function resetFields()
    {
        $this->title = '';
        $this->author_name = Auth::user()?->name ?? 'Kylian Dev';
        $this->content = '';
        $this->excerpt = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->status = 'draft';
        $this->published_at = Carbon::now()->format('Y-m-d');
        $this->featured_image_upload   = null;
        $this->existing_featured_image = '';
        $this->isEditing = false;
        $this->editingId = null;
        $this->json_pasted = '';
    }

    public function processPastedJson()
    {
        $this->validate([
            'json_pasted' => 'required|string'
        ]);

        $data = json_decode($this->json_pasted, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            $this->resetFields();
            $this->title = $data['titre'] ?? ($data['title'] ?? '');
            $this->author_name = $data['auteur'] ?? ($data['author'] ?? (Auth::user()?->name ?? 'Kylian Dev'));
            $this->excerpt = $data['extrait'] ?? ($data['excerpt'] ?? '');
            $this->content = $data['contenu_html'] ?? ($data['content'] ?? '');
            $this->meta_title = $data['meta_title'] ?? '';
            $this->meta_description = $data['meta_description'] ?? '';
            $this->meta_keywords = isset($data['mots_cles']) && is_array($data['mots_cles'])
                                    ? implode(', ', $data['mots_cles'])
                                    : ($data['mots_cles'] ?? ($data['meta_keywords'] ?? ''));
            $this->status = in_array($data['status'] ?? '', ['draft', 'published']) ? $data['status'] : 'draft';
            
            $this->isEditing = false;
            $this->editingId = null;
            $this->showForm = true;
            $this->showAIPromptModal = false;

            $this->dispatch('notify', 'Article importé avec succès.');
        } else {
            $this->dispatch('notify', 'Erreur : Le texte collé n\'est pas un JSON valide.');
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'author_name' => $this->author_name ?: (Auth::user()?->name ?? 'Kylian Dev'),
            'content' => $this->content,
            'excerpt' => strip_tags($this->excerpt) ?: \Illuminate\Support\Str::limit(strip_tags($this->content), 200),
            'meta_title' => $this->meta_title ?: $this->title,
            'meta_description' => strip_tags($this->meta_description) ?: \Illuminate\Support\Str::limit(strip_tags($this->content), 155),
            'meta_keywords' => $this->meta_keywords,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? ($this->published_at ?? now()) : null,
            'read_time' => BlogPost::estimateReadTime($this->content),
        ];

        if ($this->featured_image_upload) {
            if ($this->isEditing && $this->existing_featured_image) {
                Storage::disk('public')->delete($this->existing_featured_image);
            }
            $data['featured_image'] = $this->featured_image_upload->store('blog', 'public');
        }

        if ($this->isEditing) {
            $post = BlogPost::findOrFail($this->editingId);
            $data['slug'] = BlogPost::generateSlug($this->title, $this->editingId);
            $post->update($data);
            $this->dispatch('notify', 'Article mis à jour.');
        } else {
            $data['user_id'] = Auth::id();
            $data['slug'] = BlogPost::generateSlug($this->title);
            BlogPost::create($data);
            $this->dispatch('notify', 'Article créé.');
        }

        $this->resetFields();
        $this->showForm = false;
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        $this->editingId = $id;
        $this->title = $post->title;
        $this->author_name = $post->author_name ?? ($post->user?->name ?? 'Kylian Dev');
        $this->content = $post->content;
        $this->excerpt = $post->excerpt;
        $this->meta_title = $post->meta_title;
        $this->meta_description = $post->meta_description;
        $this->meta_keywords = $post->meta_keywords;
        $this->status = $post->status;
        $this->published_at = $post->published_at ? Carbon::parse($post->published_at)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $this->existing_featured_image = $post->featured_image;
        $this->isEditing = true;
        $this->showForm = true;
    }

    public function delete($id)
    {
        $post = BlogPost::findOrFail($id);
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();
        $this->dispatch('notify', 'Article supprimé.');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus($id)
    {
        $post = BlogPost::findOrFail($id);
        if ($post->status === 'draft') {
            $post->update(['status' => 'published', 'published_at' => $post->published_at ?? now()]);
        } else {
            $post->update(['status' => 'draft']);
        }
    }

    public function simplifyBannerTitle(string $title): array
    {
        if (empty(trim($title))) {
            return ['title' => ''];
        }

        $response = OpenAI::chat()->create([
            'model'    => 'gpt-4o',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => "Tu es expert en communication digitale. Génère un titre court et percutant pour une miniature de blog (3 à 6 mots maximum, pas de ponctuation lourde, pas de deux-points, pas de point d'interrogation). Tu peux utiliser *mot* pour mettre un mot en italique coloré si c'est pertinent. Réponds uniquement en JSON : {\"title\": \"...\"}",
                ],
                [
                    'role'    => 'user',
                    'content' => "Titre d'article : " . $title,
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
        $posts = BlogPost::query()
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.admin.blog.blog-manager', compact('posts'));
    }
}
