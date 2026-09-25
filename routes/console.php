<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\SystemHeartbeat;
use App\Services\SmartLoadBalancerService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Helper untuk mencatat detak jantung scheduler ke tabel system_heartbeats
 */
if (! function_exists('recordSchedulerHeartbeat')) {
    function recordSchedulerHeartbeat(string $jobName, bool $success, ?string $errorMessage = null): void
    {
        try {
            SystemHeartbeat::updateOrCreate(
                ['job_name' => $jobName],
                [
                    'ran_at' => now(),
                    'status' => $success ? 'success' : 'failed',
                    'error_message' => $errorMessage,
                ]
            );
        } catch (Throwable $e) {
            // Fail-safe jika tabel belum siap
        }
    }
}

Schedule::call(function () {
    Enrollment::where('status', EnrollmentStatus::WAITING_ADMIN->value)
        ->where('created_at', '<', now()->subDays(7))
        ->update([
            'status' => EnrollmentStatus::CANCELLED->value,
            'admin_notes' => 'Otomatis dibatalkan oleh sistem karena tidak diproses lebih dari 7 hari.',
        ]);

    Enrollment::where('status', EnrollmentStatus::WAITING_PARENT->value)
        ->where('updated_at', '<', now()->subDays(7))
        ->update([
            'status' => EnrollmentStatus::CANCELLED->value,
            'admin_notes' => 'Otomatis dibatalkan oleh sistem karena wali santri tidak merespon tawaran jadwal lebih dari 7 hari.',
        ]);
})->daily()
    ->name('expire-stale-enrollments')
    ->onSuccess(fn () => recordSchedulerHeartbeat('expire-stale-enrollments', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('expire-stale-enrollments', false));

// 🔔 Pemindaian Anomali Operasional 3 Kali Sehari (06:00, 12:00, 18:00 WIB)
Schedule::command('alerts:scan')
    ->cron('0 6,12,18 * * *')
    ->name('scan-alerts-3-times-daily')
    ->onSuccess(fn () => recordSchedulerHeartbeat('scan-alerts-3-times-daily', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('scan-alerts-3-times-daily', false));

// 🏆 Refresh Cache Leaderboard Gamifikasi Santri Setiap Tengah Malam (00:00 WIB)
Schedule::command('gamification:refresh-leaderboard')
    ->dailyAt('00:00')
    ->name('refresh-leaderboard-midnight')
    ->onSuccess(fn () => recordSchedulerHeartbeat('refresh-leaderboard-midnight', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('refresh-leaderboard-midnight', false));

// 📊 Snapshot Bulanan Skor Komposit Kinerja Mentor (Setiap tanggal 1 pukul 00:05 WIB)
Schedule::command('mentor:snapshot-performance')
    ->monthlyOn(1, '00:05')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->name('snapshot-mentor-performance-monthly')
    ->onSuccess(fn () => recordSchedulerHeartbeat('snapshot-mentor-performance-monthly', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('snapshot-mentor-performance-monthly', false));

// 🔮 Predictive Analytics Snapshot Harian (Setiap pukul 01:00 WIB)
Schedule::command('analytics:snapshot-predictive')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->name('snapshot-predictive-analytics')
    ->onSuccess(fn () => recordSchedulerHeartbeat('snapshot-predictive-analytics', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('snapshot-predictive-analytics', false));

// ⏰ Sinkronisasi Harian Metrik Masa Percobaan Guru & Peringatan Evaluasi H-14 (Setiap pukul 00:00 WIB)
Schedule::command('probation:daily-sync --notify')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->name('sync-daily-mentor-probation')
    ->onSuccess(fn () => recordSchedulerHeartbeat('sync-daily-mentor-probation', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('sync-daily-mentor-probation', false));

// ⚖️ Evaluasi Harian Indeks Kelelahan & Smart Load Balancing Mentor (Setiap pukul 00:30 WIB)
Schedule::call(function () {
    $mentors = Mentor::where('is_active', true)->get();
    $loadBalancer = app(SmartLoadBalancerService::class);
    foreach ($mentors as $mentor) {
        $loadBalancer->calculateBurnoutIndex($mentor);
    }
})->dailyAt('00:30')
    ->timezone('Asia/Jakarta')
    ->name('evaluate-mentor-burnout-daily')
    ->onSuccess(fn () => recordSchedulerHeartbeat('evaluate-mentor-burnout-daily', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('evaluate-mentor-burnout-daily', false));

// 💾 Pencadangan Basis Data & Sistem Otomatis Harian (Setiap pukul 01:00 WIB)
Schedule::command('backup:run --type=daily')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->name('backup-system-daily')
    ->onSuccess(fn () => recordSchedulerHeartbeat('backup-system-daily', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('backup-system-daily', false, 'Eksekusi backup harian gagal'));

// 🧪 Verifikasi Pemulihan Cadangan Mingguan / Dry-Run Restore (Setiap Ahad pukul 04:00 WIB)
Schedule::command('backup:verify-restore')
    ->weeklyOn(0, '04:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->name('backup-verify-restore-weekly')
    ->onSuccess(fn () => recordSchedulerHeartbeat('backup-verify-restore-weekly', true))
    ->onFailure(fn () => recordSchedulerHeartbeat('backup-verify-restore-weekly', false, 'Verifikasi restore mingguan gagal'));
