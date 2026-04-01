<?php
namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UserShow extends Component
{
    use WithPagination;

    public User $user;
    public $newTotalQuota;
    public ?\App\Models\Post $selectedPost = null;

    public function selectPost(\App\Models\Post $post)
    {
        $this->selectedPost = $post;
    }

    public function closeModal()
    {
        $this->selectedPost = null;
    }

    public function mount(User $user)
    {
        $this->user = $user;
        $this->newTotalQuota = $user->total_credits;
    }

    public function updateQuota()
    {
        $this->validate(['newTotalQuota' => 'required|integer|min:0']);
        $this->user->total_credits = $this->newTotalQuota;
        $this->user->save();
        session()->flash('success', 'Quota mis à jour.');
    }

    public function toggleAdmin()
    {
        $this->user->is_admin = !$this->user->is_admin;
        $this->user->save();
        session()->flash('success', 'Rang mis à jour.');
    }

    public function setManualPlan(?string $planKey)
    {
        $this->user->manual_plan = $planKey;
        
        // Backward compatibility: set is_premium for non-free plans
        $this->user->is_premium = ($planKey !== null);
        
        $this->user->save();
        
        $status = $planKey ? "Plan " . ucfirst($planKey) : "Standard (Free)";
        session()->flash('success', "Le plan a été forcé en : $status.");
    }

    public function toggleTester()
    {
        $this->user->is_tester = !$this->user->is_tester;
        $this->user->save();
        session()->flash('success', 'Statut testeur mis à jour.');
    }

    public function toggleBan()
    {
        $this->user->is_banned = !$this->user->is_banned;
        $this->user->save();
        $status = $this->user->is_banned ? 'banni' : 'débanni';
        session()->flash('success', "L'utilisateur a été $status.");
    }

    public function render()
    {
        $this->user->load('company');
        $posts = $this->user->posts()->latest()->paginate(12);
        
        return view('livewire.admin.users.user-show', compact('posts'));
    }
}
