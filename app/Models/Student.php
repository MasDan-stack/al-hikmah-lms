<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'full_name',
        'age',
        'gender',
        'location',
        'notes',
        'total_points',
        'current_streak',
        'longest_streak',
        'last_setoran_date',
        'privacy_leaderboard',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'age' => 'integer',
        'total_points' => 'integer',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'last_setoran_date' => 'date',
        'privacy_leaderboard' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(Mentor::class, 'mentor_student')
            ->withPivot(['day_assigned', 'time_assigned', 'is_active'])
            ->withTimestamps();
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'student_program')
            ->withPivot('status', 'enrolled_at')
            ->withTimestamps();
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function hifzTargets()
    {
        return $this->hasMany(HifzTarget::class);
    }

    public function juzProgress()
    {
        return $this->hasMany(JuzProgress::class);
    }

    public function earnedBadges()
    {
        return $this->belongsToMany(Badge::class, 'student_badges')
            ->withPivot(['earned_at', 'trigger_data', 'announced_to_parent'])
            ->withTimestamps();
    }

    public function studentBadges()
    {
        return $this->hasMany(StudentBadge::class);
    }

    public function milestones()
    {
        return $this->hasMany(HifzMilestone::class);
    }

    public function leaderboardSnapshots()
    {
        return $this->hasMany(LeaderboardSnapshot::class);
    }

    public function gamificationPoints()
    {
        return $this->hasMany(GamificationPoint::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function getDisplayName(): string
    {
        return $this->user?->name ?? $this->full_name ?? 'Santri';
    }

    public function getParentNameAttribute(): ?string
    {
        return $this->parent?->user?->name ?? 'Orang Tua';
    }

    public function getParentPhoneAttribute(): ?string
    {
        return $this->parent?->user?->phone ?? $this->parent?->emergency_phone ?? '-';
    }

    /**
     * Cek apakah santri sudah pernah melunasi biaya pendaftaran (1x payment).
     */
    public function hasPaidRegistrationFee(): bool
    {
        return $this->payments()
            ->where('status', 'paid')
            ->where('registration_fee', '>', 0)
            ->exists();
    }

    /**
     * Mendapatkan alamat lengkap santri (dari profil wali atau kolom lokasi santri)
     */
    public function getFullAddress(): string
    {
        if (! empty($this->parent?->address)) {
            return $this->parent->address;
        }

        return $this->location ?? 'Alamat belum dilengkapi';
    }

    /**
     * Mendapatkan nomor WhatsApp aktif wali santri
     */
    public function getParentPhone(): ?string
    {
        $phone = $this->parent?->emergency_phone ?? $this->parent?->user?->phone ?? null;

        return $phone !== '-' ? $phone : null;
    }

    /**
     * Mendapatkan mentor aktif santri (dari pivot mentor_student atau enrollment aktif)
     */
    public function getActiveMentor(): ?Mentor
    {
        // 1. Prioritaskan dari pivot mentor_student yang aktif
        $activeMentor = $this->mentors()->wherePivot('is_active', true)->first();
        if ($activeMentor) {
            return $activeMentor;
        }

        // 2. Fallback dari enrollment berstatus CONFIRMED atau ACTIVE
        $enrollment = $this->enrollments()
            ->whereIn('status', [
                EnrollmentStatus::CONFIRMED->value,
                EnrollmentStatus::ACTIVE->value,
            ])
            ->whereNotNull('mentor_id')
            ->latest()
            ->first();

        return $enrollment?->mentor;
    }

    public function getMentorNameAttribute(): string
    {
        $mentor = $this->getActiveMentor();

        return $mentor ? $mentor->getDisplayName() : 'Belum ditentukan';
    }
}
