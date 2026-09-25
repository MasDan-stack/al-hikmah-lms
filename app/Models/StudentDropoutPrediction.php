<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDropoutPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'prediction_date',
        'risk_score',
        'risk_level',
        'attendance_score',
        'payment_score',
        'progress_score',
        'engagement_score',
        'risk_factors',
        'recommendations',
        'is_alerted',
        'alerted_at',
    ];

    protected function casts(): array
    {
        return [
            'prediction_date' => 'date',
            'risk_score' => 'float',
            'attendance_score' => 'float',
            'payment_score' => 'float',
            'progress_score' => 'float',
            'engagement_score' => 'float',
            'risk_factors' => 'array',
            'recommendations' => 'array',
            'is_alerted' => 'boolean',
            'alerted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
