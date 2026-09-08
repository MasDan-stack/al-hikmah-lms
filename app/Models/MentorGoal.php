<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'goal_type',
        'title',
        'target_value',
        'current_value',
        'period',
        'status',
        'achieved_at',
    ];

    protected $casts = [
        'target_value' => 'float',
        'current_value' => 'float',
        'achieved_at' => 'datetime',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
