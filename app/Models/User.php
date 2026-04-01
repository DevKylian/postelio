<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class User extends Authenticatable implements MustVerifyEmail
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
        'google_id',
        'google_token',
        'google_refresh_token',
        'avatar',
        'is_admin',
        'is_premium',
        'is_tester',
        'manual_plan',
        'is_banned',
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
            'is_admin' => 'boolean',
            'is_premium' => 'boolean',
            'is_tester' => 'boolean',
            'is_banned' => 'boolean',
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

    public function getTotalCreditsAttribute($value): int
    {
        return $this->planCredits();
    }

    public function getRemainingCreditsAttribute(): int
    {
        return max(0, $this->total_credits - $this->used_credits);
    }

    public function hasCredits(int $amount): bool
    {
        if ($this->is_admin || $this->is_premium) {
            return true;
        }
        return $this->remaining_credits >= $amount;
    }

    public function deductCredits(int $amount): void
    {
        if ($this->is_admin) {
            return;
        }
        
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
            if (!empty($plan['yearly_stripe_price_id']) && $subscription->hasPrice($plan['yearly_stripe_price_id'])) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Get target role key for permissions.
     */
    public function getRoleKey(): string
    {
        if ($this->is_admin) return 'admin';
        
        // 1. Manual plan set by Admin (Priority 1)
        if ($this->manual_plan) return $this->manual_plan;
        
        // 2. Active Stripe Subscription (Priority 2)
        $planKey = $this->currentPlanKey();
        if ($planKey) return $planKey;
        
        // 3. Tester role (Priority 3)
        if ($this->is_tester) return 'tester';
        
        return 'free';
    }

    /**
     * Get a specific permission value for this user.
     */
    public function getPermission(string $key, $default = null)
    {
        $roleKey = $this->getRoleKey();
        $permissions = RolePermission::getForRole($roleKey);
        
        return $permissions->$key ?? $default;
    }

    /**
     * Get credits allowed by the current plan/role.
     */
    public function planCredits(): int
    {
        return (int) $this->getPermission('monthly_quota', 3);
    }

    /**
     * Whether the user has an active paid subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscribed('default');
    }

    /**
     * Whether the user can connect and publish to Instagram/Facebook.
     */
    public function canPublishToSocialMedia(): bool
    {
        return (bool) $this->getPermission('can_connect_social', false);
    }

    /**
     * Whether the user can access all visual templates (premium ones).
     */
    public function hasFullTemplateAccess(): bool
    {
        if ($this->is_admin) return true;
        return (bool) $this->getPermission('has_premium_templates', false);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );

        Mail::to($this)->send(new WelcomeMail($this, $verificationUrl));
    }
}
