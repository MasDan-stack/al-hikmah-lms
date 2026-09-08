<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Illuminate\Console\Command;

class RefreshLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gamification:refresh-leaderboard';

    protected $aliases = ['app:refresh-leaderboard'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh cache leaderboard gamifikasi santri';

    /**
     * Execute the console command.
     */
    public function handle(LeaderboardService $service): int
    {
        $service->invalidateCache();
        $categories = ['overall', 'anak', 'dewasa', 'streak'];

        foreach ($categories as $cat) {
            $service->getLeaderboard($cat, 50);
        }

        $this->info('Cache leaderboard berhasil diperbarui.');

        return Command::SUCCESS;
    }
}
