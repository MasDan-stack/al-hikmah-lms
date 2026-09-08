<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HifzTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'mentor_id',
        'learning_session_id',
        'target_date',
        'surah_name',
        'start_ayat',
        'end_ayat',
        'total_ayat',
        'notes',
        'scheduled_time',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'start_ayat' => 'integer',
        'end_ayat' => 'integer',
        'total_ayat' => 'integer',
        'completed_at' => 'datetime',
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

    /**
     * @return BelongsTo<Session, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'learning_session_id');
    }
}
