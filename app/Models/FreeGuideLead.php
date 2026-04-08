<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeGuideLead extends Model
{
    protected $fillable = [
        'free_guide_id',
        'email',
    ];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(FreeGuide::class, 'free_guide_id');
    }
}
