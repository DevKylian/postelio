<?php

namespace App\Livewire\Admin\Guide;

use App\Models\FreeGuideLead;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GuideAllLeads extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $leads = FreeGuideLead::with('guide')
            ->where('email', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(50);

        return view('livewire.admin.guide.guide-all-leads', compact('leads'));
    }
}
