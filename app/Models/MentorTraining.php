<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'title',
        'category',
        'instructor_name',
        'training_date',
        'duration_hours',
        'certificate_path',
        'badge_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'training_date' => 'date',
            'duration_hours' => 'float',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }
}
