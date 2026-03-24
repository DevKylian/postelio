<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Changelog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'version',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'feature'     => 'emerald',
            'improvement' => 'amber',
            'fix'         => 'rose',
            default       => 'blue',
        };
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'feature'     => 'Nouveauté',
            'improvement' => 'Amélioration',
            'fix'         => 'Correction',
            default       => 'Mise à jour',
        };
    }
}
