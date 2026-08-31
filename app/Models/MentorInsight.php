<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'period',
        'ai_summary',
        'coaching_recommendations',
        'risk_level',
        'predicted_score_next_month',
        'ai_model_used',
        'generated_at',
    ];

    protected $casts = [
        'coaching_recommendations' => 'array',
        'predicted_score_next_month' => 'float',
        'generated_at' => 'datetime',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
