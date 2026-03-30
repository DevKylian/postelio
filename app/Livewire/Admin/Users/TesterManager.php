<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TesterManager extends Component
{
    use WithPagination;

    public $search = '';
    public $tester_quota;
    public $tester_can_publish;
    public $tester_all_templates;

    protected $rules = [
        'tester_quota' => 'required|integer|min:0',
        'tester_can_publish' => 'required|boolean',
        'tester_all_templates' => 'required|boolean',
    ];

    public function mount()
    {
        $this->tester_quota = Setting::get('tester_quota', 10);
        $this->tester_can_publish = (bool) Setting::get('tester_can_publish', false);
        $this->tester_all_templates = (bool) Setting::get('tester_all_templates', false);
    }

    public function updatedTesterQuota($value)
    {
        $this->validateOnly('tester_quota');
        Setting::set('tester_quota', $value);
        $this->dispatch('notify', 'Quota des testeurs mis à jour.');
    }

    public function updatedTesterCanPublish($value)
    {
        Setting::set('tester_can_publish', (bool) $value);
        $this->dispatch('notify', 'Permission de publication mise à jour.');
    }

    public function updatedTesterAllTemplates($value)
    {
        Setting::set('tester_all_templates', (bool) $value);
        $this->dispatch('notify', 'Accès aux gabarits mis à jour.');
    }

    public function toggleTester(int $userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_tester' => !$user->is_tester]);
        
        $status = $user->is_tester ? 'promu testeur' : 'retiré des testeurs';
        $this->dispatch('notify', "Utilisateur {$user->name} {$status}.");
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('is_tester', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('livewire.admin.users.tester-manager', [
            'users' => $users,
        ]);
    }
}
