<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'audio_path',
        'image_path',
        'transcription',
        'refined_content',
        'template',
        'status',
        'image_before_path',
        'image_after_path',
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
