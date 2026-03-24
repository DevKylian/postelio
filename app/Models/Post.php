<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const STATUS_PENDING         = 'pending';
    const STATUS_PROCESSING      = 'processing';
    const STATUS_REVIEW_PENDING  = 'review_pending';
    const STATUS_COMPLETED       = 'completed';
    const STATUS_PUBLISHED       = 'published';
    const STATUS_FAILED          = 'failed';

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
        'zernio_id',
        'error_message',
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

    public function getStatusLabelAttribute()
    {
        if ($this->status === self::STATUS_COMPLETED && $this->scheduled_at && $this->scheduled_at->isFuture()) {
            return 'Programmé';
        }

        if ($this->status === self::STATUS_COMPLETED && $this->zernio_id) {
            return 'Envoi...';
        }

        return match ($this->status) {
            self::STATUS_PENDING        => 'En attente',
            self::STATUS_PROCESSING     => 'Génération...',
            self::STATUS_REVIEW_PENDING => 'Prêt pour revue',
            self::STATUS_COMPLETED      => 'Prêt',
            self::STATUS_PUBLISHED      => 'En ligne',
            self::STATUS_FAILED         => 'Échec',
            default                     => 'Inconnu',
        };
    }

    public function getStatusColorClassAttribute()
    {
        if ($this->status === self::STATUS_COMPLETED && $this->scheduled_at && $this->scheduled_at->isFuture()) {
            return 'bg-amber-50 text-amber-600 border-amber-200';
        }

        if ($this->status === self::STATUS_COMPLETED && $this->zernio_id) {
            return 'bg-blue-50 text-blue-600 border-blue-200';
        }

        return match ($this->status) {
            self::STATUS_PENDING        => 'bg-gray-100 text-gray-600 border-gray-200',
            self::STATUS_PROCESSING     => 'bg-blue-50 text-blue-600 border-blue-200 animate-pulse',
            self::STATUS_REVIEW_PENDING => 'bg-amber-50 text-amber-600 border-amber-200',
            self::STATUS_COMPLETED      => 'bg-gray-50 text-gray-400 border-gray-100',
            self::STATUS_PUBLISHED      => 'bg-emerald-50 text-emerald-600 border-emerald-200',
            self::STATUS_FAILED         => 'bg-red-50 text-red-600 border-red-200',
            default                     => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }
}
