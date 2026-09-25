<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'mentor_id',
        'score',
        'breakdown',
        'selection_type',
        'selected_by',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'breakdown' => 'array',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function selectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'selected_by');
    }
}
