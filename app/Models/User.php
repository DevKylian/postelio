<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
