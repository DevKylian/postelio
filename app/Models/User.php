<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'onboarding_completed',
        'total_credits',
        'used_credits',
        'last_quota_reset_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
            'last_quota_reset_at' => 'datetime',
            'total_credits' => 'integer',
            'used_credits' => 'integer',
        ];
    }

    /**
     * Check if user needs a quota reset (monthly)
     */
    public function checkAndResetQuota(): void
    {
        $now = now();
        
        if (!$this->last_quota_reset_at || $this->last_quota_reset_at->addMonth()->isPast()) {
            $this->update([
                'used_credits' => 0,
                'last_quota_reset_at' => $now,
            ]);
        }
    }

    public function getRemainingCreditsAttribute(): int
    {
        return max(0, $this->total_credits - $this->used_credits);
    }

    public function hasCredits(int $amount): bool
    {
        return $this->remaining_credits >= $amount;
    }

    public function deductCredits(int $amount): void
    {
        $this->increment('used_credits', $amount);
    }

    /**
     * Get the user's current subscription plan key (starter, pro, business) or null.
     */
    public function currentPlanKey(): ?string
    {
        $subscription = $this->subscription('default');

        if (!$subscription || !$subscription->valid()) {
            return null;
        }

        $plans = config('subscription.plans');

        foreach ($plans as $key => $plan) {
            if ($plan['stripe_price_id'] && $subscription->hasPrice($plan['stripe_price_id'])) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Get credits allowed by the current plan.
     */
    public function planCredits(): int
    {
        $planKey = $this->currentPlanKey();

        if (!$planKey) {
            return config('subscription.free_credits', 3);
        }

        return config("subscription.plans.{$planKey}.credits", 0);
    }

    /**
     * Whether the user has an active paid subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscribed('default');
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
