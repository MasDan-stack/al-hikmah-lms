<?php

namespace App\Http\Controllers;

use App\Services\HealthCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    public function __invoke(Request $request, HealthCheckService $service): JsonResponse
    {
        $token = $request->header('X-Health-Key');
        $expectedToken = config('alhikmah.health_key');
        $isAuthorized = $token && $expectedToken && hash_equals($expectedToken, $token);

        $health = $service->check($isAuthorized);
        $status = $health['healthy'] ? 200 : 503;

        return response()->json($health, $status);
    }
}
