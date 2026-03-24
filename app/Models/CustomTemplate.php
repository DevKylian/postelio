<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'description',
        'ai_prompt',
        'visual_style',
        'layout_type',
        'photo_mode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
