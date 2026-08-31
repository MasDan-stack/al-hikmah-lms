<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\EnrollmentStatus;
use App\Enums\Role as RoleEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Properti Memoization (Request-Level Cache)
    private ?bool $cachedHasActivePaidProgram = null;

    private ?bool $cachedHasPendingInvoiceOrEnrollment = null;

    private ?bool $cachedHasChildren = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function mentor(): HasOne
    {
        return $this->hasOne(Mentor::class);
    }

    public function hasRole(string|RoleEnum $role): bool
    {
        $roleName = $role instanceof RoleEnum ? $role->value : $role;

        return $this->role?->name === $roleName;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleEnum::ADMIN);
    }

    public function isMentor(): bool
    {
        return $this->hasRole(RoleEnum::MENTOR);
    }

    public function isParent(): bool
    {
        return $this->hasRole(RoleEnum::PARENT);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(RoleEnum::STUDENT);
    }

    /**
     * Cek apakah Orang Tua sudah memiliki program belajar yang AKTIF / LUNAS.
     * (Membuka fitur penuh: Anak & Progres, Jadwal, Absensi, Chat Mentor).
     */
    public function hasActivePaidProgram(): bool
    {
        if (! $this->isParent()) {
            return true;
        }

        if ($this->cachedHasActivePaidProgram !== null) {
            return $this->cachedHasActivePaidProgram;
        }

        $parent = $this->parentProfile;
        if (! $parent) {
            return $this->cachedHasActivePaidProgram = false;
        }

        $childIds = $parent->students()->pluck('id')->toArray();
        if (empty($childIds)) {
            return $this->cachedHasActivePaidProgram = false;
        }

        // 1. Cek pembayaran berstatus 'paid'
        $hasPaid = Payment::whereIn('student_id', $childIds)
            ->where('status', 'paid')
            ->exists();

        if ($hasPaid) {
            return $this->cachedHasActivePaidProgram = true;
        }

        // 2. Cek enrollment berstatus 'active'
        $hasActive = Enrollment::whereIn('student_id', $childIds)
            ->where('status', EnrollmentStatus::ACTIVE->value)
            ->exists();

        return $this->cachedHasActivePaidProgram = $hasActive;
    }

    /**
     * Cek apakah Orang Tua sedang dalam proses pendaftaran / memiliki tagihan yang harus dibayar.
     * (Membuka menu State 2: Tagihan & SPP, Pendaftaran & Jadwal, serta menampilkan Mini Progress Bar).
     */
    public function hasPendingInvoiceOrEnrollment(): bool
    {
        if (! $this->isParent()) {
            return true;
        }

        if ($this->cachedHasPendingInvoiceOrEnrollment !== null) {
            return $this->cachedHasPendingInvoiceOrEnrollment;
        }

        $parent = $this->parentProfile;
        if (! $parent) {
            return $this->cachedHasPendingInvoiceOrEnrollment = false;
        }

        $childIds = $parent->students()->pluck('id')->toArray();
        if (empty($childIds)) {
            return $this->cachedHasPendingInvoiceOrEnrollment = false;
        }

        $hasPendingPayment = Payment::whereIn('student_id', $childIds)->where('status', 'pending')->exists();
        $hasEnrollment = Enrollment::whereIn('student_id', $childIds)->exists();

        return $this->cachedHasPendingInvoiceOrEnrollment = ($hasPendingPayment || $hasEnrollment);
    }

    /**
     * Cek apakah Orang Tua sudah memiliki data anak (santri) yang terdaftar.
     */
    public function hasChildren(): bool
    {
        if (! $this->isParent()) {
            return false;
        }

        if ($this->cachedHasChildren !== null) {
            return $this->cachedHasChildren;
        }

        $parent = $this->parentProfile;
        if (! $parent) {
            return $this->cachedHasChildren = false;
        }

        return $this->cachedHasChildren = $parent->students()->exists();
    }

    /**
     * Mendapatkan pendaftaran terkini milik orang tua
     */
    public function getLatestEnrollment(): ?Enrollment
    {
        $parent = $this->parentProfile;
        if (! $parent) {
            return null;
        }

        $childIds = $parent->students()->pluck('id')->toArray();
        if (empty($childIds)) {
            return null;
        }

        return Enrollment::whereIn('student_id', $childIds)
            ->with(['program', 'student', 'mentor'])
            ->latest('id')
            ->first();
    }

    public function passwordResetLogs()
    {
        return $this->hasMany(PasswordResetLog::class, 'user_id');
    }

    public function passwordResetsMade()
    {
        return $this->hasMany(PasswordResetLog::class, 'changed_by');
    }
}
