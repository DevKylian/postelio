<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AnnouncementManager extends Component
{
    public string $text = '';
    public string $link_text = '';
    public string $route = '';
    public bool $is_active = false;

    public function mount()
    {
        $this->text = Setting::get('announcement_text', 'Recherche 10 artisans pour tester gratuitement l\'app !');
        $this->link_text = Setting::get('announcement_link_text', 'Rejoindre le test →');
        $this->route = Setting::get('announcement_route', 'register');
        $this->is_active = Setting::get('announcement_is_active', true);
    }

    public function save()
    {
        Setting::set('announcement_text', $this->text);
        Setting::set('announcement_link_text', $this->link_text);
        Setting::set('announcement_route', $this->route);
        Setting::set('announcement_is_active', $this->is_active);

        session()->flash('success', 'Le bandeau d\'annonce a été mis à jour.');
    }

    public function render()
    {
        $availableRoutes = [
            'subscription.plans' => 'Tarifs / Plans',
            'dashboard' => 'Tableau de bord',
            'generate-post' => 'Générer un post',
            'blog.index' => 'Blog',
            'calendar' => 'Calendrier',
            'statistics' => 'Statistiques',
            'register' => 'Inscription',
            'login' => 'Connexion',
        ];

        return view('livewire.admin.settings.announcement-manager', [
            'availableRoutes' => $availableRoutes
        ]);
    }
}
