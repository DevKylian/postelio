<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ActivityStream extends Component
{
    use WithPagination;

    public $search = '';
    public $tag = '';
    public $rank = '';
    public $perPage = 25;

    protected $queryString = ['search', 'tag', 'rank'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTag()
    {
        $this->resetPage();
    }

    public function updatedRank()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'tag', 'rank']);
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::with('user')
            ->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('email', 'like', '%' . $this->search . '%')
                  ->orWhere('ip', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('url', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->tag) {
            $query->where('tag', $this->tag);
        }

        if ($this->rank) {
            $query->where('rank', $this->rank);
        }

        $logs = $query->paginate($this->perPage);

        $availableTags = AuditLog::distinct()->pluck('tag')->filter();
        $availableRanks = AuditLog::distinct()->pluck('rank')->filter();

        return view('livewire.admin.activity-stream', [
            'logs' => $logs,
            'availableTags' => $availableTags,
            'availableRanks' => $availableRanks,
        ]);
    }
}
