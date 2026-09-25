<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HifzMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'mentor_id',
        'name',
        'target_type',
        'target_date',
        'progress_current',
        'progress_goal',
        'status',
        'achieved_at',
    ];

    protected $casts = [
        'target_date' => 'datetime',
        'progress_current' => 'integer',
        'progress_goal' => 'integer',
        'achieved_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
