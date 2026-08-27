<?php

namespace App\Console\Commands;

use App\Models\HifzTarget;
use Illuminate\Console\Command;

class MarkMissedTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-missed-targets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menandai target hafalan harian yang tidak disetor hingga akhir hari';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $updated = HifzTarget::where('target_date', '<', now()->toDateString())
            ->where('status', 'pending')
            ->update(['status' => 'missed']);

        $this->info("Berhasil menandai {$updated} target sebagai missed.");

        return Command::SUCCESS;
    }
}
