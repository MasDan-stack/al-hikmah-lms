<?php

namespace App\Services\PredictiveAnalytics;

use App\Models\Payment;
use App\Models\RevenueForecast;
use Illuminate\Support\Facades\Cache;

class RevenueForecastService
{
    public const DEFAULT_MONTHLY_CHURN_RATE = 0.05; // 5% Churn Discount

    public function snapshotMonthlyForecast(): array
    {
        $today = now();
        $dateStr = $today->toDateString();

        // 1. Ambil data historis pendapatan 12 bulan terakhir
        $historyMonths = 12;
        $monthlyRevenues = [];

        for ($k = $historyMonths; $k >= 1; $k--) {
            $monthDate = $today->copy()->subMonths($k);
            $startOfMonth = $monthDate->copy()->startOfMonth();
            $endOfMonth = $monthDate->copy()->endOfMonth();

            $sum = Payment::where('status', 'paid')
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                        ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                            $q2->whereNull('payment_date')
                                ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                        });
                })
                ->sum('amount');

            $monthlyRevenues[] = (float) $sum;
        }

        // Hitung rata-rata dan OLS jika ada data
        $n = count($monthlyRevenues);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        $hasNonZero = false;
        for ($i = 0; $i < $n; $i++) {
            $x = $i + 1;
            $y = $monthlyRevenues[$i];
            if ($y > 0) {
                $hasNonZero = true;
            }
            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumX2 += ($x * $x);
        }

        $avgY = $sumY / max(1, $n);
        $avgX = $sumX / max(1, $n);

        if ($hasNonZero && ($n * $sumX2 - $sumX * $sumX) != 0) {
            $beta1 = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
            $beta0 = $avgY - ($beta1 * $avgX);
        } else {
            // Default baseline jika data historis baru
            $beta0 = $avgY > 0 ? $avgY : 15000000;
            $beta1 = 250000; // Asumsi pertumbuhan organik moderat
        }

        // Seasonal Multipliers (Ramadhan/Idul Fitri/Liburan)
        $seasonalFactors = [
            1 => 0.95,  // Januari
            2 => 1.00,  // Februari
            3 => 1.15,  // Maret (Ramadhan)
            4 => 1.20,  // April (Idul Fitri)
            5 => 1.00,  // Mei
            6 => 0.85,  // Juni (Liburan Sekolah)
            7 => 1.10,  // Juli (Tahun Ajaran Baru)
            8 => 1.00,  // Agustus
            9 => 1.00,  // September
            10 => 1.00, // Oktober
            11 => 1.00, // November
            12 => 0.90, // Desember (Liburan Akhir Tahun)
        ];

        $forecastResults = [];

        for ($j = 1; $j <= 6; $j++) {
            $forecastMonth = $today->copy()->addMonths($j);
            $monthKey = $forecastMonth->format('Y-m');
            $monthLabel = $forecastMonth->translatedFormat('F Y');
            $seasonFactor = $seasonalFactors[$forecastMonth->month] ?? 1.0;

            $trendValue = max(5000000, $beta0 + ($beta1 * ($n + $j)));
            $grossPredicted = $trendValue * $seasonFactor;
            $churnDiscount = $grossPredicted * self::DEFAULT_MONTHLY_CHURN_RATE;
            $netPredicted = round(max(0, $grossPredicted - $churnDiscount), 2);
            $confidence = max(50.0, 95.0 - ($j * 6.5));

            $forecast = RevenueForecast::updateOrCreate(
                [
                    'forecast_month' => $monthKey,
                    'forecast_date' => $dateStr,
                ],
                [
                    'forecast_month_label' => $monthLabel,
                    'predicted_amount' => $netPredicted,
                    'confidence_level' => $confidence,
                    'trend_value' => round($trendValue, 2),
                    'seasonal_factor' => $seasonFactor,
                    'estimated_churn_discount' => round($churnDiscount, 2),
                ]
            );

            $forecastResults[] = $forecast;
        }

        Cache::forget('revenue_forecasts_6m');

        return $forecastResults;
    }

    public function getForecasts(): array
    {
        return Cache::remember('revenue_forecasts_6m', 3600, function () {
            $latest = RevenueForecast::orderBy('forecast_month')
                ->where('forecast_date', function ($q) {
                    $q->selectRaw('MAX(forecast_date)')->from('revenue_forecasts');
                })
                ->get();

            if ($latest->isEmpty()) {
                return $this->snapshotMonthlyForecast();
            }

            return $latest->toArray();
        });
    }
}
