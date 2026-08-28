<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\HifzTarget;
use App\Models\Mentor;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentBadge;
use Carbon\Carbon;

class AlertService
{
    /**
     * Dapatkan semua alert yang dikelompokkan berdasarkan level urgensi
     */
    public function getAllAlerts(): array
    {
        $critical = $this->getCriticalAlerts();
        $warning = $this->getWarningAlerts();
        $info = $this->getInfoAlerts();

        $criticalCount = array_sum(array_column($critical, 'count'));
        $warningCount = array_sum(array_column($warning, 'count'));
        $infoCount = array_sum(array_column($info, 'count'));

        return [
            'critical' => $critical,
            'warning' => $warning,
            'info' => $info,
            'critical_count' => $criticalCount,
            'warning_count' => $warningCount,
            'info_count' => $infoCount,
            'total_count' => $criticalCount + $warningCount + $infoCount,
        ];
    }

    /**
     * Dapatkan jumlah alert kritis untuk badge navbar
     */
    public function getCriticalCount(): int
    {
        $critical = $this->getCriticalAlerts();

        return array_sum(array_column($critical, 'count'));
    }

    /**
     * 🔴 KRITIS (Harus Segera Ditangani)
     */
    public function getCriticalAlerts(): array
    {
        $alerts = [];
        $today = today();

        // 1. Tagihan Overdue > 30 hari (Risiko piutang macet)
        $overdue30Days = Payment::with(['student.user', 'program'])
            ->where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today->copy()->subDays(30))
            ->get();

        if ($overdue30Days->isNotEmpty()) {
            $totalAmount = $overdue30Days->sum('amount');
            $alerts[] = [
                'id' => 'crit_overdue_30',
                'level' => 'critical',
                'category' => 'financial',
                'icon' => 'bi-exclamation-triangle-fill',
                'title' => 'Tagihan Overdue > 30 Hari',
                'description' => "Terdapat {$overdue30Days->count()} tagihan dengan total Rp ".number_format($totalAmount, 0, ',', '.').' yang telah melewati jatuh tempo lebih dari 30 hari.',
                'count' => $overdue30Days->count(),
                'action_label' => 'Kelola Tagihan Overdue',
                'action_url' => route('admin.payments.index', ['status' => 'overdue']),
                'items' => $overdue30Days->take(5)->map(fn ($p) => [
                    'title' => "Invoice #{$p->invoice_number} - {$p->student?->getDisplayName()}",
                    'subtitle' => 'Rp '.number_format($p->amount, 0, ',', '.').' (Jatuh tempo: '.Carbon::parse($p->due_date)->format('d/m/Y').')',
                    'url' => route('admin.payments.index'),
                ])->toArray(),
            ];
        }

        // 2. Santri Tidak Aktif > 30 hari (Risiko dropout)
        $inactive30DaysStudents = Student::with('user')
            ->whereHas('programs', fn ($q) => $q->where('student_program.status', 'active'))
            ->where(function ($q) use ($today) {
                $q->where('last_setoran_date', '<', $today->copy()->subDays(30))
                    ->orWhere(function ($q2) use ($today) {
                        $q2->whereNull('last_setoran_date')
                            ->where('created_at', '<', $today->copy()->subDays(30));
                    });
            })
            ->get();

        if ($inactive30DaysStudents->isNotEmpty()) {
            $alerts[] = [
                'id' => 'crit_inactive_students_30',
                'level' => 'critical',
                'category' => 'academic',
                'icon' => 'bi-person-x-fill',
                'title' => 'Santri Tidak Aktif > 30 Hari',
                'description' => "Sebanyak {$inactive30DaysStudents->count()} santri aktif tidak melakukan setoran mutaba'ah selama lebih dari 30 hari.",
                'count' => $inactive30DaysStudents->count(),
                'action_label' => 'Lihat Database Santri',
                'action_url' => route('admin.students.index'),
                'items' => $inactive30DaysStudents->take(5)->map(fn ($s) => [
                    'title' => $s->getDisplayName(),
                    'subtitle' => 'Terakhir setoran: '.($s->last_setoran_date ? Carbon::parse($s->last_setoran_date)->diffForHumans() : 'Belum pernah'),
                    'url' => route('admin.students.index'),
                ])->toArray(),
            ];
        }

        // 3. Mentor Overload (> 40 santri binaan aktif)
        $overloadMentors = Mentor::with('user')
            ->withCount(['students' => function ($q) {
                $q->where('mentor_student.is_active', true);
            }])
            ->having('students_count', '>', 40)
            ->get();

        if ($overloadMentors->isNotEmpty()) {
            $alerts[] = [
                'id' => 'crit_mentor_overload',
                'level' => 'critical',
                'category' => 'hr',
                'icon' => 'bi-person-lines-fill',
                'title' => 'Mentor Overload (> 40 Santri)',
                'description' => "Terdapat {$overloadMentors->count()} guru pembimbing membina lebih dari 40 santri aktif yang berpotensi menurunkan mutu pendampingan.",
                'count' => $overloadMentors->count(),
                'action_label' => 'Atur Ulang Alokasi Guru',
                'action_url' => route('admin.mentors.availability'),
                'items' => $overloadMentors->take(5)->map(fn ($m) => [
                    'title' => $m->getDisplayName()." ({$m->students_count} Santri)",
                    'subtitle' => "Spesialisasi: {$m->specialization}",
                    'url' => route('admin.mentors.availability'),
                ])->toArray(),
            ];
        }

        // 4. Payment Gateway Error / Pending Lama (>24 Jam)
        $stalePendingPayments = Payment::where('status', 'pending')
            ->whereNotNull('pakasir_order_id')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        if ($stalePendingPayments->isNotEmpty()) {
            $alerts[] = [
                'id' => 'crit_gateway_stale',
                'level' => 'critical',
                'category' => 'system',
                'icon' => 'bi-shield-slash-fill',
                'title' => 'Transaksi Gateway Pending > 24 Jam',
                'description' => "Terdapat {$stalePendingPayments->count()} transaksi online belum terselesaikan dalam 24 jam terakhir.",
                'count' => $stalePendingPayments->count(),
                'action_label' => 'Periksa Transaksi',
                'action_url' => route('admin.payments.index'),
                'items' => $stalePendingPayments->take(5)->map(fn ($p) => [
                    'title' => "Invoice #{$p->invoice_number}",
                    'subtitle' => 'Dibuat: '.$p->created_at->diffForHumans(),
                    'url' => route('admin.payments.index'),
                ])->toArray(),
            ];
        }

        return $alerts;
    }

    /**
     * 🟡 PERHATIAN (Perlu Monitoring Berkala)
     */
    public function getWarningAlerts(): array
    {
        $alerts = [];
        $today = today();

        // 1. Tagihan Jatuh Tempo Dekat (7 – 30 hari overdue / jatuh tempo 7 hari ke depan)
        $dueSoonPayments = Payment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$today->copy()->subDays(30), $today->copy()->addDays(7)])
            ->get();

        if ($dueSoonPayments->isNotEmpty()) {
            $alerts[] = [
                'id' => 'warn_due_soon',
                'level' => 'warning',
                'category' => 'financial',
                'icon' => 'bi-hourglass-split',
                'title' => 'Tagihan Jatuh Tempo (7 – 30 Hari)',
                'description' => "Sebanyak {$dueSoonPayments->count()} tagihan berada dalam masa jatuh tempo aktif atau baru lewat tempo < 30 hari.",
                'count' => $dueSoonPayments->count(),
                'action_label' => 'Kirim Pengingat SPP',
                'action_url' => route('admin.payments.index'),
                'items' => $dueSoonPayments->take(5)->map(fn ($p) => [
                    'title' => "Invoice #{$p->invoice_number}",
                    'subtitle' => 'Jatuh tempo: '.Carbon::parse($p->due_date)->translatedFormat('d F Y'),
                    'url' => route('admin.payments.index'),
                ])->toArray(),
            ];
        }

        // 2. Santri Tidak Aktif (14 – 30 hari tanpa mutaba'ah)
        $inactive14DaysStudents = Student::whereHas('programs', fn ($q) => $q->where('student_program.status', 'active'))
            ->whereBetween('last_setoran_date', [$today->copy()->subDays(30), $today->copy()->subDays(14)])
            ->get();

        if ($inactive14DaysStudents->isNotEmpty()) {
            $alerts[] = [
                'id' => 'warn_inactive_14',
                'level' => 'warning',
                'category' => 'academic',
                'icon' => 'bi-clock-history',
                'title' => 'Santri Tidak Mutaba\'ah (14 – 30 Hari)',
                'description' => "Terdapat {$inactive14DaysStudents->count()} santri belum menyetor hafalan dalam 2-4 pekan terakhir.",
                'count' => $inactive14DaysStudents->count(),
                'action_label' => 'Cek Santri',
                'action_url' => route('admin.students.index'),
                'items' => $inactive14DaysStudents->take(5)->map(fn ($s) => [
                    'title' => $s->getDisplayName(),
                    'subtitle' => 'Terakhir: '.Carbon::parse($s->last_setoran_date)->diffForHumans(),
                    'url' => route('admin.students.index'),
                ])->toArray(),
            ];
        }

        // 3. Pendaftaran Baru Belum Dialokasi Mentor (> 3 hari)
        $unassignedEnrollments = Enrollment::where('status', EnrollmentStatus::WAITING_ADMIN->value)
            ->where('created_at', '<', now()->subDays(3))
            ->get();

        if ($unassignedEnrollments->isNotEmpty()) {
            $alerts[] = [
                'id' => 'warn_unassigned_enrollments',
                'level' => 'warning',
                'category' => 'academic',
                'icon' => 'bi-person-plus-fill',
                'title' => 'Pendaftaran Belum Dialokasi Guru (> 3 Hari)',
                'description' => "Ada {$unassignedEnrollments->count()} permohonan pendaftaran baru menunggu alokasi guru lebih dari 3 hari.",
                'count' => $unassignedEnrollments->count(),
                'action_label' => 'Proses Pendaftaran',
                'action_url' => route('admin.enrollments.index'),
                'items' => $unassignedEnrollments->take(5)->map(fn ($e) => [
                    'title' => 'Pendaftaran #ENR-'.str_pad($e->id, 5, '0', STR_PAD_LEFT).' ('.$e->student?->getDisplayName().')',
                    'subtitle' => 'Program: '.($e->program?->name ?? '-'),
                    'url' => route('admin.enrollments.edit', $e->id),
                ])->toArray(),
            ];
        }

        // 4. Mentor Belum Menginput Target Santri (> 7 hari)
        $recentTargetMentorUserIds = HifzTarget::where('created_at', '>=', now()->subDays(7))
            ->pluck('mentor_id')
            ->filter()
            ->unique()
            ->toArray();

        $mentorsWithoutRecentTarget = Mentor::where('is_active', true)
            ->whereNotIn('user_id', $recentTargetMentorUserIds)
            ->get();

        if ($mentorsWithoutRecentTarget->isNotEmpty()) {
            $alerts[] = [
                'id' => 'warn_mentors_no_target',
                'level' => 'warning',
                'category' => 'academic',
                'icon' => 'bi-bullseye',
                'title' => 'Mentor Belum Input Target Hafalan (> 7 Hari)',
                'description' => "Sebanyak {$mentorsWithoutRecentTarget->count()} guru belum menetapkan target hafalan baru untuk santrinya minggu ini.",
                'count' => $mentorsWithoutRecentTarget->count(),
                'action_label' => 'Hubungi Mentor',
                'action_url' => route('admin.mentors.index'),
                'items' => $mentorsWithoutRecentTarget->take(5)->map(fn ($m) => [
                    'title' => $m->getDisplayName(),
                    'subtitle' => "Spesialisasi: {$m->specialization}",
                    'url' => route('admin.mentors.index'),
                ])->toArray(),
            ];
        }

        return $alerts;
    }

    /**
     * 🟢 INFO (Pemberitahuan Rutin 7 Hari Terakhir)
     */
    public function getInfoAlerts(): array
    {
        $alerts = [];
        $last7Days = now()->subDays(7);

        // 1. Santri baru berhasil terdaftar (7 hari terakhir)
        $newStudentsCount = Student::where('created_at', '>=', $last7Days)->count();
        if ($newStudentsCount > 0) {
            $alerts[] = [
                'id' => 'info_new_students',
                'level' => 'info',
                'category' => 'academic',
                'icon' => 'bi-person-check-fill',
                'title' => 'Santri Baru Terdaftar',
                'description' => "Alhamdulillah, {$newStudentsCount} santri baru telah terdaftar di sistem dalam 7 hari terakhir.",
                'count' => $newStudentsCount,
                'action_label' => 'Buka Database Santri',
                'action_url' => route('admin.students.index'),
                'items' => [],
            ];
        }

        // 2. Pembayaran SPP / Pendaftaran terkonfirmasi lunas (7 hari terakhir)
        $recentPaidPayments = Payment::where('status', 'paid')
            ->where('payment_date', '>=', $last7Days)
            ->get();

        if ($recentPaidPayments->isNotEmpty()) {
            $paidTotal = $recentPaidPayments->sum('amount');
            $alerts[] = [
                'id' => 'info_paid_payments',
                'level' => 'info',
                'category' => 'financial',
                'icon' => 'bi-cash-coin',
                'title' => 'Pembayaran Masuk Pekan Ini',
                'description' => "Telah terkonfirmasi {$recentPaidPayments->count()} transaksi lunas senilai Rp ".number_format($paidTotal, 0, ',', '.').' dalam 7 hari terakhir.',
                'count' => $recentPaidPayments->count(),
                'action_label' => 'Lihat Riwayat Tagihan',
                'action_url' => route('admin.payments.index'),
                'items' => [],
            ];
        }

        // 3. Santri meraih lencana baru
        $recentBadgesCount = StudentBadge::where('earned_at', '>=', $last7Days)->count();
        if ($recentBadgesCount > 0) {
            $alerts[] = [
                'id' => 'info_badges_earned',
                'level' => 'info',
                'category' => 'gamification',
                'icon' => 'bi-award-fill',
                'title' => 'Lencana Santri Diraih',
                'description' => "Sebanyak {$recentBadgesCount} lencana Islami baru berhasil diraih santri dalam sepekan terakhir.",
                'count' => $recentBadgesCount,
                'action_label' => 'Pantau Leaderboard',
                'action_url' => route('admin.gamification.leaderboard'),
                'items' => [],
            ];
        }

        // 4. Target hafalan harian diselesaikan santri
        $completedTargetsCount = HifzTarget::where('status', 'completed')
            ->where('completed_at', '>=', $last7Days)
            ->count();

        if ($completedTargetsCount > 0) {
            $alerts[] = [
                'id' => 'info_targets_completed',
                'level' => 'info',
                'category' => 'academic',
                'icon' => 'bi-check2-all',
                'title' => 'Target Hafalan Terselesaikan',
                'description' => "Santri telah menuntaskan {$completedTargetsCount} target hafalan Al-Qur'an dalam 7 hari terakhir.",
                'count' => $completedTargetsCount,
                'action_label' => 'Lihat Sesi Belajar',
                'action_url' => route('admin.active-enrollments.index'),
                'items' => [],
            ];
        }

        return $alerts;
    }
}
