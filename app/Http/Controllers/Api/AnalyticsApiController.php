<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RevenueAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsApiController extends Controller
{
    public function __construct(
        protected RevenueAnalyticsService $revenueService
    ) {}

    /**
     * Endpoint tren pendapatan 12 bulan untuk ApexCharts
     */
    public function revenueTrend(Request $request): JsonResponse
    {
        $trend = $this->revenueService->get12MonthsTrend();

        return response()->json([
            'status' => 'success',
            'data' => $trend,
        ]);
    }

    /**
     * Endpoint breakdown pendapatan per program
     */
    public function programBreakdown(Request $request): JsonResponse
    {
        $breakdown = $this->revenueService->getProgramBreakdown();

        return response()->json([
            'status' => 'success',
            'data' => $breakdown,
        ]);
    }

    /**
     * Endpoint status pembayaran
     */
    public function paymentStatus(Request $request): JsonResponse
    {
        $status = $this->revenueService->getPaymentStatusDistribution();

        return response()->json([
            'status' => 'success',
            'data' => $status,
        ]);
    }
}
