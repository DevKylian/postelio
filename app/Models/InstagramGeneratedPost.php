<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstagramGeneratedPost extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instagram_generated_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'template',
        'topic',
        'content_data',
        'caption',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'content_data' => 'array',
    ];

    /**
     * Get the user that owns the generated post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
