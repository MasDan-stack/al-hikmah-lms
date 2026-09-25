<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Relasi ke Program melalui Enrollment santri binaan
     */
    public function program()
    {
        return $this->hasOneThrough(
            Program::class,
            Enrollment::class,
            'student_id',
            'id',
            'student_id',
            'program_id'
        );
    }

    /**
     * Dapatkan enrollment aktif santri yang relevan dengan sesi bimbingan ini.
     */
    public function getEnrollmentAttribute(): ?Enrollment
    {
        if ($this->relationLoaded('student') && $this->student && $this->student->relationLoaded('enrollments')) {
            return $this->student->enrollments->firstWhere('mentor_id', $this->mentor_id)
                ?? $this->student->enrollments->whereIn('status', [EnrollmentStatus::ACTIVE, EnrollmentStatus::CONFIRMED])->first()
                ?? $this->student->enrollments->first();
        }

        return Enrollment::where('student_id', $this->student_id)
            ->where(function ($q) {
                $q->where('mentor_id', $this->mentor_id)
                    ->orWhereIn('status', [EnrollmentStatus::ACTIVE->value, EnrollmentStatus::CONFIRMED->value]);
            })
            ->latest()
            ->first() ?? Enrollment::where('student_id', $this->student_id)->latest()->first();
    }

    /**
     * Dapatkan objek Program riil yang diikuti santri binaan.
     */
    public function getProgramAttribute(): ?Program
    {
        if ($this->enrollment?->program) {
            return $this->enrollment->program;
        }

        if ($this->student_id && $this->mentor_id) {
            $pivot = DB::table('mentor_student')
                ->where('student_id', $this->student_id)
                ->where('mentor_id', $this->mentor_id)
                ->whereNotNull('program_id')
                ->first();
            if ($pivot && $pivot->program_id) {
                $prog = Program::find($pivot->program_id);
                if ($prog) {
                    return $prog;
                }
            }
        }

        return $this->relationLoaded('student') && $this->student?->relationLoaded('programs')
            ? $this->student->programs->first()
            : $this->student?->programs()->first();
    }
}
