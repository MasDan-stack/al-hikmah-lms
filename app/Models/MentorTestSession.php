<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorTestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'session_type',
        'scheduled_at',
        'duration_minutes',
        'mode',
        'meeting_link',
        'location',
        'score',
        'grade',
        'evaluator_notes',
        'evaluator_id',
        'status',
        'completed_at',
        'ai_question_payload',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
            'score' => 'float',
            'duration_minutes' => 'integer',
            'ai_question_payload' => 'array',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(MentorApplication::class, 'application_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
