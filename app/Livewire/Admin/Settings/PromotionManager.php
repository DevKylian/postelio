<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PromotionManager extends Component
{
    public bool   $promo_active                   = false;
    public int    $promo_discount                 = 20;
    public string $promo_label                    = 'PROMO';
    public string $promo_stripe_promotion_code_id = '';

    public function mount(): void
    {
        $this->promo_active                   = (bool) Setting::get('promo_active', false);
        $this->promo_discount                 = (int)  Setting::get('promo_discount', 20);
        $this->promo_label                    = (string) Setting::get('promo_label', 'PROMO');
        $this->promo_stripe_promotion_code_id = (string) Setting::get('promo_stripe_promotion_code_id', '');
    }

    public function save(): void
    {
        $this->validate([
            'promo_discount' => 'required|integer|min:1|max:99',
            'promo_label'    => 'required|string|max:30',
        ]);

        Setting::set('promo_active',                   $this->promo_active);
        Setting::set('promo_discount',                 $this->promo_discount);
        Setting::set('promo_label',                    trim($this->promo_label));
        Setting::set('promo_stripe_promotion_code_id', trim($this->promo_stripe_promotion_code_id));

        session()->flash('success', 'La promotion a été mise à jour.');
    }

    public function render()
    {
        $plans = config('subscription.plans');

        return view('livewire.admin.settings.promotion-manager', ['plans' => $plans]);
    }
}
