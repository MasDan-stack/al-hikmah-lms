<?php

namespace App\Models;

use Database\Factories\ProgressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progress extends Model
{
    /** @use HasFactory<ProgressFactory> */
    use HasFactory;

    protected $table = 'progress';

    protected $fillable = [
        'session_id',
        'student_id',
        'mentor_id',
        'kategori',
        'surah_start',
        'surah_end',
        'ayat_start',
        'ayat_end',
        'juz',
        'nilai_fluent',
        'nilai_tajwid',
        'nilai_adab',
        'is_mutqin_test',
        'juz_number',
        'catatan_evaluasi',
        'homework',
    ];

    protected $casts = [
        'is_mutqin_test' => 'boolean',
        'juz_number' => 'integer',
        'juz' => 'integer',
        'nilai_fluent' => 'integer',
        'nilai_tajwid' => 'integer',
        'nilai_adab' => 'integer',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Mentor, $this>
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    /**
     * @return BelongsTo<Session, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
