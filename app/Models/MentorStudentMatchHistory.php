<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorStudentMatchHistory extends Model
{
    protected $fillable = [
        'mentor_id',
        'student_id',
        'enrollment_id',
        'initial_match_score',
        'retention_weeks',
        'average_rating_received',
        'is_completed_successfully',
        'requested_mutation',
        'mutation_reason',
    ];

    protected function casts(): array
    {
        return [
            'initial_match_score' => 'decimal:2',
            'average_rating_received' => 'decimal:2',
            'is_completed_successfully' => 'boolean',
            'requested_mutation' => 'boolean',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
