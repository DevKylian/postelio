<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterLog extends Model
{
    protected $fillable = [
        'subject',
        'content',
        'audience',
        'recipients_count',
        'status',
        'sent_by',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function getAudienceLabelAttribute(): string
    {
        return match($this->audience) {
            'all'   => 'Tous les abonnés',
            'free'  => 'Plan Gratuit',
            'basic' => 'Plan Basic',
            'pro'   => 'Plan Pro',
            default => ucfirst($this->audience),
        };
    }
}
