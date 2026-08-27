<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Illuminate\Console\Command;

class SnapshotLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:snapshot-leaderboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengambil snapshot periodik peringkat leaderboard';

    /**
     * Execute the console command.
     */
    public function handle(LeaderboardService $service): int
    {
        $service->snapshot('weekly');
        $this->info('Snapshot leaderboard mingguan berhasil disimpan.');

        return Command::SUCCESS;
    }
}
