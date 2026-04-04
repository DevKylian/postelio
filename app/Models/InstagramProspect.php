<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramProspect extends Model
{
    protected $fillable = [
        'account_name',
        'profile_url',
        'status',
        'last_action_date',
        'notes',
    ];

    protected $casts = [
        'last_action_date' => 'datetime',
    ];

    public const STATUS_DISCOVERED = 'discovered';
    public const STATUS_FOLLOWED = 'followed';
    public const STATUS_DM_SENT = 'dm_sent';
    public const STATUS_RELANCE_1 = 'relance_1';
    public const STATUS_NOT_INTERESTED = 'not_interested';
    public const STATUS_RESPONDED = 'responded';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DISCOVERED => '1 - Trouvé',
            self::STATUS_FOLLOWED => '2 - Suivi & Commenté',
            self::STATUS_DM_SENT => '3 - DM Envoyé (Attente réponse)',
            self::STATUS_RELANCE_1 => '4 - Relance 48h',
            self::STATUS_NOT_INTERESTED => '5 - Non intéressé',
            self::STATUS_RESPONDED => '6 - Réponse reçue 🎉',
        ];
    }
}
