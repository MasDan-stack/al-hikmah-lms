<?php

namespace App\Observers;

use App\Models\Progress;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Log;

class ProgressObserver
{
    /**
     * Handle the Progress "created" event.
     */
    public function created(Progress $progress): void
    {
        try {
            app(GamificationService::class)->processProgress($progress);
        } catch (\Throwable $e) {
            Log::error('[ProgressObserver Error] '.$e->getMessage());
        }
    }
}
