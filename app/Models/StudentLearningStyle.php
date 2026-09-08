<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLearningStyle extends Model
{
    protected $fillable = [
        'student_id',
        'visual_score',
        'auditory_score',
        'kinesthetic_score',
        'patience_need',
        'pace_preference',
        'dominant_style',
        'notes',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
