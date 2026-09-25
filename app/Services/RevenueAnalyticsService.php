<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\FinancialAuditLog;
use App\Models\Mentor;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Session;
use App\Models\Setting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RevenueAnalyticsService
{
    /**
     * Standar Tarif & Pembagian Hasil Bimbingan Al-Hikmah (Per Sesi 90 Menit)
     */
    public const RATE_PER_SESSION = 150000;

    public const MENTOR_FEE_PER_SESSION = 100000;

    public const OWNER_GROSS_PER_SESSION = 50000;

    public const INFAQ_PERCENTAGE = 0.10;

    public const INFAQ_PER_SESSION = 5000;

    public const OWNER_NET_PER_SESSION = 45000;

    /**
     * Biaya Pendaftaran Santri Baru (1x di awal)
     * Rp 150.000 langsung masuk sebagai Biaya Pendaftaran Kas Lembaga (tanpa potongan 10%).
     */
    public const REGISTRATION_FEE = 150000;

    public const OWNER_REGISTRATION_GROSS = 150000;

    public const INFAQ_REGISTRATION = 0;

    public const OWNER_REGISTRATION_NET = 150000;

    /**
     * Cache duration in minutes
     */
    protected int $cacheTtl = 15;

    /**
     * Dapatkan ringkasan metrik finansial utama (Summary Metrics)
     */
    public function getSummaryMetrics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // 1. Total Pendapatan Seluruh Waktu / Berdasarkan Rentang Tanggal
        $totalRevenueQuery = Payment::where('status', 'paid');
        if ($startDate && $endDate) {
            $totalRevenueQuery->whereBetween('payment_date', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }
        $totalRevenue = (float) $totalRevenueQuery->sum('amount');
        $totalPaidInvoices = (int) $totalRevenueQuery->count();

        // 2. Pendapatan Bulan Ini & Bulan Lalu
        $thisMonthRevenue = (float) Payment::where('status', 'paid')
            ->whereBetween('payment_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        $lastMonthRevenue = (float) Payment::where('status', 'paid')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        // 3. Month-over-Month (MoM) Growth Percentage
        if ($lastMonthRevenue > 0) {
            $momGrowthPercent = round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        } else {
            $momGrowthPercent = $thisMonthRevenue > 0 ? 100.0 : 0.0;
        }

        // 4. ARPU (Average Revenue Per User / Santri Aktif)
        $activeStudentsCount = Student::whereHas('programs', fn ($q) => $q->where('student_program.status', 'active'))->count();
        if ($activeStudentsCount === 0) {
            $activeStudentsCount = max(1, Student::count());
        }
        $arpu = round($thisMonthRevenue / max(1, $activeStudentsCount), 0);

        // 5. Tagihan Pending & Overdue
        $pendingPayments = Payment::where('status', 'pending');
        $pendingInvoicesCount = (int) $pendingPayments->count();
        $pendingInvoicesAmount = (float) $pendingPayments->sum('amount');

        $today = today();
        $overduePayments = Payment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today);
        $overdueInvoicesCount = (int) $overduePayments->count();
        $overdueInvoicesAmount = (float) $overduePayments->sum('amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_paid_invoices' => $totalPaidInvoices,
            'this_month_revenue' => $thisMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'mom_growth_percent' => $momGrowthPercent,
            'arpu' => $arpu,
            'active_students_count' => $activeStudentsCount,
            'pending_invoices_count' => $pendingInvoicesCount,
            'pending_invoices_amount' => $pendingInvoicesAmount,
            'overdue_invoices_count' => $overdueInvoicesCount,
            'overdue_invoices_amount' => $overdueInvoicesAmount,
        ];
    }

    /**
     * Dapatkan data tren pendapatan 12 bulan terakhir untuk ApexCharts
     */
    public function get12MonthsTrend(): array
    {
        return Cache::remember('revenue_analytics_12_months_trend', now()->addMinutes($this->cacheTtl), function () {
            $categories = [];
            $revenueSeries = [];
            $invoicesSeries = [];
            $lastYearSeries = [];

            $now = now();

            for ($i = 11; $i >= 0; $i--) {
                $monthDate = $now->copy()->subMonths($i);
                $monthStart = $monthDate->copy()->startOfMonth();
                $monthEnd = $monthDate->copy()->endOfMonth();

                $categories[] = $monthDate->translatedFormat('M Y');

                // Pendapatan bulan bersangkutan
                $rev = (float) Payment::where('status', 'paid')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->sum('amount');
                $revenueSeries[] = $rev;

                // Jumlah transaksi lunas
                $count = (int) Payment::where('status', 'paid')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->count();
                $invoicesSeries[] = $count;

                // Pendapatan bulan yang sama tahun lalu (YoY)
                $prevYearStart = $monthStart->copy()->subYear();
                $prevYearEnd = $monthEnd->copy()->subYear();
                $prevRev = (float) Payment::where('status', 'paid')
                    ->whereBetween('payment_date', [$prevYearStart, $prevYearEnd])
                    ->sum('amount');
                $lastYearSeries[] = $prevRev;
            }

            return [
                'categories' => $categories,
                'series' => [
                    [
                        'name' => 'Pendapatan (Rp)',
                        'type' => 'area',
                        'data' => $revenueSeries,
                    ],
                    [
                        'name' => 'Tahun Lalu (Rp)',
                        'type' => 'line',
                        'data' => $lastYearSeries,
                    ],
                ],
                'invoices_count_series' => $invoicesSeries,
            ];
        });
    }

    /**
     * Dapatkan breakdown pendapatan per program
     */
    public function getProgramBreakdown(): array
    {
        return Cache::remember('revenue_analytics_program_breakdown', now()->addMinutes($this->cacheTtl), function () {
            $programs = Program::withCount(['students' => function ($q) {
                $q->where('student_program.status', 'active');
            }])->get();

            $labels = [];
            $series = [];
            $details = [];
            $totalRevenueAll = (float) Payment::where('status', 'paid')->sum('amount');

            foreach ($programs as $program) {
                $rev = (float) Payment::where('status', 'paid')
                    ->where('program_id', $program->id)
                    ->sum('amount');

                $labels[] = $program->name;
                $series[] = $rev;

                $percentage = $totalRevenueAll > 0 ? round(($rev / $totalRevenueAll) * 100, 1) : 0;

                $details[] = [
                    'id' => $program->id,
                    'name' => $program->name,
                    'category' => $program->category ?? 'Umum',
                    'revenue' => $rev,
                    'percentage' => $percentage,
                    'active_students' => $program->students_count,
                    'price' => (float) $program->price,
                ];
            }

            // Sertakan transaksi tanpa program jika ada
            $noProgramRev = (float) Payment::where('status', 'paid')
                ->whereNull('program_id')
                ->sum('amount');

            if ($noProgramRev > 0) {
                $labels[] = 'Pendaftaran / Lainnya';
                $series[] = $noProgramRev;
                $percentage = $totalRevenueAll > 0 ? round(($noProgramRev / $totalRevenueAll) * 100, 1) : 0;
                $details[] = [
                    'id' => null,
                    'name' => 'Pendaftaran / Lainnya',
                    'category' => 'Registrasi',
                    'revenue' => $noProgramRev,
                    'percentage' => $percentage,
                    'active_students' => 0,
                    'price' => 0,
                ];
            }

            return [
                'labels' => $labels,
                'series' => $series,
                'details' => $details,
                'total_revenue' => $totalRevenueAll,
            ];
        });
    }

    /**
     * Dapatkan status distribusi pembayaran (Paid, Pending, Overdue, Cancelled)
     */
    public function getPaymentStatusDistribution(): array
    {
        $today = today();

        $paidCount = Payment::where('status', 'paid')->count();
        $paidAmount = (float) Payment::where('status', 'paid')->sum('amount');

        $pendingWithinDue = Payment::where('status', 'pending')
            ->where(function ($q) use ($today) {
                $q->whereNull('due_date')->orWhere('due_date', '>=', $today);
            })->count();
        $pendingAmount = (float) Payment::where('status', 'pending')
            ->where(function ($q) use ($today) {
                $q->whereNull('due_date')->orWhere('due_date', '>=', $today);
            })->sum('amount');

        $overdueCount = Payment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->count();
        $overdueAmount = (float) Payment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->sum('amount');

        $cancelledCount = Payment::where('status', 'cancelled')->count();
        $cancelledAmount = (float) Payment::where('status', 'cancelled')->sum('amount');

        $totalCount = $paidCount + $pendingWithinDue + $overdueCount + $cancelledCount;

        return [
            'paid' => ['count' => $paidCount, 'amount' => $paidAmount, 'percent' => $totalCount > 0 ? round(($paidCount / $totalCount) * 100, 1) : 0],
            'pending' => ['count' => $pendingWithinDue, 'amount' => $pendingAmount, 'percent' => $totalCount > 0 ? round(($pendingWithinDue / $totalCount) * 100, 1) : 0],
            'overdue' => ['count' => $overdueCount, 'amount' => $overdueAmount, 'percent' => $totalCount > 0 ? round(($overdueCount / $totalCount) * 100, 1) : 0],
            'cancelled' => ['count' => $cancelledCount, 'amount' => $cancelledAmount, 'percent' => $totalCount > 0 ? round(($cancelledCount / $totalCount) * 100, 1) : 0],
            'total_count' => $totalCount,
        ];
    }

    /**
     * Dapatkan log audit keuangan terbaru
     */
    public function getRecentFinancialAuditLogs(int $limit = 10): Collection
    {
        return FinancialAuditLog::with('user')
            ->latest('created_at')
            ->take($limit)
            ->get();
    }

    /**
     * Dapatkan rekapitulasi bagi hasil otomatis (Admin & Yayasan)
     * Formula Sesi: Rp 150rb = Mentor Rp 100rb + Owner Rp 50rb (Infaq 10% = Rp 5rb, Owner Net = Rp 45rb)
     * Formula Pendaftaran: Rp 150rb = Operasional Rp 100rb + Owner Rp 50rb (Infaq 10% = Rp 5rb, Owner Net = Rp 45rb)
     */
    public function getRevenueSharingSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Session::where('status', 'completed');
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }
        $completedSessions = (int) $query->count();

        $thisMonthSessions = (int) Session::where('status', 'completed')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        // Metrik Pendaftaran Santri Baru (payment dengan registration_fee > 0 & status paid)
        $regQuery = Payment::where('status', 'paid')->where('registration_fee', '>', 0);
        if ($startDate && $endDate) {
            $regQuery->whereBetween('payment_date', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }
        $totalRegistrations = (int) $regQuery->count();

        $thisMonthRegistrations = (int) Payment::where('status', 'paid')
            ->where('registration_fee', '>', 0)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->count();

        // Metrik Sesi Bimbingan (Infaq 10% dipotong dari hak owner Rp 50.000 = Rp 5.000 / sesi)
        $totalInfaqDakwah = $completedSessions * self::INFAQ_PER_SESSION;
        $thisMonthInfaqDakwah = $thisMonthSessions * self::INFAQ_PER_SESSION;

        // Metrik Biaya Pendaftaran (Rp 150.000 / santri masuk kas lembaga)
        $totalRegistrationAmount = $totalRegistrations * self::REGISTRATION_FEE;
        $thisMonthRegistrationAmount = $thisMonthRegistrations * self::REGISTRATION_FEE;

        return [
            'rate_per_session' => self::RATE_PER_SESSION,
            'mentor_fee_per_session' => self::MENTOR_FEE_PER_SESSION,
            'owner_gross_per_session' => self::OWNER_GROSS_PER_SESSION,
            'infaq_per_session' => self::INFAQ_PER_SESSION,
            'owner_net_per_session' => self::OWNER_NET_PER_SESSION,
            'infaq_percentage' => (int) (self::INFAQ_PERCENTAGE * 100),

            // Sesi Bimbingan (Rp 150.000 / Pertemuan)
            'completed_sessions_count' => $completedSessions,
            'total_retail_revenue' => $completedSessions * self::RATE_PER_SESSION,
            'total_mentor_honor' => $completedSessions * self::MENTOR_FEE_PER_SESSION,
            'total_owner_gross' => $completedSessions * self::OWNER_GROSS_PER_SESSION,
            'total_infaq_dakwah' => $totalInfaqDakwah,
            'total_owner_net' => $completedSessions * self::OWNER_NET_PER_SESSION,

            'this_month_sessions_count' => $thisMonthSessions,
            'this_month_retail_revenue' => $thisMonthSessions * self::RATE_PER_SESSION,
            'this_month_mentor_honor' => $thisMonthSessions * self::MENTOR_FEE_PER_SESSION,
            'this_month_owner_gross' => $thisMonthSessions * self::OWNER_GROSS_PER_SESSION,
            'this_month_infaq_dakwah' => $thisMonthInfaqDakwah,
            'this_month_owner_net' => $thisMonthSessions * self::OWNER_NET_PER_SESSION,

            // Biaya Pendaftaran (Rp 150.000 / santri)
            'registration_fee' => self::REGISTRATION_FEE,
            'total_registrations' => $totalRegistrations,
            'total_registration_amount' => $totalRegistrationAmount,
            'this_month_registrations' => $thisMonthRegistrations,
            'this_month_registration_amount' => $thisMonthRegistrationAmount,

            // Compatibility aliases
            'owner_registration_gross' => self::REGISTRATION_FEE,
            'total_registration_owner_gross' => $totalRegistrationAmount,
            'this_month_registration_owner_gross' => $thisMonthRegistrationAmount,
            'total_infaq_registration' => 0,
            'this_month_infaq_registration' => 0,
            'total_registration_owner_net' => $totalRegistrationAmount,
            'this_month_registration_owner_net' => $thisMonthRegistrationAmount,
            'total_infaq_combined' => $totalInfaqDakwah,
            'this_month_infaq_combined' => $thisMonthInfaqDakwah,
        ];
    }

    /**
     * Dapatkan data slip gaji / honorarium mengajar mentor untuk satu periode bulan.
     *
     * @return array{
     *   mentor_id: int,
     *   mentor_name: string,
     *   period_month: int,
     *   period_year: int,
     *   period_label: string,
     *   salary_status: string,
     *   sessions_a: array,
     *   students_b: array,
     *   total_valid_attendance: int,
     *   total_honor: int,
     *   rate_per_attendance: int,
     * }
     */
    /**
     * Pastikan sesi bulanan tersedia di learning_sessions untuk mentor dan santri binaannya.
     * Mencegah data slip gaji & jadwal kosong jika santri sudah dialokasikan ke mentor.
     */
    public function ensureMonthlySessionsExistForMentor(int $mentorId, ?int $month = null, ?int $year = null): void
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $existingCount = Session::where('mentor_id', $mentorId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->count();

        if ($existingCount > 0) {
            return;
        }

        $assignments = DB::table('mentor_student')
            ->where('mentor_id', $mentorId)
            ->where('is_active', true)
            ->get();

        $dayMap = [
            'sunday' => Carbon::SUNDAY,
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
            'ahad' => Carbon::SUNDAY,
            'senin' => Carbon::MONDAY,
            'selasa' => Carbon::TUESDAY,
            'rabu' => Carbon::WEDNESDAY,
            'kamis' => Carbon::THURSDAY,
            'jumat' => Carbon::FRIDAY,
            'sabtu' => Carbon::SATURDAY,
        ];

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        foreach ($assignments as $assignment) {
            $dayKey = strtolower($assignment->day_assigned ?? 'monday');
            $carbonDay = $dayMap[$dayKey] ?? Carbon::MONDAY;
            $timeAssigned = $assignment->time_assigned
                ?? ($assignment->time_label ? $assignment->time_label.':00' : '16:00:00');

            $date = $startOfMonth->copy();
            if ($date->dayOfWeek !== $carbonDay) {
                $date->next($carbonDay);
            }

            while ($date->lte($endOfMonth)) {
                Session::firstOrCreate(
                    [
                        'student_id' => $assignment->student_id,
                        'mentor_id' => $mentorId,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'time' => $timeAssigned,
                        'method' => 'offline',
                        'status' => 'pending',
                    ]
                );
                $date->addWeek();
            }
        }
    }

    public function getMentorSalarySlipData(int $mentorId, ?int $month = null, ?int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        $periodLabel = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');

        // Pastikan sesi santri binaan yang belum terhubung mentor_id disinkronkan ke mentor ini
        Session::whereNull('mentor_id')
            ->whereIn('student_id', function ($q) use ($mentorId) {
                $q->select('student_id')->from('mentor_student')->where('mentor_id', $mentorId)->where('is_active', true);
            })
            ->update(['mentor_id' => $mentorId]);

        Session::whereNull('mentor_id')
            ->whereIn('student_id', function ($q) use ($mentorId) {
                $q->select('student_id')->from('enrollments')->where('mentor_id', $mentorId);
            })
            ->update(['mentor_id' => $mentorId]);

        // Pastikan sesi bimbingan bulanan sudah di-generate jika santri sudah ditugaskan
        $this->ensureMonthlySessionsExistForMentor($mentorId, $month, $year);

        $mentor = Mentor::with('user')->find($mentorId);
        $mentorName = $mentor?->user?->name ?? $mentor?->full_name ?? 'Guru';

        // Ambil semua sesi mentor pada periode ini (termasuk yang ada konfirmasi kehadiran)
        $sessions = Session::with([
            'student.user',
            'student.enrollments.program',
            'student.programs',
            'confirmation',
        ])
            ->where('mentor_id', $mentorId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        // Bagian A: Rincian Sesi & Jadwal Mengajar
        $sessionsA = [];
        $totalProofUploaded = 0;
        $totalProofMissing = 0;

        foreach ($sessions as $session) {
            $confirmationStatus = $session->confirmation?->status;
            // hadir / terlambat = 1 kehadiran valid; izin / sakit = tidak dihitung
            $isValidAttendance = in_array($confirmationStatus, ['hadir', 'terlambat'])
                || ($confirmationStatus === null && $session->status === 'completed');

            $hasProof = ! empty($session->confirmation?->proof_image);
            if ($isValidAttendance) {
                if ($hasProof) {
                    $totalProofUploaded++;
                } else {
                    $totalProofMissing++;
                }
            }

            $programName = $session->program?->name
                ?? $session->enrollment?->program?->name
                ?? ($session->student?->programs->first()?->name)
                ?? 'Program Bimbingan';

            $sessionsA[] = [
                'session_id' => $session->id,
                'student_name' => $session->student?->user?->name ?? $session->student?->full_name ?? '-',
                'program_name' => $programName,
                'date' => $session->date->locale('id')->translatedFormat('l, d M Y'),
                'date_raw' => $session->date->format('Y-m-d'),
                'time' => Carbon::parse($session->time)->format('H:i'),
                'method' => $session->method,
                'confirmation_status' => $confirmationStatus ?? ($session->status === 'completed' ? 'hadir' : 'belum dikonfirmasi'),
                'confirmed_by' => $session->confirmation?->confirmed_by ?? ($confirmationStatus ? 'parent' : null),
                'proof_image' => $session->confirmation?->proof_image,
                'proof_image_url' => $session->confirmation?->proof_image_url,
                'has_proof' => $hasProof,
                'notes' => $session->confirmation?->notes ?? $session->notes,
                'session_status' => $session->status,
                'is_valid_attendance' => $isValidAttendance,
                'rate' => self::MENTOR_FEE_PER_SESSION,
                'amount' => $isValidAttendance ? self::MENTOR_FEE_PER_SESSION : 0,
            ];
        }

        // Bagian B: Rincian Kehadiran & Honor persantri (groupby student)
        $studentGroups = [];
        foreach ($sessions as $session) {
            $studentId = $session->student_id;
            $confirmationStatus = $session->confirmation?->status;
            $isValidAttendance = in_array($confirmationStatus, ['hadir', 'terlambat'])
                || ($confirmationStatus === null && $session->status === 'completed');

            if (! isset($studentGroups[$studentId])) {
                $progName = $session->program?->name
                    ?? $session->enrollment?->program?->name
                    ?? ($session->student?->programs->first()?->name)
                    ?? 'Program Bimbingan';

                $studentGroups[$studentId] = [
                    'student_id' => $studentId,
                    'student_name' => $session->student?->user?->name ?? $session->student?->full_name ?? '-',
                    'program_name' => $progName,
                    'total_sessions' => 0,
                    'valid_attendance' => 0,
                    'subtotal' => 0,
                    'proof_uploaded_count' => 0,
                    'proof_missing_count' => 0,
                ];
            }

            $studentGroups[$studentId]['total_sessions']++;
            if ($isValidAttendance) {
                $studentGroups[$studentId]['valid_attendance']++;
                $studentGroups[$studentId]['subtotal'] += self::MENTOR_FEE_PER_SESSION;
                if (! empty($session->confirmation?->proof_image)) {
                    $studentGroups[$studentId]['proof_uploaded_count']++;
                } else {
                    $studentGroups[$studentId]['proof_missing_count']++;
                }
            }
        }
        $studentsB = array_values($studentGroups);

        $totalValidAttendance = array_sum(array_column($studentsB, 'valid_attendance'));
        $totalHonor = $totalValidAttendance * self::MENTOR_FEE_PER_SESSION;

        // Status pembayaran slip
        $salaryStatusKey = "mentor_salary_status_{$mentorId}_{$year}_{$month}";
        $salaryStatus = Setting::get($salaryStatusKey, 'pending');

        return [
            'mentor_id' => $mentorId,
            'mentor_name' => $mentorName,
            'period_month' => $month,
            'period_year' => $year,
            'period_label' => $periodLabel,
            'salary_status' => $salaryStatus,
            'sessions_a' => $sessionsA,
            'students_b' => $studentsB,
            'total_sessions' => $sessions->count(),
            'total_valid_attendance' => $totalValidAttendance,
            'total_proof_uploaded' => $totalProofUploaded,
            'total_proof_missing' => $totalProofMissing,
            'total_honor' => $totalHonor,
            'rate_per_attendance' => self::MENTOR_FEE_PER_SESSION,
        ];
    }

    /**
     * Tandai status pembayaran honor bulanan mentor (dipanggil oleh Admin).
     */
    public function markMentorSalaryStatus(
        int $mentorId,
        int $year,
        int $month,
        string $status,
    ): void {
        $key = "mentor_salary_status_{$mentorId}_{$year}_{$month}";
        Setting::set($key, $status);
    }

    /**
     * Dapatkan ringkasan akumulasi honor mentor (Khusus Mentor - Margin & Harga Retail Terisolasi Aman)
     * Sinkron otomatis dengan input absensi guru & orang tua serta target sesi program santri.
     */
    public function getMentorHonorariumSummary(int $mentorId): array
    {
        // 1. Sesi Selesai Semua Waktu (status completed ATAU konfirmasi hadir/terlambat, tidak izin/sakit)
        $completedSessions = (int) Session::where('mentor_id', $mentorId)
            ->where(function ($q) {
                $q->whereHas('confirmation', fn ($cq) => $cq->whereIn('status', ['hadir', 'terlambat']))
                    ->orWhere(function ($sq) {
                        $sq->where('status', 'completed')
                            ->whereDoesntHave('confirmation', fn ($cq) => $cq->whereIn('status', ['izin', 'sakit']));
                    });
            })
            ->count();

        // 2. Sesi Selesai Bulan Ini
        $thisMonthSessions = (int) Session::where('mentor_id', $mentorId)
            ->where(function ($q) {
                $q->whereHas('confirmation', fn ($cq) => $cq->whereIn('status', ['hadir', 'terlambat']))
                    ->orWhere(function ($sq) {
                        $sq->where('status', 'completed')
                            ->whereDoesntHave('confirmation', fn ($cq) => $cq->whereIn('status', ['izin', 'sakit']));
                    });
            })
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        // 3. Hitung target total sesi program dari santri-santri yang dibimbing mentor ini
        // (Tergantung sesi program yang dipilih orang tua / admin pada fitur program saat pendaftaran)
        $activeMentorStudents = DB::table('mentor_student')
            ->where('mentor_id', $mentorId)
            ->where('is_active', true)
            ->get();

        $targetTotalSessionsThisMonth = 0;
        $countedStudents = [];

        foreach ($activeMentorStudents as $ms) {
            if (in_array($ms->student_id, $countedStudents)) {
                continue;
            }
            $countedStudents[] = $ms->student_id;

            $program = null;
            if ($ms->program_id) {
                $program = Program::find($ms->program_id);
            }
            if (! $program) {
                $activeEnrollment = Enrollment::where('student_id', $ms->student_id)
                    ->where('mentor_id', $mentorId)
                    ->whereIn('status', [EnrollmentStatus::ACTIVE->value, EnrollmentStatus::CONFIRMED->value])
                    ->first();
                $program = $activeEnrollment?->program;
            }
            if (! $program) {
                $student = Student::with('programs')->find($ms->student_id);
                $program = $student?->programs->first();
            }

            $programSessions = $program?->duration_weeks ?? 8;
            $targetTotalSessionsThisMonth += $programSessions;
        }

        // Cek juga dari enrollment aktif jika belum masuk pivot
        $activeEnrollments = Enrollment::with('program')
            ->where('mentor_id', $mentorId)
            ->whereIn('status', [EnrollmentStatus::ACTIVE->value, EnrollmentStatus::CONFIRMED->value])
            ->get();

        foreach ($activeEnrollments as $enr) {
            if (! in_array($enr->student_id, $countedStudents)) {
                $countedStudents[] = $enr->student_id;
                $targetTotalSessionsThisMonth += $enr->program?->duration_weeks ?? 8;
            }
        }

        // 4. Hitung sesi mendatang yang belum selesai di tabel learning_sessions
        $scheduledFutureSessions = (int) Session::where('mentor_id', $mentorId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->whereDoesntHave('confirmation', fn ($cq) => $cq->whereIn('status', ['hadir', 'terlambat']))
            ->count();

        // Jika target dari program lebih besar dari 0, estimasi sesi mendatang = max(0, targetTotal - thisMonthCompleted)
        if ($targetTotalSessionsThisMonth > 0) {
            $upcomingSessions = max(0, $targetTotalSessionsThisMonth - $thisMonthSessions);
        } else {
            $upcomingSessions = $scheduledFutureSessions;
        }

        return [
            'rate_per_session' => self::MENTOR_FEE_PER_SESSION,
            'this_month_sessions' => $thisMonthSessions,
            'this_month_honor' => $thisMonthSessions * self::MENTOR_FEE_PER_SESSION,
            'total_completed_sessions' => $completedSessions,
            'total_honor' => $completedSessions * self::MENTOR_FEE_PER_SESSION,
            'upcoming_sessions' => $upcomingSessions,
            'estimated_upcoming_honor' => $upcomingSessions * self::MENTOR_FEE_PER_SESSION,
        ];
    }

    /**
     * Dapatkan ringkasan sesi & alokasi berkah infaq 10% (Untuk Orang Tua Santri)
     */
    public function getParentSessionBlessingSummary(array $childIds): array
    {
        if (empty($childIds)) {
            return [
                'completed_sessions' => 0,
                'this_month_sessions' => 0,
                'infaq_allocated' => 0,
                'infaq_percentage' => (int) (self::INFAQ_PERCENTAGE * 100),
                'rate_per_session' => self::RATE_PER_SESSION,
            ];
        }

        $completedSessions = (int) Session::whereIn('student_id', $childIds)
            ->where('status', 'completed')
            ->count();

        $thisMonthSessions = (int) Session::whereIn('student_id', $childIds)
            ->where('status', 'completed')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        return [
            'completed_sessions' => $completedSessions,
            'this_month_sessions' => $thisMonthSessions,
            'infaq_allocated' => $completedSessions * self::INFAQ_PER_SESSION,
            'infaq_percentage' => (int) (self::INFAQ_PERCENTAGE * 100),
            'rate_per_session' => self::RATE_PER_SESSION,
        ];
    }

    /**
     * Bersihkan cache analitik
     */
    public function clearCache(): void
    {
        Cache::forget('revenue_analytics_12_months_trend');
        Cache::forget('revenue_analytics_program_breakdown');
    }
}
