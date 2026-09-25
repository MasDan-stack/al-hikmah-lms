<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLearningVelocity extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'calculation_date',
        'velocity_ayat_per_day',
        'target_velocity',
        'velocity_status',
        'target_achievement_percent',
        'days_active',
        'total_ayat_30d',
        'estimated_days_remaining',
        'projected_completion_date',
        'percentile_rank',
    ];

    protected function casts(): array
    {
        return [
            'calculation_date' => 'date',
            'velocity_ayat_per_day' => 'float',
            'target_velocity' => 'float',
            'target_achievement_percent' => 'float',
            'days_active' => 'integer',
            'total_ayat_30d' => 'integer',
            'estimated_days_remaining' => 'integer',
            'projected_completion_date' => 'date',
            'percentile_rank' => 'float',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
