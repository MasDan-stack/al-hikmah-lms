<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorProbationTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'start_date',
        'end_date',
        'duration_months',
        'orientation_completed',
        'system_training_completed',
        'first_session_conducted',
        'training_modules_completed',
        'training_modules_required',
        'total_sessions_conducted',
        'active_students_assigned',
        'average_rating',
        'attendance_rate',
        'mid_review_date',
        'mid_review_notes',
        'final_evaluation_date',
        'final_decision',
        'final_notes',
        'evaluated_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'mid_review_date' => 'date',
            'final_evaluation_date' => 'date',
            'duration_months' => 'integer',
            'orientation_completed' => 'boolean',
            'system_training_completed' => 'boolean',
            'first_session_conducted' => 'boolean',
            'training_modules_completed' => 'integer',
            'training_modules_required' => 'integer',
            'total_sessions_conducted' => 'integer',
            'active_students_assigned' => 'integer',
            'average_rating' => 'float',
            'attendance_rate' => 'float',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
