<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_name',
        'child_name',
        'whatsapp',
        'child_age',
        'gender',
        'program_id',
        'trial_focus',
        'preferred_date',
        'preferred_time_slot',
        'learning_method',
        'city',
        'notes',
        'status',
        'assigned_mentor_id',
        'scheduled_at',
        'assessment_result',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'scheduled_at' => 'datetime',
        ];
    }

    public const FOCUS_OPTIONS = [
        'iqra_placement' => 'Penempatan Iqra & Huruf Hijaiyah Dasar',
        'tahsin_tajwid' => 'Tes Tahsin & Kaidah Makharijul Huruf',
        'tahfidz_hafalan' => 'Evaluasi Hafalan & Kelancaran Murajaah',
        'bahasa_arab' => 'Dasar Bahasa Arab & Adab Harian',
    ];

    public const TIME_SLOTS = [
        'pagi' => 'Pagi (08:30 - 11:30 WIB)',
        'siang' => 'Siang (13:30 - 15:30 WIB)',
        'sore' => 'Sore (16:00 - 17:30 WIB)',
        'malam' => 'Malam (19:30 - 21:00 WIB)',
    ];

    public const STATUS_LABELS = [
        'pending' => 'Menunggu Konfirmasi',
        'contacted' => 'Sudah Dihubungi',
        'scheduled' => 'Jadwal Ditetapkan',
        'completed' => 'Sesi Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function assignedMentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'assigned_mentor_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    public function getFocusLabelAttribute(): string
    {
        return self::FOCUS_OPTIONS[$this->trial_focus] ?? ucfirst(str_replace('_', ' ', $this->trial_focus));
    }

    public function getTimeSlotLabelAttribute(): string
    {
        return self::TIME_SLOTS[$this->preferred_time_slot] ?? ucfirst($this->preferred_time_slot);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning-subtle text-warning border-warning',
            'contacted' => 'bg-info-subtle text-info border-info',
            'scheduled' => 'bg-primary-subtle text-primary border-primary',
            'completed' => 'bg-success-subtle text-success border-success',
            'cancelled' => 'bg-danger-subtle text-danger border-danger',
            default => 'bg-secondary-subtle text-secondary border-secondary',
        };
    }
}
