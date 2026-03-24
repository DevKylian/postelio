<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class RecentActivities extends Component
{
    public function render()
    {
        $activities = Activity::where('user_id', Auth::id())
            ->orWhere('company_id', Auth::user()->company_id)
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.recent-activities', compact('activities'));
    }
}
