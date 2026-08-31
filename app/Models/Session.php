<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    // 👇 Tentukan nama tabel yang benar
    protected $table = 'learning_sessions';

    protected $fillable = [
        'student_id',
        'mentor_id',
        'date',
        'time',
        'method',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }

    public function confirmation()
    {
        return $this->hasOne(SessionConfirmation::class, 'session_id');
    }

    public function feedback()
    {
        return $this->hasOne(MentorFeedback::class, 'session_id');
    }
}
