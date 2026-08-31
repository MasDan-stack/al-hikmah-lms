<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorPerformanceSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'period_type',
        'period_start',
        'period_end',
        'total_students',
        'active_students',
        'retention_rate',
        'dropout_rate',
        'avg_tajwid_score',
        'avg_adab_score',
        'academic_quality_score',
        'total_sessions',
        'completed_sessions',
        'attendance_rate',
        'avg_rating_raw',
        'avg_rating_bayesian',
        'total_feedback_count',
        'target_achievement_rate',
        'handicap_bonus_points',
        'composite_score',
        'rank_position',
        'is_locked',
        'calculated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'retention_rate' => 'float',
        'dropout_rate' => 'float',
        'avg_tajwid_score' => 'float',
        'avg_adab_score' => 'float',
        'academic_quality_score' => 'float',
        'attendance_rate' => 'float',
        'avg_rating_raw' => 'float',
        'avg_rating_bayesian' => 'float',
        'target_achievement_rate' => 'float',
        'handicap_bonus_points' => 'float',
        'composite_score' => 'float',
        'is_locked' => 'boolean',
        'calculated_at' => 'datetime',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
