<?php

namespace App\Models;

use App\Services\MentorMatchingService;
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
        'gender',
        'specialization',
        'bio',
        'birth_date',
        'city',
        'education',
        'institution',
        'experience_years',
        'hifz_total_juz',
        'address',
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
        'latitude',
        'longitude',
        'specializations',
        'blocked_programs',
        'blocked_days',
        'max_students_per_day',
        'students_count',
        'last_coaching_alert_at',
        'coaching_needed',
        'coaching_urgency',
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
            'birth_date' => 'date',
            'experience_years' => 'integer',
            'hifz_total_juz' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'specializations' => 'array',
            'blocked_programs' => 'array',
            'blocked_days' => 'array',
            'max_students_per_day' => 'integer',
            'students_count' => 'integer',
            'last_coaching_alert_at' => 'datetime',
            'coaching_needed' => 'boolean',
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
            ->withPivot(['day_assigned', 'time_assigned', 'slot_number', 'time_label', 'program_id', 'notes', 'is_active'])
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

    public function mentorApplication(): BelongsTo
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

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'mentor_trainings', 'mentor_id', 'badge_id')
            ->withTimestamps();
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(MentorLeave::class, 'mentor_id');
    }

    public function substituteLeaves(): HasMany
    {
        return $this->hasMany(MentorLeave::class, 'substitute_mentor_id');
    }

    public function performanceSnapshots(): HasMany
    {
        return $this->hasMany(MentorPerformanceSnapshot::class, 'mentor_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(MentorFeedback::class, 'mentor_id');
    }

    public function insights(): HasMany
    {
        return $this->hasMany(MentorInsight::class, 'mentor_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(MentorGoal::class, 'mentor_id');
    }

    public function selfAssessments(): HasMany
    {
        return $this->hasMany(MentorSelfAssessment::class, 'mentor_id');
    }

    public function incentives(): HasMany
    {
        return $this->hasMany(MentorIncentive::class, 'mentor_id');
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

    /**
     * Cek apakah mentor memiliki jadwal bentrok privat 1-on-1 pada hari dan jam tertentu.
     */
    public function hasScheduleConflict(array $days, ?string $time, ?int $excludeStudentId = null): bool
    {
        if (empty($days) || ! $time) {
            return false;
        }

        $reqSlot = MentorAvailability::getSlotNumberFromTime($time);

        $daysToCheck = [];
        foreach ($days as $d) {
            $key = MentorMatchingService::normalizeDay((string) $d);
            $label = MentorAvailability::DAYS[$key] ?? $d;
            $daysToCheck[$key] = array_unique([$key, strtolower($label), (string) $d]);
        }

        foreach ($daysToCheck as $key => $variants) {
            $conflict = $this->students
                ->where('pivot.is_active', true)
                ->contains(function ($st) use ($variants, $reqSlot, $excludeStudentId) {
                    if ($excludeStudentId && $st->id === $excludeStudentId) {
                        return false;
                    }

                    $assignedDay = MentorMatchingService::normalizeDay((string) ($st->pivot->day_assigned ?? ''));
                    $assignedSlot = $st->pivot->slot_number;
                    if ($assignedSlot === null && ! empty($st->pivot->time_assigned)) {
                        $assignedSlot = MentorAvailability::getSlotNumberFromTime($st->pivot->time_assigned);
                    }

                    return in_array($assignedDay, $variants, true) && (int) $assignedSlot === (int) $reqSlot;
                });

            if ($conflict) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah mentor benar-benar dapat mengambil jadwal santri ini
     * (buka hari, buka slot, tidak libur, kuota ada, dan tidak bentrok privat 1-on-1).
     */
    public function isAvailableForSchedule(array $days, ?string $time, ?int $excludeStudentId = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (empty($days)) {
            return true;
        }

        $reqSlot = $time ? MentorAvailability::getSlotNumberFromTime($time) : null;

        foreach ($days as $day) {
            $dayKey = MentorMatchingService::normalizeDay((string) $day);
            $dayLabel = MentorAvailability::DAYS[$dayKey] ?? $day;

            $avail = $this->availabilities->first(function ($av) use ($dayKey, $dayLabel) {
                return in_array($av->day, [$dayKey, $dayLabel])
                    && $av->is_available
                    && ! $av->is_holiday;
            });

            if (! $avail) {
                return false;
            }

            if ($reqSlot !== null && ! $avail->hasSlot($reqSlot)) {
                return false;
            }

            $maxStudents = $avail->max_students ?? $this->default_max_students_per_day ?? 5;
            $currentAssigned = $this->students
                ->where('pivot.is_active', true)
                ->filter(function ($st) use ($dayKey, $dayLabel) {
                    $assignedDay = MentorMatchingService::normalizeDay((string) ($st->pivot->day_assigned ?? ''));

                    return in_array($assignedDay, [$dayKey, $dayLabel]);
                })->count();

            if ($currentAssigned >= $maxStudents) {
                return false;
            }
        }

        if ($time && $this->hasScheduleConflict($days, $time, $excludeStudentId)) {
            return false;
        }

        return true;
    }

    public function getBankAccountHolderAttribute(): ?string
    {
        return $this->bank_account_name;
    }

    public function setBankAccountHolderAttribute(?string $value): void
    {
        $this->attributes['bank_account_name'] = $value;
    }

    public function getBirthDateAttribute($value)
    {
        return $value ?? $this->application?->birth_date;
    }

    public function getCityAttribute($value): ?string
    {
        return $value ?? $this->application?->city;
    }

    public function getEducationAttribute($value): ?string
    {
        return $value ?? $this->application?->education;
    }

    public function getInstitutionAttribute($value): ?string
    {
        return $value ?? $this->application?->institution;
    }

    public function getExperienceYearsAttribute($value): int
    {
        return $value !== null ? (int) $value : (int) ($this->application?->experience_years ?? 0);
    }

    public function getHifzTotalJuzAttribute($value): int
    {
        return $value !== null ? (int) $value : (int) ($this->application?->hifz_total_juz ?? 0);
    }

    public function getAddressAttribute($value): ?string
    {
        return $value ?? $this->application?->address;
    }

    public function getSanadChainAttribute($value): ?string
    {
        return $value ?? $this->application?->sanad_chain;
    }

    public function getCvDocument()
    {
        return $this->application?->documents?->firstWhere('document_type', 'cv');
    }

    public function getCertificateDocument()
    {
        return $this->application?->documents?->firstWhere('document_type', 'certificate');
    }

    public function interventionTickets(): HasMany
    {
        return $this->hasMany(MentorInterventionTicket::class);
    }

    public function ahpSnapshots(): HasMany
    {
        return $this->hasMany(AhpEvaluationSnapshot::class);
    }
}
