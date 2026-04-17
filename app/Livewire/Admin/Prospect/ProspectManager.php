<?php

namespace App\Livewire\Admin\Prospect;

use App\Models\InstagramProspect;
use App\Models\SalesScript;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

class ProspectManager extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    public $newAccountName = '';
    public $newProfileUrl = '';
    public $filterAction = false;

    #[On('scripts-updated')]
    public function refresh()
    {
        // Triggers re-render
    }

    public function rules()
    {
        return [
            'newAccountName' => 'required|string|max:255|unique:instagram_prospects,account_name',
            'newProfileUrl'  => 'nullable|url|max:255',
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
            'account_name'   => $this->newAccountName,
            'profile_url'    => $this->newProfileUrl,
            'status'         => InstagramProspect::STATUS_DISCOVERED,
            'last_action_date' => now(),
        ]);

        $this->newAccountName = '';
        $this->newProfileUrl  = '';

        session()->flash('message', 'Prospect ajouté avec succès.');
        $this->resetPage();
    }

    public function startScript($prospectId)
    {
        $prospect = InstagramProspect::findOrFail($prospectId);
        if (!$prospect->script_version) return;

        $script = SalesScript::find($prospect->script_version);
        if (!$script || !$script->flow) return;

        $rootNodeId = $script->flow['root'] ?? null;
        if (!$rootNodeId) return;

        $prospect->current_node_id = $rootNodeId;
        $prospect->status          = InstagramProspect::STATUS_DM_SENT;
        $prospect->last_action_date = now();
        $prospect->save();
    }

    public function advanceProspect($prospectId, $nextNodeId, $condition)
    {
        $validConditions = ['reply', 'no_reply', 'positive', 'negative'];
        if (!in_array($condition, $validConditions)) return;

        $prospect = InstagramProspect::findOrFail($prospectId);
        $nextNodeId = $nextNodeId ?: null;

        // Validate the target node exists in the script
        if ($nextNodeId) {
            $script = SalesScript::find($prospect->script_version);
            if (!$script || !isset($script->flow['nodes'][$nextNodeId])) return;
        }

        $prospect->current_node_id  = $nextNodeId;
        $prospect->last_action_date = now();

        if (in_array($condition, ['positive', 'reply'])) {
            $prospect->status = InstagramProspect::STATUS_RESPONDED;
        } elseif ($condition === 'negative') {
            $prospect->status = InstagramProspect::STATUS_NOT_INTERESTED;
        } elseif ($condition === 'no_reply') {
            $prospect->status = InstagramProspect::STATUS_RELANCE_1;
        }

        $prospect->save();
    }

    public function resetScript($prospectId)
    {
        $prospect = InstagramProspect::findOrFail($prospectId);
        $prospect->current_node_id  = null;
        $prospect->status           = InstagramProspect::STATUS_DISCOVERED;
        $prospect->last_action_date = now();
        $prospect->save();
    }

    public function updateStatus($prospectId, $status)
    {
        $prospect = InstagramProspect::findOrFail($prospectId);
        $prospect->status           = $status;
        $prospect->last_action_date = now();
        $prospect->save();
    }

    public function updateNotes($prospectId, $notes)
    {
        $prospect = InstagramProspect::findOrFail($prospectId);
        $prospect->notes = $notes;
        $prospect->save();
    }

    public function updateScript($prospectId, $scriptVersion)
    {
        $prospect = InstagramProspect::findOrFail($prospectId);
        $prospect->script_version  = $scriptVersion ?: null;
        $prospect->current_node_id = null; // Reset flow when changing script
        $prospect->save();
    }

    public function deleteProspect($prospectId)
    {
        InstagramProspect::findOrFail($prospectId)->delete();
    }

    public function render()
    {
        $actionsTodoClosure = function ($q) {
            $q->where(function ($q1) {
                // Flow-based: bloqué sur un nœud depuis > 48h
                $q1->whereNotNull('current_node_id')
                    ->where('last_action_date', '<=', now()->subHours(48));
            })->orWhere(function ($q2) {
                // Legacy: DM Sent > 48h
                $q2->whereNull('current_node_id')
                    ->where('status', InstagramProspect::STATUS_DM_SENT)
                    ->where('last_action_date', '<=', now()->subHours(48));
            })->orWhere(function ($q3) {
                // Legacy: Relance 1 > 72h
                $q3->whereNull('current_node_id')
                    ->where('status', InstagramProspect::STATUS_RELANCE_1)
                    ->where('last_action_date', '<=', now()->subHours(72));
            })->orWhere(function ($q4) {
                // Legacy: Réponse reçue (nécessite un suivi)
                $q4->whereNull('current_node_id')
                    ->where('status', InstagramProspect::STATUS_RESPONDED);
            });
        };

        $query = InstagramProspect::query();

        if ($this->filterAction) {
            $query->where($actionsTodoClosure);
        }

        $prospects = $query->with('script')->orderBy('last_action_date', 'desc')->paginate(20);
        $statuses  = InstagramProspect::getStatuses();
        $scripts   = InstagramProspect::getScripts();

        $stats = [
            'responded'     => InstagramProspect::where('status', InstagramProspect::STATUS_RESPONDED)->count(),
            'dm_sent_total' => InstagramProspect::where(function ($q) {
                $q->whereNotNull('current_node_id')
                    ->orWhereIn('status', [InstagramProspect::STATUS_DM_SENT, InstagramProspect::STATUS_RELANCE_1]);
            })->count(),
            'dm_sent_today' => InstagramProspect::where(function ($q) {
                $q->whereNotNull('current_node_id')
                    ->orWhere('status', InstagramProspect::STATUS_DM_SENT);
            })->whereDate('last_action_date', today())->count(),
            'actions_todo'  => InstagramProspect::where($actionsTodoClosure)->count(),
        ];

        $scriptStats = InstagramProspect::whereNotNull('script_version')
            ->selectRaw('script_version, count(*) as total, sum(status = ?) as responded', [InstagramProspect::STATUS_RESPONDED])
            ->groupBy('script_version')
            ->orderBy('script_version')
            ->get();

        return view('livewire.admin.prospect.prospect-manager', [
            'prospects'   => $prospects,
            'statuses'    => $statuses,
            'scripts'     => $scripts,
            'stats'       => $stats,
            'scriptStats' => $scriptStats,
        ]);
    }
}
