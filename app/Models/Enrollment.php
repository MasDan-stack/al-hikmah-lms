<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'program_id',
        'program_price',
        'learning_method',
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
        'shadow_mentor_id',
        'matching_score',
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
            'matching_score' => 'float',
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

    public function shadowMentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class, 'shadow_mentor_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function matchingLogs(): HasMany
    {
        return $this->hasMany(MatchingLog::class);
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

    public function getEffectiveDaysLabelAttribute(): string
    {
        $days = ! empty($this->offered_days) ? $this->offered_days : ($this->requested_days ?? []);
        if (empty($days)) {
            return '-';
        }
        $labels = array_map(fn ($day) => self::DAYS[$day] ?? ucfirst($day), $days);

        return implode(', ', $labels);
    }

    public function getEffectiveTimeLabelAttribute(): string
    {
        $time = $this->offered_time ?? $this->requested_time;

        return $time ? date('H:i', strtotime($time)).' WIB' : 'Fleksibel';
    }

    public function getStartDateLabelAttribute(): string
    {
        if (! $this->start_date) {
            return 'Menunggu penetapan';
        }

        $firstSession = $this->calculateFirstSessionDate(Carbon::parse($this->start_date));

        return $firstSession->translatedFormat('l, d F Y');
    }

    /**
     * Hitung tanggal sesi belajar pertama yang valid sesuai jadwal hari yang disepakati/diminta.
     */
    public function calculateFirstSessionDate(?Carbon $fromDate = null): Carbon
    {
        $baseDate = $fromDate ? $fromDate->copy() : ($this->start_date ? Carbon::parse($this->start_date) : Carbon::today()->addDays(3));

        $days = ! empty($this->offered_days) ? $this->offered_days : $this->requested_days;
        if (empty($days) || ! is_array($days)) {
            return $baseDate;
        }

        $dayMap = [
            'sunday' => Carbon::SUNDAY,
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
        ];

        $candidateDates = [];
        foreach ($days as $dayName) {
            $cleanDay = strtolower(trim((string) $dayName));
            if (! isset($dayMap[$cleanDay])) {
                continue;
            }

            $carbonDay = $dayMap[$cleanDay];
            $current = $baseDate->copy();
            if ($current->dayOfWeek !== $carbonDay) {
                $current->next($carbonDay);
            }
            $candidateDates[] = $current;
        }

        if (empty($candidateDates)) {
            return $baseDate;
        }

        usort($candidateDates, fn ($a, $b) => $a->timestamp <=> $b->timestamp);

        return $candidateDates[0];
    }

    /**
     * Accessor badge warna Bootstrap berdasarkan status pendaftaran.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->status instanceof EnrollmentStatus) {
            return $this->status->badgeClass();
        }

        return match ($this->status) {
            EnrollmentStatus::WAITING_ADMIN->value => 'warning',
            EnrollmentStatus::WAITING_PARENT->value => 'info',
            EnrollmentStatus::CONFIRMED->value => 'primary',
            EnrollmentStatus::ACTIVE->value => 'success',
            EnrollmentStatus::CANCELLED->value => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Accessor label bahasa Indonesia berdasarkan status pendaftaran.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status instanceof EnrollmentStatus) {
            return $this->status->label();
        }

        return match ($this->status) {
            EnrollmentStatus::WAITING_ADMIN->value => 'Menunggu Review Lembaga',
            EnrollmentStatus::WAITING_PARENT->value => 'Menunggu Respon Anda',
            EnrollmentStatus::CONFIRMED->value => 'Jadwal Disepakati (Siap Bayar)',
            EnrollmentStatus::ACTIVE->value => 'Kelas Aktif',
            EnrollmentStatus::CANCELLED->value => 'Dibatalkan / Expired',
            default => (string) $this->status,
        };
    }

    /**
     * Accessor persentase progress untuk mini progress bar di sidebar & dashboard.
     */
    public function getProgressPercentAttribute(): int
    {
        $statusValue = $this->status instanceof EnrollmentStatus ? $this->status->value : $this->status;

        return match ($statusValue) {
            EnrollmentStatus::WAITING_ADMIN->value => 35,
            EnrollmentStatus::WAITING_PARENT->value => 65,
            EnrollmentStatus::CONFIRMED->value => 85,
            EnrollmentStatus::ACTIVE->value => 100,
            default => 20,
        };
    }

    /**
     * Accessor ringkasan langkah pendaftaran saat ini.
     */
    public function getProgressStepLabelAttribute(): string
    {
        $statusValue = $this->status instanceof EnrollmentStatus ? $this->status->value : $this->status;

        return match ($statusValue) {
            EnrollmentStatus::WAITING_ADMIN->value => '1. Review Jadwal Lembaga',
            EnrollmentStatus::WAITING_PARENT->value => '2. Konfirmasi Tawaran Jadwal',
            EnrollmentStatus::CONFIRMED->value => '3. Pembayaran Pendaftaran',
            EnrollmentStatus::ACTIVE->value => 'Kelas Bimbingan Aktif',
            default => 'Proses Pendaftaran',
        };
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

    /**
     * Helper terpusat: Sinkronisasi ke tabel pivot mentor_student per hari belajar
     */
    public function syncToMentorStudent(bool $isActive = true): void
    {
        if (! $this->mentor_id || ! $this->student_id) {
            return;
        }

        $days = ! empty($this->offered_days) ? $this->offered_days : ($this->requested_days ?? ['monday']);
        $timeAssigned = $this->offered_time ?? $this->requested_time ?? '16:00:00';
        $slotNumber = MentorAvailability::getSlotNumberFromTime($timeAssigned);
        $timeLabel = MentorAvailability::SLOT_MAP[$slotNumber]['time'] ?? substr($timeAssigned, 0, 5);

        if (is_array($days)) {
            foreach ($days as $day) {
                $dayKey = MentorAvailability::INDONESIAN_TO_ENGLISH[strtolower($day)] ?? strtolower($day);

                // 1. Sinkronkan ke tabel pivot mentor_student
                DB::table('mentor_student')->updateOrInsert(
                    [
                        'mentor_id' => $this->mentor_id,
                        'student_id' => $this->student_id,
                        'day_assigned' => $dayKey,
                    ],
                    [
                        'program_id' => $this->program_id,
                        'slot_number' => $slotNumber,
                        'time_label' => $timeLabel,
                        'time_assigned' => $timeAssigned,
                        'is_active' => $isActive,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                // 2. OTOMATIS AKTIFKAN & TAMBAHKAN slot ini ke mentor_availabilities
                $avail = MentorAvailability::firstOrCreate(
                    [
                        'mentor_id' => $this->mentor_id,
                        'day' => $dayKey,
                    ],
                    [
                        'slot_numbers' => [$slotNumber],
                        'max_students' => 5,
                        'is_available' => true,
                        'is_holiday' => false,
                    ]
                );

                $currentSlots = $avail->slot_numbers ?? [];
                if (! is_array($currentSlots)) {
                    $currentSlots = [];
                }
                if (! in_array($slotNumber, $currentSlots, true)) {
                    $currentSlots[] = $slotNumber;
                    sort($currentSlots);
                    $avail->update([
                        'slot_numbers' => array_values(array_unique($currentSlots)),
                        'is_available' => true,
                        'is_holiday' => false,
                    ]);
                }
            }
        }
    }

    /**
     * Tandai enrollment sebagai lunas dan aktifkan program serta penugasan mentor (Idempotent & Transactional)
     */
    public function markAsPaidAndActive(): void
    {
        if ($this->status === EnrollmentStatus::ACTIVE) {
            return;
        }

        DB::transaction(function () {
            $this->update([
                'status' => EnrollmentStatus::ACTIVE,
                'paid_at' => now(),
            ]);

            // 1. Hubungkan santri ke program di tabel pivot student_program
            if ($this->student_id && $this->program_id) {
                $student = Student::find($this->student_id);
                $student?->programs()->syncWithoutDetaching([
                    $this->program_id => [
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ],
                ]);
            }

            // 2. Hubungkan santri ke mentor di tabel pivot mentor_student per HARI BELAJAR
            $this->syncToMentorStudent(true);

            // 3. Generate Otomatis Sesi Belajar (learning_sessions) untuk 4 Minggu ke Depan
            $this->generateInitialLearningSessions();

            // 4. Catat log mentor
            if ($this->mentor_id) {
                MentorActivityLog::log(
                    $this->mentor_id,
                    'student_activated',
                    'Santri '.($this->student?->getDisplayName() ?? 'Santri').' resmi aktif pada program '.($this->program?->name ?? 'Program').'.'
                );
            }

            // 5. Kirim notifikasi internal ke Orang Tua
            if ($this->student?->parent?->user_id) {
                Notification::create([
                    'user_id' => $this->student->parent->user_id,
                    'type' => 'enrollment_active',
                    'title' => 'Program '.($this->program?->name ?? 'Program').' Telah Aktif!',
                    'message' => 'Pembayaran berhasil. Bimbingan belajar '.($this->student->getDisplayName()).' bersama Ustadz/Ustadzah '.($this->mentor?->getDisplayName() ?? 'Mentor').' siap dimulai pada '.($this->start_date_label).'.',
                    'is_read' => false,
                ]);
            }

            // 6. Notifikasi WhatsApp Otomatis (Opsional)
            $parentPhone = $this->student?->getParentPhone();
            if ($parentPhone) {
                $waMessage = "Assalamu'alaikum Ayah/Bunda,\n\nAlhamdulillah pembayaran pendaftaran program *{$this->program?->name}* untuk ananda *{$this->student?->getDisplayName()}* telah berhasil kami terima.\n\n"
                    ."👳‍♂️ *Guru Pembimbing:* {$this->mentor?->getDisplayName()}\n"
                    ."📅 *Jadwal Bimbingan:* {$this->effective_days_label} ({$this->effective_time_label})\n"
                    ."🚀 *Mulai Bimbingan:* {$this->start_date_label}\n\n"
                    .'Silakan pantau jadwal dan materi bimbingan di portal: '.route('parent.schedules.index')."\n\n_Jazakumullahu Khairan - AL-HIKMAH LMS_";

                app(WhatsAppService::class)->sendMessage($parentPhone, $waMessage);
            }
        });
    }

    /**
     * Generate 4 minggu sesi bimbingan awal di tabel learning_sessions
     */
    public function generateInitialLearningSessions(): void
    {
        if (! $this->student_id || ! $this->mentor_id) {
            return;
        }

        $days = ! empty($this->offered_days) ? $this->offered_days : $this->requested_days;
        if (empty($days) || ! is_array($days)) {
            return;
        }

        $timeAssigned = $this->offered_time ?? $this->requested_time ?? '16:00:00';
        $startDate = $this->start_date ? Carbon::parse($this->start_date) : Carbon::today();
        $method = $this->learning_method ?? 'offline';

        $dayMap = [
            'sunday' => Carbon::SUNDAY,
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
        ];

        foreach ($days as $dayName) {
            if (! isset($dayMap[$dayName])) {
                continue;
            }

            $carbonDay = $dayMap[$dayName];
            $currentDate = $startDate->copy();

            if ($currentDate->dayOfWeek !== $carbonDay) {
                $currentDate->next($carbonDay);
            }

            for ($week = 0; $week < 4; $week++) {
                $sessionDate = $currentDate->copy()->addWeeks($week)->toDateString();

                Session::firstOrCreate(
                    [
                        'student_id' => $this->student_id,
                        'mentor_id' => $this->mentor_id,
                        'date' => $sessionDate,
                        'time' => $timeAssigned,
                    ],
                    [
                        'method' => $method,
                        'status' => 'scheduled',
                        'notes' => 'Sesi bimbingan rutin program '.($this->program?->name ?? 'Al-Hikmah'),
                    ]
                );
            }
        }
    }
}
