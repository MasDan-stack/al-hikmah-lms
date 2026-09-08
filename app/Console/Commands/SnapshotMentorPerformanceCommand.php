<?php

namespace App\Console\Commands;

use App\Services\MentorInsightsService;
use App\Services\MentorPerformanceService;
use Illuminate\Console\Command;

class SnapshotMentorPerformanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mentor:snapshot-performance {--month= : Periode bulan format YYYY-MM}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hitung skor komposit bulanan, simpan snapshot, dan generate AI insights seluruh guru';

    /**
     * Execute the console command.
     */
    public function handle(MentorPerformanceService $performanceService, MentorInsightsService $insightsService): int
    {
        $month = $this->option('month') ?: now()->format('Y-m');
        $this->info("🔄 Memulai kalkulasi snapshot kinerja mentor untuk periode {$month}...");

        $count = $performanceService->snapshotAllMentors($month);
        $this->info("✅ Berhasil menyimpan {$count} snapshot performa mentor.");

        return Command::SUCCESS;
    }
}
