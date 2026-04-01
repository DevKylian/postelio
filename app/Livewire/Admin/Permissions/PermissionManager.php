<?php

namespace App\Livewire\Admin\Permissions;

use App\Models\RolePermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PermissionManager extends Component
{
    public $roles = [];
    public $editingQuota = null;
    public $newQuota = null;

    protected $rules = [
        'roles.*.monthly_quota' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->loadRoles();
    }

    public function loadRoles()
    {
        $this->roles = RolePermission::all()->keyBy('role_key')->toArray();
    }

    /**
     * Toggle a boolean permission.
     */
    public function togglePermission($roleKey, $field)
    {
        $permission = RolePermission::where('role_key', $roleKey)->first();
        
        if ($permission) {
            $permission->$field = !$permission->$field;
            $permission->save();
            
            $this->loadRoles();
            session()->flash('success', "Permission mise à jour pour le rôle {$roleKey}.");
        }
    }

    /**
     * Start editing a quota.
     */
    public function startEditingQuota($roleKey)
    {
        $this->editingQuota = $roleKey;
        $this->newQuota = $this->roles[$roleKey]['monthly_quota'];
    }

    /**
     * Save the new quota.
     */
    public function saveQuota()
    {
        if ($this->editingQuota) {
            $permission = RolePermission::where('role_key', $this->editingQuota)->first();
            if ($permission) {
                $permission->monthly_quota = (int) $this->newQuota;
                $permission->save();
                
                $this->editingQuota = null;
                $this->loadRoles();
                session()->flash('success', "Quota mis à jour.");
            }
        }
    }

    public function render()
    {
        $roleLabels = [
            'admin' => 'Administrateurs',
            'pro' => 'Plan Professionnel',
            'basic' => 'Plan Basique',
            'tester' => 'Testeurs',
            'free' => 'Utilisateurs Gratuits',
        ];

        return view('livewire.admin.permissions.permission-manager', [
            'roleLabels' => $roleLabels
        ]);
    }
}
