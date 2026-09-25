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
        'slot_numbers',
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
            'slot_numbers' => 'array',
            'is_available' => 'boolean',
            'is_holiday' => 'boolean',
            'max_students' => 'integer',
        ];
    }

    public const DAYS = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Ahad',
    ];

    public const DAYS_ORDER = [
        'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
    ];

    public const INDONESIAN_TO_ENGLISH = [
        'senin' => 'monday',
        'selasa' => 'tuesday',
        'rabu' => 'wednesday',
        'kamis' => 'thursday',
        'jumat' => 'friday',
        'sabtu' => 'saturday',
        'minggu' => 'sunday',
        'ahad' => 'sunday',
    ];

    public const SLOT_MAP = [
        0 => ['slot' => 0, 'time' => '05:00', 'label' => '05:00', 'desc' => 'Sebelum / Ba\'da Subuh', 'badge' => '0️⃣ 05:00'],
        1 => ['slot' => 1, 'time' => '08:00', 'label' => '08:00', 'desc' => 'Pagi (Dhuha / Pra-Sekolah)', 'badge' => '1️⃣ 08:00'],
        2 => ['slot' => 2, 'time' => '10:00', 'label' => '10:00', 'desc' => 'Menjelang Dzuhur', 'badge' => '2️⃣ 10:00'],
        3 => ['slot' => 3, 'time' => '13:00', 'label' => '13:00', 'desc' => 'Ba\'da Dzuhur', 'badge' => '3️⃣ 13:00'],
        4 => ['slot' => 4, 'time' => '16:00', 'label' => '16:00', 'desc' => 'Ba\'da Ashar (Sore)', 'badge' => '4️⃣ 16:00'],
        5 => ['slot' => 5, 'time' => '18:30', 'label' => '18:30', 'desc' => 'Ba\'da Maghrib', 'badge' => '5️⃣ 18:30'],
        6 => ['slot' => 6, 'time' => '20:00', 'label' => '20:00', 'desc' => 'Ba\'da Isya', 'badge' => '6️⃣ 20:00'],
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function isAvailable(): bool
    {
        return $this->is_available && ! $this->is_holiday && ! empty($this->slot_numbers);
    }

    public function getDayLabelAttribute(): string
    {
        return self::DAYS[$this->day] ?? $this->day;
    }

    public function hasSlot(int $slotNumber): bool
    {
        if (! is_array($this->slot_numbers)) {
            return false;
        }

        return in_array($slotNumber, $this->slot_numbers, true);
    }

    public function getActiveSlotsAttribute(): array
    {
        if (empty($this->slot_numbers) || ! is_array($this->slot_numbers)) {
            return [];
        }

        $result = [];
        foreach ($this->slot_numbers as $slotNum) {
            if (isset(self::SLOT_MAP[$slotNum])) {
                $result[$slotNum] = self::SLOT_MAP[$slotNum];
            }
        }

        ksort($result);

        return $result;
    }

    public static function getSlotNumberFromTime(?string $time): int
    {
        if (! $time) {
            return 4; // Default 16:00
        }

        $clean = substr(trim($time), 0, 5);
        $parts = explode(':', $clean);
        $hour = isset($parts[0]) ? (int) $parts[0] : 16;
        $min = isset($parts[1]) ? (int) $parts[1] : 0;
        $timeInMinutes = $hour * 60 + $min;

        // Rentang jam:
        // 00:00 - 06:30 -> Slot 0 (05:00)
        // 06:31 - 09:00 -> Slot 1 (08:00)
        // 09:01 - 11:30 -> Slot 2 (10:00)
        // 11:31 - 14:30 -> Slot 3 (13:00)
        // 14:31 - 17:30 -> Slot 4 (16:00)
        // 17:31 - 19:30 -> Slot 5 (18:30)
        // 19:31 - 23:59 -> Slot 6 (20:00)
        if ($timeInMinutes <= 390) {
            return 0; // 00:00 - 06:30
        } elseif ($timeInMinutes <= 540) {
            return 1; // 06:31 - 09:00
        } elseif ($timeInMinutes <= 690) {
            return 2; // 09:01 - 11:30
        } elseif ($timeInMinutes <= 870) {
            return 3; // 11:31 - 14:30
        } elseif ($timeInMinutes <= 1050) {
            return 4; // 14:31 - 17:30
        } elseif ($timeInMinutes <= 1170) {
            return 5; // 17:31 - 19:30
        } else {
            return 6; // 19:31 - 23:59
        }
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
