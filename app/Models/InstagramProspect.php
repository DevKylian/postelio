<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramProspect extends Model
{
    protected $fillable = [
        'account_name',
        'profile_url',
        'status',
        'script_version',
        'current_node_id',
        'last_action_date',
        'notes',
    ];

    protected $casts = [
        'last_action_date' => 'datetime',
    ];

    public const STATUS_DISCOVERED    = 'discovered';
    public const STATUS_FOLLOWED      = 'followed';
    public const STATUS_DM_SENT       = 'dm_sent';
    public const STATUS_RELANCE_1     = 'relance_1';
    public const STATUS_NOT_INTERESTED = 'not_interested';
    public const STATUS_RESPONDED     = 'responded';

    public function script()
    {
        return $this->belongsTo(SalesScript::class, 'script_version');
    }

    public function getCurrentNode(): ?array
    {
        if (!$this->current_node_id) return null;
        $script = $this->relationLoaded('script') ? $this->script : SalesScript::find($this->script_version);
        if (!$script || !$script->flow) return null;
        return $script->flow['nodes'][$this->current_node_id] ?? null;
    }

    public function getAvailableBranches(): array
    {
        $node = $this->getCurrentNode();
        return $node['branches'] ?? [];
    }

    public function needsFollowUp(int $hours = 48): bool
    {
        if (!$this->last_action_date) return false;

        if ($this->current_node_id) {
            return $this->last_action_date->copy()->addHours($hours)->isPast();
        }

        return ($this->status === self::STATUS_DM_SENT && $this->last_action_date->copy()->addHours(48)->isPast())
            || ($this->status === self::STATUS_RELANCE_1 && $this->last_action_date->copy()->addHours(72)->isPast());
    }

    public static function getScripts(): array
    {
        $dbScripts = SalesScript::all()->pluck('name', 'id')->toArray();
        if (!empty($dbScripts)) {
            return $dbScripts;
        }

        // Fallback for legacy
        $scripts = [];
        for ($i = 1; $i <= 10; $i++) {
            $scripts[$i] = "Script $i";
        }
        return $scripts;
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DISCOVERED    => '1 - Trouvé',
            self::STATUS_FOLLOWED      => '2 - Suivi & Commenté',
            self::STATUS_DM_SENT       => '3 - DM Envoyé (Attente réponse)',
            self::STATUS_RELANCE_1     => '4 - Relance 48h',
            self::STATUS_NOT_INTERESTED => '5 - Non intéressé',
            self::STATUS_RESPONDED     => '6 - Réponse reçue 🎉',
        ];
    }
}
