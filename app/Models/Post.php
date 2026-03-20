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
        'facebook_image_path',
        'transcription',
        'refined_content',
        'template',
        'template_options',
        'platforms',
        'status',
        'scheduled_at',
        'image_before_path',
        'image_after_path',
        'slides',
        'facebook_slides',
        'extra_images',
    ];

    protected $casts = [
        'slides'           => 'array',
        'facebook_slides'  => 'array',
        'extra_images'     => 'array',
        'template_options' => 'array',
        'platforms'        => 'array',
        'scheduled_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getCaptionAttribute()
    {
        $content = $this->refined_content;
        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['caption'])) {
            return $decoded['caption'];
        }

        return $content;
    }
}
