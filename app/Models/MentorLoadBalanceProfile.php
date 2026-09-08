<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorLoadBalanceProfile extends Model
{
    protected $fillable = [
        'mentor_id',
        'employment_type',
        'max_active_students',
        'max_daily_slots',
        'current_active_students',
        'burnout_risk_score',
        'burnout_level',
        'is_throttled',
        'coaching_cooldown_until',
        'cooldown_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_throttled' => 'boolean',
            'coaching_cooldown_until' => 'datetime',
            'burnout_risk_score' => 'decimal:2',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
