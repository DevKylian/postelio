<?php

namespace App\Livewire\Admin\Changelog;

use App\Models\Changelog;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Carbon;

#[Layout('layouts.app')]
class ChangelogManager extends Component
{
    public $title = '';
    public $content = '';
    public $type = 'feature';
    public $version = '';
    public $published_at;
    
    public $isEditing = false;
    public $editingId = null;
    public $showForm = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'type' => 'required|in:feature,improvement,fix',
        'version' => 'nullable|string|max:20',
        'published_at' => 'required|date',
    ];

    public function mount()
    {
        $this->published_at = Carbon::now()->format('Y-m-d');
        $this->version = $this->guessNextVersion();
    }

    private function guessNextVersion()
    {
        $last = Changelog::orderByDesc('published_at')->first();
        if (!$last || !$last->version) {
            return 'v0.0.1';
        }

        // Try to increment patch version if it's vX.Y.Z
        if (preg_match('/v(\d+)\.(\d+)\.(\d+)/', $last->version, $matches)) {
            $major = $matches[1];
            $minor = $matches[2];
            $patch = $matches[3] + 1;
            return "v{$major}.{$minor}.{$patch}";
        }

        return $last->version;
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetFields();
        } else {
            $this->version = $this->guessNextVersion();
        }
    }

    public function resetFields()
    {
        $this->title = '';
        $this->content = '';
        $this->type = 'feature';
        $this->version = $this->guessNextVersion();
        $this->published_at = Carbon::now()->format('Y-m-d');
        $this->isEditing = false;
        $this->editingId = null;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $changelog = Changelog::findOrFail($this->editingId);
            $changelog->update([
                'title' => $this->title,
                'content' => $this->content,
                'type' => $this->type,
                'version' => $this->version,
                'published_at' => $this->published_at,
            ]);
            $this->dispatch('notify', 'Changelog mis à jour avec succès.');
        } else {
            Changelog::create([
                'title' => $this->title,
                'content' => $this->content,
                'type' => $this->type,
                'version' => $this->version,
                'published_at' => $this->published_at,
            ]);
            $this->dispatch('notify', 'Nouveau changelog ajouté.');
        }

        $this->resetFields();
        $this->showForm = false;
    }

    public function edit($id)
    {
        $changelog = Changelog::findOrFail($id);
        $this->editingId = $id;
        $this->title = $changelog->title;
        $this->content = $changelog->content;
        $this->type = $changelog->type;
        $this->version = $changelog->version;
        $this->published_at = Carbon::parse($changelog->published_at)->format('Y-m-d');
        $this->isEditing = true;
        $this->showForm = true;
    }

    public function delete($id)
    {
        Changelog::findOrFail($id)->delete();
        $this->dispatch('notify', 'Changelog supprimé.');
    }

    public function render()
    {
        $changelogs = Changelog::orderByDesc('published_at')->get();
        return view('livewire.admin.changelog.changelog-manager', compact('changelogs'));
    }
}
