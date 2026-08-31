<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMutationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'student_id',
        'previous_mentor_id',
        'new_mentor_id',
        'reason_category',
        'notes',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function previousMentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'previous_mentor_id');
    }

    public function newMentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'new_mentor_id');
    }
}
