<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'user_id',
        'topic',
        'difficulty',
        'question',
        'options',
        'correct_answer',
        'explanation',
        'created_by_ai',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'integer',
            'created_by_ai' => 'boolean',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias relasi mentor untuk kemudahan sintaks
     */
    public function mentor(): BelongsTo
    {
        return $this->user();
    }

    public function getCorrectOptionLabelAttribute(): string
    {
        $labels = ['A', 'B', 'C', 'D'];

        return $labels[$this->correct_answer] ?? 'A';
    }

    public function getCorrectOptionTextAttribute(): string
    {
        return $this->options[$this->correct_answer] ?? '-';
    }
}
