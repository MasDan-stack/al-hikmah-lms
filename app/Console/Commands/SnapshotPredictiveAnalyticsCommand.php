<?php

namespace App\Console\Commands;

use App\Services\PredictiveAnalytics\DropoutPredictionService;
use App\Services\PredictiveAnalytics\LearningVelocityService;
use App\Services\PredictiveAnalytics\RevenueForecastService;
use App\Services\PredictiveAnalytics\TeacherPredictionService;
use Illuminate\Console\Command;

class SnapshotPredictiveAnalyticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:snapshot-predictive {--force : Paksa hitung ulang snapshot hari ini}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghitung snapshot harian analitik prediktif dalam batch (Dropout, Velocity, Revenue, Teacher)';

    /**
     * Execute the console command.
     */
    public function handle(
        DropoutPredictionService $dropoutService,
        LearningVelocityService $velocityService,
        TeacherPredictionService $teacherService,
        RevenueForecastService $revenueService
    ): int {
        $this->info('🚀 Memulai Komputasi Predictive Analytics Snapshot...');

        if ($this->option('force')) {
            $this->info('🔄 Opsi force aktif: Menghapus cache prediksi...');
            $dropoutService->invalidateCache();
        }

        $this->info('📊 1. Menghitung Skor Risiko Dropout Santri...');
        $dropoutService->snapshotDailyPredictions();

        $this->info('📈 2. Menghitung Learning Velocity Santri...');
        $velocityService->snapshotDailyVelocities();

        $this->info('👨‍🏫 3. Menghitung Prediksi Performa & Coaching Guru...');
        $teacherService->snapshotDailyPredictions();

        $this->info('💰 4. Menghitung Revenue Forecasting 6 Bulan...');
        $revenueService->snapshotMonthlyForecast();

        $this->info('🎉 Seluruh Snapshot Predictive Analytics Selesai!');

        return Command::SUCCESS;
    }
}
