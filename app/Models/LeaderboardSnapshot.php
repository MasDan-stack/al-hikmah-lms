<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_type',
        'period_start',
        'period_end',
        'category',
        'student_id',
        'rank_position',
        'total_points',
        'total_ayat',
        'total_juz_mutqin',
        'current_streak',
        'trend',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'rank_position' => 'integer',
        'total_points' => 'integer',
        'total_ayat' => 'integer',
        'total_juz_mutqin' => 'integer',
        'current_streak' => 'integer',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
