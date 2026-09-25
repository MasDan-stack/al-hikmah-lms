<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorFeedbackRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_id',
        'category',
        'rating',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(MentorFeedback::class, 'feedback_id');
    }
}
