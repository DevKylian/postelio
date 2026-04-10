<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'city',
        'phone',
        'email',
        'website',
        'trade',
        'brand_color',
        'secondary_color',
        'bio_text',
        'bio_audio_path',
        'logo_path',
        'instagram_access_token',
        'instagram_business_account_id',
        'instagram_username',
        'instagram_profile_picture_url',
        'instagram_token_expires_at',
        'facebook_access_token',
        'facebook_page_id',
        'facebook_page_name',
        'facebook_page_picture_url',
        'weekly_posts_goal',
        'zernio_profile_id',
        'zernio_facebook_account_id',
        'zernio_instagram_account_id',
    ];

    protected $casts = [
        'instagram_token_expires_at' => 'datetime',
    ];

    public function getInstagramConnectedAttribute(): bool
    {
        return !empty($this->zernio_instagram_account_id);
    }

    public function getFacebookConnectedAttribute(): bool
    {
        return !empty($this->zernio_facebook_account_id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create a Zernio Profile ID for the company.
     */
    public function getZernioProfileId(): string
    {
        if ($this->zernio_profile_id) {
            return $this->zernio_profile_id;
        }

        return app(\App\Services\ZernioService::class)->createProfile($this);
    }
}
