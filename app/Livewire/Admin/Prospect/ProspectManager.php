<?php

namespace App\Livewire\Admin\Prospect;

use App\Models\InstagramProspect;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ProspectManager extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    public $newAccountName = '';
    public $newProfileUrl = '';

    public function rules()
    {
        return [
            'newAccountName' => 'required|string|max:255|unique:instagram_prospects,account_name',
            'newProfileUrl' => 'nullable|url|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'newAccountName.unique' => 'Ce compte est déjà dans la liste.',
        ];
    }

    public function addProspect()
    {
        $this->newAccountName = strtolower(trim($this->newAccountName));
        $this->validate();

        InstagramProspect::create([
            'account_name' => $this->newAccountName,
            'profile_url' => $this->newProfileUrl,
            'status' => InstagramProspect::STATUS_DISCOVERED,
            'last_action_date' => now(),
        ]);

        $this->newAccountName = '';
        $this->newProfileUrl = '';
        
        session()->flash('message', 'Prospect ajouté avec succès.');
        $this->resetPage();
    }

    public function updateStatus($prospectId, $status)
    {
        $prospect = InstagramProspect::findOrFail($prospectId);
        $prospect->status = $status;
        $prospect->last_action_date = now();
        $prospect->save();
    }

    public function updateNotes($prospectId, $notes)
    {
        $prospect = InstagramProspect::findOrFail($prospectId);
        $prospect->notes = $notes;
        $prospect->save();
    }
    
    public function deleteProspect($prospectId)
    {
        InstagramProspect::findOrFail($prospectId)->delete();
    }

    public $filterAction = false;

    public function render()
    {
        $query = InstagramProspect::query();

        $actionsTodoClosure = function($q) {
            $q->where(function($q1) {
                // DM Sent > 48h
                $q1->where('status', InstagramProspect::STATUS_DM_SENT)
                   ->where('last_action_date', '<=', now()->subHours(48));
            })->orWhere(function($q2) {
                // Relance 1 > 72h
                $q2->where('status', InstagramProspect::STATUS_RELANCE_1)
                   ->where('last_action_date', '<=', now()->subHours(72));
            })->orWhere(function($q3) {
                // Réponse reçue (nécessite une action / un suivi)
                $q3->where('status', InstagramProspect::STATUS_RESPONDED);
            });
        };

        if ($this->filterAction) {
            $query->where($actionsTodoClosure);
        }

        $prospects = $query->orderBy('last_action_date', 'desc')->paginate(20);
        $statuses = InstagramProspect::getStatuses();

        $stats = [
            'responded' => InstagramProspect::where('status', InstagramProspect::STATUS_RESPONDED)->count(),
            'dm_sent_total' => InstagramProspect::whereIn('status', [InstagramProspect::STATUS_DM_SENT, InstagramProspect::STATUS_RELANCE_1])->count(),
            'dm_sent_today' => InstagramProspect::where('status', InstagramProspect::STATUS_DM_SENT)->whereDate('last_action_date', today())->count(),
            'actions_todo' => InstagramProspect::where($actionsTodoClosure)->count(),
        ];

        return view('livewire.admin.prospect.prospect-manager', [
            'prospects' => $prospects,
            'statuses' => $statuses,
            'stats' => $stats,
        ]);
    }
}
