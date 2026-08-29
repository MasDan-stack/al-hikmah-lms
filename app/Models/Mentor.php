<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_id',
        'full_name',
        'specialization',
        'bio',
        'rating',
        'join_date',
        'probation_end_date',
        'status',
        'is_active',
        'is_trainer',
        'default_max_students_per_day',
        'sanad_chain',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'emergency_contact',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'is_active' => 'boolean',
            'is_trainer' => 'boolean',
            'default_max_students_per_day' => 'integer',
            'join_date' => 'date',
            'probation_end_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availabilities()
    {
        return $this->hasMany(MentorAvailability::class, 'mentor_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'mentor_student')
            ->withPivot(['day_assigned', 'time_assigned', 'is_active'])
            ->withTimestamps();
    }

    public function activityLogs()
    {
        return $this->hasMany(MentorActivityLog::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(MentorApplication::class, 'application_id');
    }

    public function probationTracking(): HasOne
    {
        return $this->hasOne(MentorProbationTracking::class, 'mentor_id');
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(MentorTraining::class, 'mentor_id');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(MentorLeave::class, 'mentor_id');
    }

    public function substituteLeaves(): HasMany
    {
        return $this->hasMany(MentorLeave::class, 'substitute_mentor_id');
    }

    public function isAvailableOn(string $day): bool
    {
        $availability = $this->availabilities()->onDay($day)->first();

        return $availability ? $availability->isAvailable() : true;
    }

    public function getStudentCountOnDay(string $day): int
    {
        return $this->students()
            ->wherePivot('day_assigned', $day)
            ->wherePivot('is_active', true)
            ->count();
    }

    public function hasQuotaOnDay(string $day): bool
    {
        $availability = $this->availabilities()->onDay($day)->first();
        if ($availability && ! $availability->isAvailable()) {
            return false;
        }

        $maxStudents = $availability?->max_students ?? $this->default_max_students_per_day ?? 5;
        $currentCount = $this->getStudentCountOnDay($day);

        return $currentCount < $maxStudents;
    }

    public function getAvailableDays(): array
    {
        return $this->availabilities()
            ->available()
            ->pluck('day')
            ->toArray();
    }

    public function getDisplayName(): string
    {
        return $this->user?->name ?? $this->full_name ?? 'Mentor';
    }
}
