<?php

namespace App\Services;

use App\Models\SystemHeartbeat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class HealthCheckService
{
    public function check(bool $detailed = false): array
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
            'cache' => $this->checkCache(),
            'scheduler' => $this->checkScheduler(),
        ];

        $healthy = collect($checks)->every(fn (array $c): bool => $c['status'] === 'ok' || $c['status'] === 'warning');

        $result = [
            'healthy' => $healthy,
            'status' => $healthy ? 'ok' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '11.7'),
        ];

        if ($detailed) {
            $result['checks'] = $checks;
        }

        return $result;
    }

    protected function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $start) * 1000, 2);

            $status = match (true) {
                $latency > config('alhikmah.health.db_latency_critical_ms', 500) => 'critical',
                $latency > config('alhikmah.health.db_latency_warning_ms', 100) => 'warning',
                default => 'ok',
            };

            return ['status' => $status, 'latency_ms' => $latency];
        } catch (\Throwable $e) {
            return ['status' => 'critical', 'error' => $e->getMessage()];
        }
    }

    protected function checkStorage(): array
    {
        try {
            $path = storage_path('app');
            $freeBytes = disk_free_space($path);
            $freeMb = round($freeBytes / 1024 / 1024, 2);

            $status = match (true) {
                $freeMb < config('alhikmah.health.disk_critical_threshold_mb', 100) => 'critical',
                $freeMb < config('alhikmah.health.disk_warning_threshold_mb', 500) => 'warning',
                default => 'ok',
            };

            return ['status' => $status, 'free_mb' => $freeMb];
        } catch (\Throwable $e) {
            return ['status' => 'critical', 'error' => $e->getMessage()];
        }
    }

    protected function checkQueue(): array
    {
        try {
            $pending = Queue::size();
            $failed = DB::table('failed_jobs')
                ->where('failed_at', '>=', now()->subHours(24))
                ->count();

            $status = match (true) {
                $failed > 10 => 'critical',
                $pending > 100 => 'warning',
                default => 'ok',
            };

            return [
                'status' => $status,
                'pending_jobs' => $pending,
                'failed_jobs_24h' => $failed,
            ];
        } catch (\Throwable $e) {
            return ['status' => 'critical', 'error' => $e->getMessage()];
        }
    }

    protected function checkCache(): array
    {
        try {
            $key = 'health_check_'.now()->timestamp;
            Cache::put($key, 'ok', 10);
            $value = Cache::get($key);
            Cache::forget($key);

            return [
                'status' => $value === 'ok' ? 'ok' : 'critical',
                'driver' => config('cache.default'),
            ];
        } catch (\Throwable $e) {
            return ['status' => 'critical', 'error' => $e->getMessage()];
        }
    }

    protected function checkScheduler(): array
    {
        try {
            $maxAge = config('alhikmah.health.heartbeat_max_age_hours', 25);
            $heartbeats = SystemHeartbeat::all();

            if ($heartbeats->isEmpty()) {
                return ['status' => 'warning', 'message' => 'Belum ada heartbeat cron tercatat'];
            }

            $stale = $heartbeats->filter(
                fn (SystemHeartbeat $h): bool => $h->ran_at->diffInHours(now()) > $maxAge
            );

            return [
                'status' => $stale->isEmpty() ? 'ok' : 'critical',
                'stale_jobs' => $stale->pluck('job_name')->toArray(),
            ];
        } catch (\Throwable $e) {
            return ['status' => 'critical', 'error' => $e->getMessage()];
        }
    }
}
