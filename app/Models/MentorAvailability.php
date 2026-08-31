<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'day',
        'start_time',
        'end_time',
        'max_students',
        'is_available',
        'is_holiday',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'is_holiday' => 'boolean',
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

    public const DAYS_ORDER = [
        'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function isAvailable(): bool
    {
        return $this->is_available && ! $this->is_holiday;
    }

    public function getDayLabelAttribute(): string
    {
        return self::DAYS[$this->day] ?? $this->day;
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('is_holiday', false);
    }

    public function scopeOnDay($query, string $day)
    {
        return $query->where('day', $day);
    }
}
