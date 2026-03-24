<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Laravel\Cashier\Subscription;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public ?\App\Models\Post $selectedPost = null;

    public function selectPost(\App\Models\Post $post)
    {
        $this->selectedPost = $post;
    }

    public function closeModal()
    {
        $this->selectedPost = null;
    }

    public function render()
    {
        $totalUsers = User::count();
        $totalPosts = Post::whereIn('status', ['completed', 'processing', 'pending'])->count();
        $failedPosts = Post::where('status', 'failed')->count();
        $activeSubscriptions = Subscription::where('stripe_status', 'active')->count();

        $recentUsers = User::with('company')->latest()->take(50)->get();
        $recentPosts = Post::with('user')->latest()->take(50)->get();

        return view('livewire.admin.dashboard', compact(
            'totalUsers',
            'totalPosts',
            'failedPosts',
            'activeSubscriptions',
            'recentUsers',
            'recentPosts'
        ));
    }
}
