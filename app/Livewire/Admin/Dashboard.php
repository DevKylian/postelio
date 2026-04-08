<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use App\Services\ZernioService;
use Carbon\Carbon;
use Laravel\Cashier\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public ?\App\Models\Post $selectedPost = null;
    public bool $postGenerationEnabled = true;

    public function mount()
    {
        $this->postGenerationEnabled = \App\Models\Setting::get('post_generation_enabled', true);
    }

    public function selectPost(\App\Models\Post $post)
    {
        $this->selectedPost = $post;
    }

    public function closeModal()
    {
        $this->selectedPost = null;
    }

    public function togglePostGeneration()
    {
        $this->postGenerationEnabled = !$this->postGenerationEnabled;
        \App\Models\Setting::set('post_generation_enabled', $this->postGenerationEnabled);

        $status = $this->postGenerationEnabled ? 'activée' : 'désactivée';
        session()->flash('success', "La génération de posts a été {$status}.");
    }

    public function render()
    {
        $totalUsers = User::count();
        $totalPosts = Post::whereIn('status', ['completed', 'processing', 'pending'])->count();
        $failedPosts = Post::where('status', 'failed')->count();
        $activeSubscriptions = Subscription::where('stripe_status', 'active')->count();

        $recentUsers = User::with('company')->latest()->take(50)->get();
        $recentPosts = Post::with('user')->latest()->take(50)->get();

        $zernioStats = null;
        $zernioRenewalDate = null;
        $zernioDaysUntilRenewal = null;
        try {
            $zernioStats = app(ZernioService::class)->getUsageStats();
            if (!empty($zernioStats)) {
                $anchorDay = $zernioStats['billingAnchorDay'] ?? 1;
                $renewal = Carbon::createFromDate(now()->year, now()->month, $anchorDay);
                if ($renewal->isPast() || $renewal->isToday()) {
                    $renewal->addMonth();
                }
                $zernioRenewalDate = $renewal->isoFormat('D MMMM YYYY');
                $zernioDaysUntilRenewal = (int) now()->diffInDays($renewal);
            }
        } catch (\Exception $e) {
            Log::error('Admin dashboard: failed to fetch Zernio usage stats', ['error' => $e->getMessage()]);
        }

        return view('livewire.admin.dashboard', compact(
            'totalUsers',
            'totalPosts',
            'failedPosts',
            'activeSubscriptions',
            'recentUsers',
            'recentPosts',
            'zernioStats',
            'zernioRenewalDate',
            'zernioDaysUntilRenewal'
        ));
    }
}
