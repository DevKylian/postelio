<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SocialSettings extends Component
{
    public bool $instagram_enabled = true;
    public bool $facebook_enabled = true;

    public function mount(): void
    {
        $this->instagram_enabled = (bool) Setting::get('instagram_connection_enabled', true);
        $this->facebook_enabled  = (bool) Setting::get('facebook_connection_enabled', true);
    }

    public function save(): void
    {
        Setting::set('instagram_connection_enabled', $this->instagram_enabled);
        Setting::set('facebook_connection_enabled', $this->facebook_enabled);

        session()->flash('success', 'Paramètres des réseaux sociaux mis à jour.');
    }

    public function render()
    {
        return view('livewire.admin.settings.social-settings');
    }
}
