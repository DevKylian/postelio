<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\OpenAIMonitorService;
use App\Services\ZernioService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

class RevenueSimulator extends Component
{
    // Mode: 'actual' or 'simulator'
    public $mode = 'actual';
    
    // Financial Constants
    public const TAX_RATE = 0.212; // Auto-entrepreneur (Prestation de service)
    public const VPS_COST = 8.00;
    public const STRIPE_FIXED_FEE = 0.25;
    public const STRIPE_PERCENT_FEE = 0.015;

    // Simulation Inputs
    public $simBasicMo = 5;
    public $simProMo = 2;
    public $simBasicYr = 1;
    public $simProYr = 0;
    public $simFree = 20; // Number of free users for Zernio calculation
    
    // Growth Inputs (per month)
    public $growthBasicMo = 2;
    public $growthProMo = 1;
    public $growthBasicYr = 0;
    public $growthProYr = 0;

    // Actual Data (Cached/Loaded once)
    public $actualCounts = [];
    public $actualOpenAICost = 0;
    public $actualOpenAICostPerUser = 0;
    public $actualZernioCost = 0;

    // Toggle for Net vs Brut display
    public $isNet = true;

    public function updated($propertyName)
    {
        $this->dispatch('chart-updated', 
            projections: $this->projections,
            isNet: $this->isNet
        );
    }

    public function mount()
    {
        $this->loadActualData();
    }

    public function loadActualData()
    {
        $basicPriceId = config('subscription.plans.basic.stripe_price_id');
        $basicYrPriceId = config('subscription.plans.basic.yearly_stripe_price_id');
        $proPriceId = config('subscription.plans.pro.stripe_price_id');
        $proYrPriceId = config('subscription.plans.pro.yearly_stripe_price_id');

        // Count active subscriptions by price ID
        $this->actualCounts = [
            'basicMo' => $this->countSubscriptions($basicPriceId),
            'basicYr' => $this->countSubscriptions($basicYrPriceId),
            'proMo'   => $this->countSubscriptions($proPriceId),
            'proYr'   => $this->countSubscriptions($proYrPriceId),
            'free'    => User::whereDoesntHave('subscriptions')->count(),
        ];

        // Load costs
        $openai = app(OpenAIMonitorService::class)->getMonthlyUsage();
        $this->actualOpenAICost = $openai['total_usd'] ?? 0;

        // Calculate Average OpenAI Cost per user
        $actualTotalUsers = User::count();
        $this->actualOpenAICostPerUser = $actualTotalUsers > 0 ? ($this->actualOpenAICost / $actualTotalUsers) : 0.05;

        // Load Zernio (we can estimate based on total users or fetch real plan)
        $proUsers = $this->actualCounts['proMo'] + $this->actualCounts['proYr'];
        $this->actualZernioCost = $this->calculateZernioCost($proUsers);
        
        // Sync simulator values with actual data for a starting point
        $this->simBasicMo = $this->actualCounts['basicMo'];
        $this->simProMo = $this->actualCounts['proMo'];
        $this->simBasicYr = $this->actualCounts['basicYr'];
        $this->simProYr = $this->actualCounts['proYr'];
        $this->simFree = $this->actualCounts['free'];
    }

    private function countSubscriptions(?string $priceId): int
    {
        if (!$priceId) return 0;
        
        return DB::table('subscriptions')
            ->where('stripe_price', $priceId)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->count();
    }

    private function calculateZernioCost(int $proUsers): float
    {
        if ($proUsers === 0) return 0;
        if ($proUsers <= 3) return 19.00;
        
        return ceil($proUsers / 50) * 49.00;
    }

    public function setMode(string $mode)
    {
        $this->mode = $mode;
        $this->updated('mode');
    }

    public function toggleDisplay()
    {
        $this->isNet = !$this->isNet;
        $this->updated('isNet');
    }

    /**
     * Get the dynamic data for current view
     */
    public function getAnalyticsProperty()
    {
        $counts = $this->mode === 'actual' ? $this->actualCounts : [
            'basicMo' => (int) $this->simBasicMo,
            'proMo'   => (int) $this->simProMo,
            'basicYr' => (int) $this->simBasicYr,
            'proYr'   => (int) $this->simProYr,
            'free'    => (int) $this->simFree,
        ];

        return $this->calculateMetrics($counts);
    }

    private function calculateMetrics(array $counts): array
    {
        $revBasicMo = $counts['basicMo'] * 29;
        $revProMo = $counts['proMo'] * 49;
        $revBasicYr = $counts['basicYr'] * (276 / 12);
        $revProYr = $counts['proYr'] * (468 / 12);

        $mrrBrut = $revBasicMo + $revProMo + $revBasicYr + $revProYr;

        // Stripe Fees (1.5% + 0.25€)
        $stripeFees = ($counts['basicMo'] * (29 * 0.015 + 0.25))
            + ($counts['proMo'] * (49 * 0.015 + 0.25))
            + ($counts['basicYr'] * ((276 * 0.015 + 0.25) / 12))
            + ($counts['proYr'] * ((468 * 0.015 + 0.25) / 12));

        $totalUsers = array_sum($counts);
        $proUsers = $counts['proMo'] + $counts['proYr'];
        $basicUsers = $counts['basicMo'] + $counts['basicYr'];
        $freeUsers = $counts['free'];

        $zernioCost = $this->calculateZernioCost($proUsers);
        
        // Max OpenAI Cost Estimation (30 posts Pro, 15 posts Basic)
        // Est: 0.50€/Pro user, 0.20€/Basic user (includes variants & whisper)
        $simOpenAICost = ($proUsers * 0.50) + ($basicUsers * 0.20) + ($freeUsers * 0.05);

        // OpenAI Cost: Real if actual mode, or estimated MAX if simulator
        $openaiCost = $this->mode === 'actual' 
            ? $this->actualOpenAICost 
            : $simOpenAICost;
        
        $totalCosts = $zernioCost + self::VPS_COST + $stripeFees + $openaiCost;
        $taxes = $mrrBrut * self::TAX_RATE;
        $mrrNet = $mrrBrut - $taxes - $totalCosts;

        return [
            'mrrBrut' => $mrrBrut,
            'mrrNet' => $mrrNet,
            'totalCosts' => $totalCosts,
            'zernioCost' => $zernioCost,
            'stripeFees' => $stripeFees,
            'openaiCost' => $openaiCost,
            'taxes' => $taxes,
            'totalUsers' => $totalUsers,
            'payingUsers' => $totalUsers - $counts['free'],
        ];
    }

    /**
     * Calculate 36-month projection data for the chart
     */
    public function getProjectionsProperty()
    {
        $labels = [];
        $dataNet = [];
        $dataBasic = [];
        $dataPro = [];

        $currentCounts = [
            'basicMo' => (int) $this->simBasicMo,
            'proMo'   => (int) $this->simProMo,
            'basicYr' => (int) $this->simBasicYr,
            'proYr'   => (int) $this->simProYr,
            'free'    => (int) $this->simFree,
        ];

        $gBasicMo = (int) $this->growthBasicMo;
        $gProMo = (int) $this->growthProMo;
        $gBasicYr = (int) $this->growthBasicYr;
        $gProYr = (int) $this->growthProYr;

        for ($i = 0; $i <= 36; $i++) {
            $metrics = $this->calculateMetrics($currentCounts);
            
            $labels[] = $i === 0 ? "Auj." : "M+$i";
            $dataNet[] = round($metrics['mrrNet']);
            
            // For Brut mode, we want to split between Basic and Pro flavors
            $revBasic = ($currentCounts['basicMo'] * 29) + ($currentCounts['basicYr'] * 23);
            $revPro = ($currentCounts['proMo'] * 49) + ($currentCounts['proYr'] * 39);
            
            $dataBasic[] = round($revBasic);
            $dataPro[] = round($revPro);

            // Apply growth
            $currentCounts['basicMo'] += $gBasicMo;
            $currentCounts['proMo'] += $gProMo;
            $currentCounts['basicYr'] += $gBasicYr;
            $currentCounts['proYr'] += $gProYr;
        }

        return [
            'labels' => $labels,
            'net' => $dataNet,
            'basic' => $dataBasic,
            'pro' => $dataPro,
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.revenue-simulator');
    }
}
