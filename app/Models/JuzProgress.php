<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JuzProgress extends Model
{
    use HasFactory;

    protected $table = 'juz_progress';

    protected $fillable = [
        'student_id',
        'juz_number',
        'total_ayat',
        'ayat_hafal',
        'percentage',
        'status',
        'started_at',
        'completed_at',
        'mutqin_at',
    ];

    protected $casts = [
        'juz_number' => 'integer',
        'total_ayat' => 'integer',
        'ayat_hafal' => 'integer',
        'percentage' => 'float',
        'started_at' => 'date',
        'completed_at' => 'date',
        'mutqin_at' => 'date',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
