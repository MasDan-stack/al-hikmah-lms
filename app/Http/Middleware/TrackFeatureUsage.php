<?php

namespace App\Http\Middleware;

use App\Models\FeatureUsage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackFeatureUsage
{
    public function handle(Request $request, Closure $next, string $featureKey, string $featureName)
    {
        $response = $next($request);

        if ($response->isSuccessful() || $response->isRedirection()) {
            try {
                FeatureUsage::updateOrCreate(
                    ['feature_key' => $featureKey],
                    [
                        'feature_name' => $featureName,
                        'last_used_at' => now(),
                    ]
                )->increment('use_count_30d');
            } catch (\Throwable $e) {
                // Fail-safe: Jangan pernah gagalkan response HTTP pengguna jika analitik mengalami kendala
                Log::debug("Gagal mencatat feature usage {$featureKey}: ".$e->getMessage());
            }
        }

        return $response;
    }
}
