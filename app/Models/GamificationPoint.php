<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamificationPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'points',
        'source_type',
        'source_id',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
        'source_id' => 'integer',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
