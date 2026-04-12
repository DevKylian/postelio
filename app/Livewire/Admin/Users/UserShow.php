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

    public function deleteUser()
    {
        // 1. Stripe Cleanup
        try {
            if ($this->user->subscribed('default')) {
                $this->user->subscription('default')->cancelNow();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin: Failed to cancel Stripe subscription during user deletion', ['user_id' => $this->user->id, 'error' => $e->getMessage()]);
        }

        // 2. Storage Cleanup
        try {
            // Delete post files
            foreach ($this->user->posts as $post) {
                if ($post->audio_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($post->audio_path);
                if ($post->image_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image_path);
                if ($post->image_before_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image_before_path);
                if ($post->image_after_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image_after_path);
                
                if ($post->slides && is_array($post->slides)) {
                    foreach ($post->slides as $slide) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($slide);
                    }
                }
            }

            // Delete company logo
            if ($this->user->company && $this->user->company->logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->user->company->logo_path);
            }
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error('Admin: Failed to cleanup storage during user deletion', ['user_id' => $this->user->id, 'error' => $e->getMessage()]);
        }

        // 3. Database Deletion
        // Delete logs first as they are 'set null' by default, if we want a clean wipe we delete them
        \App\Models\AuditLog::where('user_id', $this->user->id)->delete();
        
        $this->user->delete();

        session()->flash('success', 'L\'utilisateur et toutes ses données ont été supprimés.');
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        $this->user->load('company');
        $posts = $this->user->posts()->latest()->paginate(12);
        
        $activityLogs = \App\Models\AuditLog::where('user_id', $this->user->id)
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->take(50)
            ->get();
        
        return view('livewire.admin.users.user-show', compact('posts', 'activityLogs'));
    }
}
