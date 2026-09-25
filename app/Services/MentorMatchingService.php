<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Jobs\ProcessMentorAllocationJob;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MentorMatchingService
{
    // Bobot Kompatibilitas Sesuai PRD (Total 100%)
    public const WEIGHT_GENDER = 0.20;

    public const WEIGHT_LOCATION = 0.15;

    public const WEIGHT_SLOT = 0.20;

    public const WEIGHT_SPECIALIZATION = 0.15;

    public const WEIGHT_LOAD = 0.15;

    public const WEIGHT_PEDAGOGY = 0.15;

    /**
     * Dapatkan rekomendasi mentor teratas untuk sebuah enrollment (Top N).
     */
    public function getTopRecommendations(Enrollment $enrollment, int $limit = 3): Collection
    {
        $student = $enrollment->student;
        $program = $enrollment->program;

        $requestedDays = is_array($enrollment->requested_days) ? $enrollment->requested_days : [];
        if (empty($requestedDays) && $enrollment->day_preference) {
            $requestedDays = is_array($enrollment->day_preference) ? $enrollment->day_preference : [$enrollment->day_preference];
        }
        if (empty($requestedDays)) {
            $requestedDays = ['Senin'];
        }

        $normalizedRequestedDays = [];
        foreach ($requestedDays as $rd) {
            $key = self::normalizeDay((string) $rd);
            $normalizedRequestedDays[] = $key;
            if (isset(MentorAvailability::DAYS[$key])) {
                $normalizedRequestedDays[] = MentorAvailability::DAYS[$key];
            }
            $normalizedRequestedDays[] = (string) $rd;
        }
        $daysList = array_unique($normalizedRequestedDays);
        $dayKey = self::normalizeDay((string) $requestedDays[0]);

        $method = strtolower((string) ($enrollment->learning_method ?? 'online'));
        $studentLat = $student?->latitude;
        $studentLon = $student?->longitude;

        // 1. Ambil Rata-rata Beban Sistem dari Cache (TTL 300 detik)
        $avgLoad = Cache::remember('mentors_system_avg_load', 300, function () {
            $totalAssigned = DB::table('mentor_student')->where('is_active', true)->count();
            $totalActiveMentors = DB::table('mentors')->where('is_active', true)->count();

            return (float) ($totalAssigned / max(1, $totalActiveMentors));
        });

        // 2. Pre-Filtering Database Query SQL
        $mentorsQuery = Mentor::query()
            ->where('is_active', true)
            ->whereHas('availabilities', function ($q) use ($daysList) {
                $q->whereIn('day', $daysList)->where('is_available', true);
            })
            ->with(['user:id,name,phone', 'availabilities', 'badges', 'students']);

        // 3. Native MySQL Geospatial Query (ST_Distance_Sphere) untuk kelas Offline
        if ($method === 'offline' && $studentLat && $studentLon) {
            $mentorsQuery->selectRaw('*, (ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) / 1000) as distance_km', [
                $studentLon, $studentLat,
            ])->whereRaw('(ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) / 1000) <= 25.0', [
                $studentLon, $studentLat,
            ]);
        }

        $mentors = $mentorsQuery->get();

        // Fallback jika pre-filtering terlalu ketat dan hasil 0 (misal availabilities belum diisi lengkap pada seeding data)
        if ($mentors->isEmpty()) {
            $fallbackQuery = Mentor::query()
                ->where('is_active', true)
                ->with(['user:id,name,phone', 'availabilities', 'badges', 'students']);

            if ($method === 'offline' && $studentLat && $studentLon) {
                $fallbackQuery->selectRaw('*, (ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) / 1000) as distance_km', [
                    $studentLon, $studentLat,
                ]);
            }
            $mentors = $fallbackQuery->get();
        }

        // 4. Hitung Skor Kecocokan Multi-Kriteria
        $scored = $mentors->map(function (Mentor $mentor) use ($student, $program, $dayKey, $method, $avgLoad, $enrollment) {
            $breakdown = $this->calculateBreakdown($mentor, $student, $program, $dayKey, $method, $avgLoad, $enrollment);

            // Check Family Blacklist (Hard Disqualification)
            if ($this->isFamilyBlacklisted($student, $mentor)) {
                $totalScore = 0.0;
                $breakdown['disqualified_reason'] = 'Riwayat mutasi/komplain pada keluarga santri sebelumnya.';
            } elseif ($breakdown['gender'] === 0.0) {
                $totalScore = 0.0;
                $breakdown['disqualified_reason'] = 'Gender mentor tidak sesuai dengan aturan umur/gender santri.';
            } elseif ($breakdown['slot'] === 0.0) {
                $totalScore = 0.0;
                $breakdown['disqualified_reason'] = $breakdown['disqualified_reason'] ?? 'Mentor libur, tidak membuka slot jam yang diminta, atau jadwal bentrok dengan santri privat lain (1-on-1).';
            } elseif (! empty($breakdown['disqualified_reason'])) {
                $totalScore = 0.0;
            } else {
                $totalScore = ($breakdown['gender'] * self::WEIGHT_GENDER) +
                              ($breakdown['location'] * self::WEIGHT_LOCATION) +
                              ($breakdown['slot'] * self::WEIGHT_SLOT) +
                              ($breakdown['specialization'] * self::WEIGHT_SPECIALIZATION) +
                              ($breakdown['load'] * self::WEIGHT_LOAD) +
                              ($breakdown['pedagogy'] * self::WEIGHT_PEDAGOGY) +
                              ($breakdown['gamification_boost'] ?? 0) +
                              ($breakdown['performance_boost'] ?? 0) -
                              ($breakdown['burnout_throttle_penalty'] ?? 0) -
                              ($breakdown['prayer_penalty'] ?? 0);

                $totalScore = min(100.0, max(0.0, $totalScore));
            }

            $activeLoad = $mentor->students->where('pivot.is_active', true)->count();
            $distance = $mentor->distance_km ?? ($method === 'online' ? 0.0 : 8.0);

            return [
                'mentor' => $mentor,
                'score' => round($totalScore, 1),
                'final_score' => round($totalScore, 1), // Alias compatibility
                'breakdown' => $breakdown,
                'distance_km' => round((float) $distance, 1),
                'active_load' => $activeLoad,
                'rating' => (float) ($mentor->rating ?? 5.0),
            ];
        });

        // 5. Multi-Level Tie-Breaker Sorting (Score Desc -> Load Asc -> Distance Asc -> Rating Desc)
        return $scored->filter(fn ($item) => $item['score'] > 0.0)
            ->sort(function ($a, $b) {
                if ($a['score'] !== $b['score']) {
                    return $b['score'] <=> $a['score']; // Level 1: Score Descending
                }
                if ($a['active_load'] !== $b['active_load']) {
                    return $a['active_load'] <=> $b['active_load']; // Level 2: Load Ascending
                }
                if ($a['distance_km'] !== $b['distance_km']) {
                    return $a['distance_km'] <=> $b['distance_km']; // Level 3: Distance Ascending
                }

                return $b['rating'] <=> $a['rating']; // Level 4: Rating Descending
            })->take($limit)->values();
    }

    /**
     * Hitung rincian skor untuk masing-masing kriteria.
     */
    public function calculateBreakdown(
        Mentor $mentor,
        Student $student,
        $program,
        string $day,
        string $method = 'online',
        float $avgLoad = 10.0,
        ?Enrollment $enrollment = null
    ): array {
        $genderScore = $this->calculateGenderScore($mentor, $student, $program);
        $locationScore = $this->calculateLocationScore($mentor, $student, $method);
        $slotScore = $this->calculateSlotScore($mentor, $day, $enrollment);
        $specScore = $this->calculateSpecializationScore($mentor, $program);
        $loadScore = $this->calculateLoadScore($mentor, $avgLoad);
        $pedagogyScore = $this->calculatePedagogyScore($mentor, $student);

        $disqualifiedReason = null;

        // Calendar Conflict Check
        $reqTime = $enrollment ? $enrollment->requested_time : null;
        if ($reqTime && app(CalendarSyncService::class)->hasExternalConflict($mentor, $day, $reqTime)) {
            $slotScore = 0.0;
            $disqualifiedReason = 'Terdapat agenda pribadi di Google Calendar pada jam tersebut.';
        }

        // Load Throttling Check
        if (app(SmartLoadBalancerService::class)->isThrottledFromNewStudents($mentor)) {
            $disqualifiedReason = 'Mentor sedang dalam masa pemulihan beban mengajar.';
        }

        // 1. Boost Lencana Teladan (M01 / M03) atau Rating >= 4.9 (+5%)
        $hasTopBadge = false;
        try {
            $hasTopBadge = $mentor->badges()->whereIn('code', ['M01', 'M03'])->exists();
        } catch (\Throwable $e) {
            $hasTopBadge = false;
        }

        $badgeBoost = ($hasTopBadge || (float) ($mentor->rating ?? 0) >= 4.9) ? 5.0 : 0.0;

        // 2. Performance Boost Berdasarkan Skor Komposit Kinerja Aktual
        $latestSnapshot = $mentor->performanceSnapshots()->latest('period_start')->first();
        $compositeScore = (float) ($latestSnapshot?->composite_score ?? 0.0);
        $perfBoost = 0.0;
        if ($compositeScore >= 90.0) {
            $perfBoost = 10.0; // Top Performer Boost (+10 Poin)
        } elseif ($compositeScore >= 80.0) {
            $perfBoost = 5.0;  // High Performer Boost (+5 Poin)
        }

        // 3. Burnout & Workload Safety Throttle Penalty
        $activeStudentsCount = $mentor->students()->wherePivot('is_active', true)->count();
        $burnoutPenalty = 0.0;
        if ($activeStudentsCount >= 35 || app(SmartLoadBalancerService::class)->calculateBurnoutIndex($mentor) > 60) {
            $burnoutPenalty = 15.0; // Burnout throttle protection
        }

        // 4. Buffer Waktu Sholat Penalty (Contoh jika mepet maghrib/isya)
        $prayerPenalty = 0.0;
        if ($enrollment && $enrollment->requested_time) {
            $time = date('H:i', strtotime($enrollment->requested_time));
            if ($time >= '17:45' && $time <= '18:30') {
                $prayerPenalty = 15.0; // Peringatan waktu Maghrib
            }
        }

        return [
            'gender' => $genderScore,
            'location' => $locationScore,
            'slot' => $slotScore,
            'specialization' => $specScore,
            'load' => $loadScore,
            'pedagogy' => $pedagogyScore,
            'gamification_boost' => $badgeBoost,
            'performance_boost' => $perfBoost,
            'burnout_throttle_penalty' => $burnoutPenalty,
            'prayer_penalty' => $prayerPenalty,
            'disqualified_reason' => $disqualifiedReason,
        ];
    }

    public function calculateGenderScore(Mentor $mentor, Student $student, $program = null): float
    {
        $category = strtolower($program->category ?? $program->name ?? '');
        $mentorName = strtolower($mentor->getDisplayName());

        if (str_contains($mentorName, 'ustazah') || str_contains($mentorName, 'ustadzah')) {
            $mentorGender = 'P';
        } elseif (str_contains($mentorName, 'ustadz') || str_contains($mentorName, 'ustaz')) {
            $mentorGender = 'L';
        } else {
            $mentorGender = strtoupper($mentor->gender ?? $mentor->user?->gender ?? 'L');
        }

        $studentGender = strtoupper($student->gender ?? 'L');
        $studentAge = (int) ($student->age ?? 0);

        // 1. Program khusus Muslimah: Wajib mentor perempuan (Ustazah)
        if (str_contains($category, 'muslimah')) {
            return $mentorGender === 'P' ? 100.0 : 0.0;
        }

        // 2. Santri Perempuan: Wajib mentor perempuan (Ustazah)
        if ($studentGender === 'P') {
            return $mentorGender === 'P' ? 100.0 : 0.0;
        }

        // 3. Santri Laki-laki: Aturan Berbasis Umur 10 Tahun
        if ($studentGender === 'L') {
            if ($studentAge < 10) {
                // Laki-laki di bawah 10 tahun: WAJIB mentor perempuan (Ustazah)
                return $mentorGender === 'P' ? 100.0 : 0.0;
            } else {
                // Laki-laki 10 tahun ke atas (>= 10 th): WAJIB mentor laki-laki (Ustadz)
                return $mentorGender === 'L' ? 100.0 : 0.0;
            }
        }

        return 0.0;
    }

    public function calculateLocationScore(Mentor $mentor, Student $student, string $method = 'online'): float
    {
        if (strtolower($method) === 'online') {
            return 100.0;
        }

        // Jika data koordinat kosong, fallback ke estimasi jarak dalam kota (8 km = 80%)
        if (! $student->latitude || ! $student->longitude || ! $mentor->latitude || ! $mentor->longitude) {
            return 80.0;
        }

        $distance = $mentor->distance_km ?? $this->calculateHaversineDistance(
            $student->latitude, $student->longitude,
            $mentor->latitude, $mentor->longitude
        );

        if ($distance <= 5.0) {
            return 100.0;
        }
        if ($distance <= 10.0) {
            return 80.0;
        }
        if ($distance <= 20.0) {
            return 60.0;
        }

        return 40.0;
    }

    public function calculateSlotScore(Mentor $mentor, string $day, Enrollment|string|null $enrollmentOrTime = null): float
    {
        $enrollment = $enrollmentOrTime instanceof Enrollment ? $enrollmentOrTime : null;
        $requestedTime = $enrollment ? $enrollment->requested_time : (is_string($enrollmentOrTime) ? $enrollmentOrTime : null);

        // Kumpulkan seluruh hari yang perlu dicek (bisa multi-hari jika dari Enrollment)
        $daysToCheck = [];
        if ($enrollment && ! empty($enrollment->requested_days)) {
            $daysToCheck = (array) $enrollment->requested_days;
        } elseif ($enrollment && $enrollment->day_preference) {
            $daysToCheck = is_array($enrollment->day_preference) ? $enrollment->day_preference : [$enrollment->day_preference];
        } else {
            $daysToCheck = [$day];
        }

        $scores = [];

        foreach ($daysToCheck as $d) {
            $dayKey = self::normalizeDay((string) $d);
            $dayLabel = MentorAvailability::DAYS[$dayKey] ?? $d;

            $availability = $mentor->availabilities->first(function ($av) use ($dayKey, $dayLabel, $d) {
                return in_array($av->day, [$dayKey, $dayLabel, $d])
                    && $av->is_available;
            });

            if (! $availability || $availability->is_holiday) {
                return 0.0;
            }

            // Cek bentrok slot 1-on-1 jika terdapat jam spesifik yang diminta santri
            if ($requestedTime) {
                $reqSlot = MentorAvailability::getSlotNumberFromTime($requestedTime);

                // Cek apakah mentor membuka slot jam tersebut
                if (! $availability->hasSlot($reqSlot)) {
                    return 0.0; // Mentor tidak membuka slot jam yang diminta
                }

                // Sistem 1-on-1: Cek apakah mentor sudah memiliki santri bimbingan di slot & hari tersebut
                $isSlotBooked = $mentor->students->where('pivot.is_active', true)->contains(function ($st) use ($dayKey, $dayLabel, $d, $reqSlot, $enrollment) {
                    if ($enrollment && $st->id === $enrollment->student_id) {
                        return false;
                    }

                    $assignedDay = self::normalizeDay((string) ($st->pivot->day_assigned ?? ''));
                    $assignedSlot = $st->pivot->slot_number;
                    if ($assignedSlot === null && ! empty($st->pivot->time_assigned)) {
                        $assignedSlot = MentorAvailability::getSlotNumberFromTime($st->pivot->time_assigned);
                    }

                    return in_array($assignedDay, [$dayKey, strtolower($dayLabel), strtolower((string) $d)], true)
                        && (int) $assignedSlot === (int) $reqSlot;
                });

                if ($isSlotBooked) {
                    return 0.0; // Bentrok jadwal privat 1-on-1: Slot sudah terisi santri lain
                }
            }

            $maxSlots = $availability->max_students ?? $mentor->max_students_per_day ?? $mentor->default_max_students_per_day ?? 5;

            $currentAssigned = $mentor->students->where('pivot.is_active', true)->filter(function ($st) use ($dayKey, $dayLabel, $d) {
                $assignedDay = self::normalizeDay((string) ($st->pivot->day_assigned ?? ''));

                return in_array($assignedDay, [$dayKey, strtolower($dayLabel), strtolower((string) $d)], true);
            })->count();

            // Jika tidak ada pembagian spesifik per hari pada pivot, hitung proporsi beban aktif
            if ($currentAssigned === 0) {
                $currentAssigned = (int) round($mentor->students->where('pivot.is_active', true)->count() / 5);
            }

            $remaining = max(0, $maxSlots - $currentAssigned);
            $scores[] = round(($remaining / max(1, $maxSlots)) * 100.0, 1);
        }

        return empty($scores) ? 0.0 : min($scores);
    }

    public function calculateSpecializationScore(Mentor $mentor, $program): float
    {
        $specializations = (array) ($mentor->specializations ?? []);
        if (empty($specializations) && $mentor->specialization) {
            $specializations = array_map('trim', explode(',', strtolower($mentor->specialization)));
        } else {
            $specializations = array_map('strtolower', $specializations);
        }

        $category = strtolower($program->category ?? $program->name ?? '');

        // Cek Mentor Blocked Programs
        $blockedPrograms = (array) ($mentor->blocked_programs ?? []);
        if (in_array($category, array_map('strtolower', $blockedPrograms))) {
            return 0.0;
        }

        if (str_contains($category, 'tahsin') && (in_array('tahsin', $specializations) || in_array('qur\'an', $specializations))) {
            return 100.0;
        }
        if (str_contains($category, 'tahfidz') && (in_array('tahfidz', $specializations) || in_array('hifz', $specializations))) {
            return 100.0;
        }
        if ((str_contains($category, 'arab') || str_contains($category, 'nahwu')) && in_array('bahasa_arab', $specializations)) {
            return 100.0;
        }

        return 60.0; // Skor lintas bidang / umum
    }

    public function calculateLoadScore(Mentor $mentor, float $avgLoad = 10.0): float
    {
        $currentLoad = $mentor->students->where('pivot.is_active', true)->count();
        if ($currentLoad === 0 && isset($mentor->students_count)) {
            $currentLoad = $mentor->students_count;
        }

        if ($currentLoad < $avgLoad) {
            return 100.0;
        }
        if ($currentLoad == $avgLoad) {
            return 80.0;
        }

        $excess = ($currentLoad - $avgLoad) / max(1.0, $avgLoad);

        return max(50.0, round(100.0 - ($excess * 50.0), 1));
    }

    public function calculatePedagogyScore(Mentor $mentor, Student $student): float
    {
        $mProfile = $mentor->pedagogicalProfile;
        $sProfile = $student->learningStyle;

        if (! $mProfile || ! $sProfile) {
            return 80.0; // Default if not profiled
        }

        $v_s = $sProfile->visual_score;
        $a_s = $sProfile->auditory_score;
        $k_s = $sProfile->kinesthetic_score;
        $p_s = $sProfile->patience_need;

        $v_m = $mProfile->visual_capability;
        $a_m = $mProfile->auditory_capability;
        $k_m = $mProfile->kinesthetic_capability;
        $p_m = $mProfile->patience_rating;

        $dotProduct = ($v_s * $v_m) + ($a_s * $a_m) + ($k_s * $k_m) + ($p_s * $p_m);
        $magS = sqrt(pow($v_s, 2) + pow($a_s, 2) + pow($k_s, 2) + pow($p_s, 2));
        $magM = sqrt(pow($v_m, 2) + pow($a_m, 2) + pow($k_m, 2) + pow($p_m, 2));

        if ($magS == 0 || $magM == 0) {
            return 80.0;
        }

        $cosineSimilarity = $dotProduct / ($magS * $magM);
        $score = $cosineSimilarity * 100;

        $historyScore = (float) $mProfile->historical_retention_rate;

        return round(($score * 0.7) + ($historyScore * 0.3), 1);
    }

    public function isFamilyBlacklisted(?Student $student, Mentor $mentor): bool
    {
        if (! $student || ! $student->parent_id) {
            return false;
        }

        try {
            return DB::table('student_mutation_logs')
                ->where('parent_id', $student->parent_id)
                ->where('previous_mentor_id', $mentor->id)
                ->where('reason_category', 'dissatisfaction')
                ->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Auto-Assign jika kandidat terbaik memiliki skor >= 95%
     */
    public function autoAssignIfEligible(Enrollment $enrollment): bool
    {
        $topRec = $this->getTopRecommendations($enrollment, 1)->first();
        if ($topRec && $topRec['score'] >= 95.0) {
            $enrollment->update([
                'mentor_id' => $topRec['mentor']->id,
                'matching_score' => $topRec['score'],
                'status' => EnrollmentStatus::CONFIRMED,
                'confirmed_at' => now(),
            ]);

            ProcessMentorAllocationJob::dispatch(
                $enrollment->id,
                $topRec['mentor']->id,
                $topRec['score'],
                'auto_high_confidence',
                $topRec['breakdown']
            );

            return true;
        }

        return false;
    }

    /**
     * Explainable AI ("Why Not...?" Tooltip Inspection)
     */
    public function explainMentorExclusion(Enrollment $enrollment, int $mentorId): array
    {
        $mentor = Mentor::find($mentorId);
        if (! $mentor) {
            return ['Mentor tidak ditemukan dalam sistem.'];
        }

        $reasons = [];
        if (! $mentor->is_active) {
            $reasons[] = 'Status mentor sedang tidak aktif / cuti.';
        }

        $requestedDays = is_array($enrollment->requested_days) ? $enrollment->requested_days : [];
        if (empty($requestedDays) && $enrollment->day_preference) {
            $requestedDays = is_array($enrollment->day_preference) ? $enrollment->day_preference : [$enrollment->day_preference];
        }
        $dayName = ! empty($requestedDays) ? ($enrollment->requested_days_label ?? $requestedDays[0]) : 'Senin';
        $dayKey = self::normalizeDay((string) ($requestedDays[0] ?? 'Senin'));

        if ($mentor->hasScheduleConflict($requestedDays, $enrollment->requested_time, $enrollment->student_id)) {
            $reasons[] = "Jadwal mentor bentrok dengan santri privat (1-on-1) lain pada hari & jam yang diminta ({$dayName} jam {$enrollment->requested_time_label}).";
        }

        if (! $mentor->hasQuotaOnDay($dayKey)) {
            $reasons[] = "Kuota hari {$dayName} telah penuh atau mentor libur.";
        }

        if (app(SmartLoadBalancerService::class)->isThrottledFromNewStudents($mentor)) {
            $reasons[] = 'Mentor sedang dalam masa pemulihan beban mengajar (Load Throttling).';
        }

        if ($enrollment->requested_time && app(CalendarSyncService::class)->hasExternalConflict($mentor, $dayKey, $enrollment->requested_time)) {
            $reasons[] = 'Terdapat agenda pribadi di kalender eksternal pada jam tersebut.';
        }

        if (($mentor->distance_km ?? 0) > 20 && strtolower($enrollment->learning_method ?? '') === 'offline') {
            $reasons[] = "Jarak lokasi ({$mentor->distance_km} km) melebihi batas ideal Home Visit.";
        }

        $student = $enrollment->student;
        if ($student) {
            $genderScore = $this->calculateGenderScore($mentor, $student, $enrollment->program);
            if ($genderScore === 0.0) {
                $studentGender = strtoupper($student->gender ?? 'L');
                $studentAge = (int) ($student->age ?? 0);
                if ($studentGender === 'L' && $studentAge < 10) {
                    $reasons[] = 'Santri laki-laki < 10 tahun wajib dibimbing oleh Ustazah (perempuan).';
                } elseif ($studentGender === 'L' && $studentAge >= 10) {
                    $reasons[] = 'Santri laki-laki >= 10 tahun wajib dibimbing oleh Ustadz (laki-laki).';
                } elseif ($studentGender === 'P') {
                    $reasons[] = 'Santri perempuan wajib dibimbing oleh Ustazah (perempuan).';
                } else {
                    $reasons[] = 'Gender mentor tidak sesuai kriteria santri.';
                }
            }
        }

        return $reasons ?: ['Skor total kalah kompetitif dibandingkan kandidat Top 3.'];
    }

    public static function normalizeDay(string $day): string
    {
        $map = [
            'senin' => 'monday',
            'selasa' => 'tuesday',
            'rabu' => 'wednesday',
            'kamis' => 'thursday',
            'jumat' => 'friday',
            'jum\'at' => 'friday',
            'sabtu' => 'saturday',
            'minggu' => 'sunday',
        ];
        $clean = strtolower(trim($day));

        return $map[$clean] ?? $clean;
    }

    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        if (! $lat1 || ! $lon1 || ! $lat2 || ! $lon2) {
            return 8.0;
        }

        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
