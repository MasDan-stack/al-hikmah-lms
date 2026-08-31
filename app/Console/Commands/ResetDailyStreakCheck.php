<?php

namespace App\Console\Commands;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetDailyStreakCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-daily-streak';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengevaluasi santri yang tidak aktif menyetor dan mereset streak';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today()->toDateString();

        // Santri yang streak > 0 tapi setoran terakhir sebelum kemarin
        $affected = Student::where('current_streak', '>', 0)
            ->where(function ($q) use ($yesterday) {
                $q->whereNull('last_setoran_date')
                    ->orWhere('last_setoran_date', '<', $yesterday);
            })
            ->update(['current_streak' => 0]);

        $this->info("Berhasil mengevaluasi streak. {$affected} santri di-reset streak-nya.");

        return Command::SUCCESS;
    }
}
