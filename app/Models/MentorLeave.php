<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'leave_date',
        'reason',
        'substitute_mentor_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'leave_date' => 'date',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function substituteMentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'substitute_mentor_id');
    }
}
