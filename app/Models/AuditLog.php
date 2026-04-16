<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'ip',
        'method',
        'url',
        'tag',
        'city',
        'country',
        'rank',
        'user_agent',
        'payload',
        'response_time',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
