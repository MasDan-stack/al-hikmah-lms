<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'specialization',
        'bio',
        'rating',
        'is_active',
        'default_max_students_per_day',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'is_active' => 'boolean',
            'default_max_students_per_day' => 'integer',
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
