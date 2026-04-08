<?php

namespace App\Livewire\Admin\Guide;

use App\Models\FreeGuide;
use App\Models\FreeGuideLead;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GuideLeads extends Component
{
    use WithPagination;

    public $guide;
    public $search = '';

    public function mount(FreeGuide $guide)
    {
        $this->guide = $guide;
    }

    public function render()
    {
        $leads = FreeGuideLead::where('free_guide_id', $this->guide->id)
            ->where('email', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(20);

        return view('livewire.admin.guide.guide-leads', compact('leads'));
    }
}
