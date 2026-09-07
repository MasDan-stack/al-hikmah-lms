<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AhpEvaluationSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_month',
        'mentor_id',
        'c1_discipline_score',
        'c2_pedagogy_score',
        'c3_morals_score',
        'c4_satisfaction_score',
        'c5_involvement_score',
        'final_ahp_score',
        'rank_position',
        'reward_amount',
        'admin_notes',
        'is_announced',
        'announced_at',
        'calculated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'c1_discipline_score' => 'float',
            'c2_pedagogy_score' => 'float',
            'c3_morals_score' => 'float',
            'c4_satisfaction_score' => 'float',
            'c5_involvement_score' => 'float',
            'final_ahp_score' => 'float',
            'rank_position' => 'integer',
            'reward_amount' => 'float',
            'is_announced' => 'boolean',
            'announced_at' => 'datetime',
            'calculated_at' => 'datetime',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
