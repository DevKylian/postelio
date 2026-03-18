<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'city',
        'trade',
        'brand_color',
        'logo_path',
        'instagram_access_token',
        'instagram_business_account_id',
        'instagram_username',
        'instagram_token_expires_at',
    ];

    protected $casts = [
        'instagram_token_expires_at' => 'datetime',
    ];

    public function getInstagramConnectedAttribute(): bool
    {
        return !empty($this->instagram_access_token)
            && !empty($this->instagram_business_account_id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
