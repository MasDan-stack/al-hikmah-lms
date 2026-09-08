<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'badge_id',
        'earned_at',
        'trigger_data',
        'announced_to_parent',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'trigger_data' => 'array',
        'announced_to_parent' => 'boolean',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Badge, $this>
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}
