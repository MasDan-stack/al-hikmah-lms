<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentorFeedback extends Model
{
    use HasFactory;

    protected $table = 'mentor_feedback';

    protected $fillable = [
        'mentor_id',
        'student_id',
        'parent_id',
        'session_id',
        'overall_rating',
        'comment',
        'quick_tags',
        'is_anonymous',
        'mentor_response',
        'responded_at',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'quick_tags' => 'array',
        'is_anonymous' => 'boolean',
        'responded_at' => 'datetime',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(MentorFeedbackRating::class, 'feedback_id');
    }
}
