<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorPedagogicalProfile extends Model
{
    protected $fillable = [
        'mentor_id',
        'visual_capability',
        'auditory_capability',
        'kinesthetic_capability',
        'patience_rating',
        'energy_level',
        'preferred_age_group',
        'historical_retention_rate',
    ];

    protected function casts(): array
    {
        return [
            'historical_retention_rate' => 'decimal:2',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
