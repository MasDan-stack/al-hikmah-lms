<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'program_id',
        'program_price',
        'mentor_id',
        'requested_days',
        'requested_time',
        'parent_notes',
        'offered_days',
        'offered_time',
        'admin_notes',
        'status',
        'confirmed_at',
        'paid_at',
        'start_date',
    ];

    protected function casts(): array
    {
        return [
            'program_price' => 'float',
            'requested_days' => 'array',
            'offered_days' => 'array',
            'status' => EnrollmentStatus::class,
            'confirmed_at' => 'datetime',
            'paid_at' => 'datetime',
            'start_date' => 'date',
        ];
    }

    public const DAYS = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Minggu',
    ];

    // Relasi Domain
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Helper Format Tampilan
    public function getRequestedDaysLabelAttribute(): string
    {
        if (empty($this->requested_days)) {
            return '-';
        }
        $labels = array_map(fn ($day) => self::DAYS[$day] ?? ucfirst($day), $this->requested_days);

        return implode(', ', $labels);
    }

    public function getOfferedDaysLabelAttribute(): string
    {
        if (empty($this->offered_days)) {
            return '-';
        }
        $labels = array_map(fn ($day) => self::DAYS[$day] ?? ucfirst($day), $this->offered_days);

        return implode(', ', $labels);
    }

    public function getRequestedTimeLabelAttribute(): string
    {
        return $this->requested_time ? date('H:i', strtotime($this->requested_time)).' WIB' : 'Fleksibel';
    }

    public function getOfferedTimeLabelAttribute(): string
    {
        return $this->offered_time ? date('H:i', strtotime($this->offered_time)).' WIB' : 'Fleksibel';
    }

    public function getFormattedPriceAttribute(): string
    {
        $amount = $this->program_price ?? $this->program?->price ?? 0;

        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    // State Helper Checks
    public function isWaitingAdmin(): bool
    {
        return $this->status === EnrollmentStatus::WAITING_ADMIN;
    }

    public function isWaitingParent(): bool
    {
        return $this->status === EnrollmentStatus::WAITING_PARENT;
    }

    public function isConfirmed(): bool
    {
        return $this->status === EnrollmentStatus::CONFIRMED;
    }

    public function isActive(): bool
    {
        return $this->status === EnrollmentStatus::ACTIVE;
    }
}
