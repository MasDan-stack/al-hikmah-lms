<?php

namespace App\Console\Commands;

use App\Models\HifzMilestone;
use Illuminate\Console\Command;

class CleanupExpiredMilestones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-milestones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengarsipkan target jangka panjang (milestone) yang telah kadaluarsa';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $updated = HifzMilestone::where('status', 'active')
            ->where('target_date', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Berhasil mengarsipkan {$updated} milestone kadaluarsa.");

        return Command::SUCCESS;
    }
}
