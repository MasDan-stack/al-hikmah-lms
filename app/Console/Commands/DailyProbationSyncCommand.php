<?php

namespace App\Console\Commands;

use App\Models\MentorProbationTracking;
use App\Models\User;
use App\Services\MentorProbationService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailyProbationSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'probation:daily-sync {--notify : Kirim notifikasi WhatsApp ke Admin jika masa percobaan < 14 hari dan KPI belum terpenuhi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi harian metrik aktual LMS untuk guru masa percobaan (probation) dan peringatan evaluasi H-14';

    /**
     * Execute the console command.
     */
    public function handle(MentorProbationService $probationService, WhatsAppService $whatsAppService): int
    {
        $this->info('⏰ Memulai sinkronisasi harian masa percobaan (probation) guru...');

        $activeProbations = MentorProbationTracking::where('status', 'active')
            ->with(['mentor.user'])
            ->get();

        if ($activeProbations->isEmpty()) {
            $this->info('ℹ️ Tidak ada guru baru dalam masa percobaan aktif saat ini.');

            return Command::SUCCESS;
        }

        $tableRows = [];
        $atRiskMentors = [];

        foreach ($activeProbations as $probation) {
            // 1. Sinkronkan data aktual dari LMS
            $probationService->syncTrackingStats($probation);
            $probation->refresh();

            $mentor = $probation->mentor;
            $mentorName = $mentor?->getDisplayName() ?? "Mentor #{$probation->mentor_id}";
            $endDate = $probation->end_date ? Carbon::parse($probation->end_date) : null;
            if ($endDate) {
                $remainingDays = $endDate->isPast() ? 0 : (int) Carbon::today()->diffInDays($endDate);
            } else {
                $remainingDays = 90;
            }

            $rating = (float) ($probation->average_rating ?? 5.0);
            $attendance = (float) ($probation->attendance_rate ?? 100.0);
            $modulesCompleted = (int) ($probation->training_modules_completed ?? 0);
            $modulesRequired = (int) ($probation->training_modules_required ?? 4);

            // Cek kriteria KPI: rating >= 4.50, attendance >= 90%, modul tuntas
            $isKpiMet = ($rating >= 4.50 && $attendance >= 90.0 && $modulesCompleted >= $modulesRequired);
            $isAtRisk = ($remainingDays <= 14 && ! $isKpiMet);

            if ($isAtRisk) {
                $atRiskMentors[] = [
                    'mentor' => $mentor,
                    'mentor_name' => $mentorName,
                    'remaining_days' => max(0, $remainingDays),
                    'rating' => $rating,
                    'attendance' => $attendance,
                    'modules' => "{$modulesCompleted}/{$modulesRequired}",
                ];
            }

            $tableRows[] = [
                $mentorName,
                $endDate ? $endDate->translatedFormat('d M Y') : '-',
                $remainingDays > 0 ? "{$remainingDays} Hari" : 'Hari Ini / Lewat',
                "{$attendance}%",
                "⭐ {$rating}",
                "{$modulesCompleted}/{$modulesRequired}",
                $isAtRisk ? '⚠️ Berisiko (H-14)' : ($isKpiMet ? '✅ Memenuhi KPI' : '🟡 Berjalan'),
            ];
        }

        $this->table(
            ['Nama Guru', 'Batas Akhir', 'Sisa Waktu', 'Kehadiran', 'Rating', 'Modul', 'Status Evaluasi'],
            $tableRows
        );

        $atRiskCount = count($atRiskMentors);
        $this->info("✅ Sinkronisasi selesai untuk {$activeProbations->count()} guru. Guru Berisiko (H-14): {$atRiskCount}.");

        // 2. Kirim Notifikasi WhatsApp ke Admin jika opsi notify diaktifkan atau saat mendeteksi at-risk
        if ($atRiskCount > 0 && ($this->option('notify') || app()->environment('production'))) {
            $adminPhone = config('services.admin.phone', env('ADMIN_PHONE'))
                ?? User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->whereNotNull('phone')->first()?->phone;

            if ($adminPhone) {
                $mentorListText = '';
                foreach ($atRiskMentors as $idx => $risk) {
                    $num = $idx + 1;
                    $mentorListText .= "{$num}. *{$risk['mentor_name']}* (Sisa {$risk['remaining_days']} hari)\n"
                        ."   • Kehadiran: {$risk['attendance']}% (Target: ≥90%)\n"
                        ."   • Rating: ⭐ {$risk['rating']}/5.0 (Target: ≥4.50)\n"
                        ."   • Modul Orientasi: {$risk['modules']}\n";
                }

                $msg = "🚨 *PERINGATAN EVALUASI MASA PERCOBAAN GURU (H-14)* 🚨\n\n"
                    ."Assalamu'alaikum Admin Al-Hikmah LMS,\n"
                    ."Sistem mendeteksi *{$atRiskCount} guru baru* mendekati akhir 90 hari masa percobaan namun capaian KPI belum memenuhi syarat kelulusan:\n\n"
                    .$mentorListText."\n"
                    ."Silakan lakukan pendampingan atau evaluasi kinerja di portal admin:\n"
                    .route('admin.mentors.probation.index');

                $sent = $whatsAppService->sendMessage($adminPhone, $msg);
                if ($sent) {
                    $this->info("📲 Notifikasi peringatan H-14 berhasil dikirim ke Admin ({$adminPhone}).");
                } else {
                    $this->warn("⚠️ Gagal mengirim notifikasi WhatsApp ke Admin ({$adminPhone}).");
                }
            } else {
                $this->warn('⚠️ Nomor WhatsApp Admin belum dikonfigurasi (ADMIN_PHONE). Pesan tidak dikirim.');
            }
        }

        return Command::SUCCESS;
    }
}
