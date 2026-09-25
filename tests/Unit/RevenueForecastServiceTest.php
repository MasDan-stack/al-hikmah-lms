<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Services\PredictiveAnalytics\RevenueForecastService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevenueForecastServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RevenueForecastService $revenueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->revenueService = app(RevenueForecastService::class);
        Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
    }

    public function test_revenue_forecast_snapshot_generates_six_months()
    {
        $forecasts = $this->revenueService->snapshotMonthlyForecast();

        $this->assertCount(6, $forecasts);
        $this->assertDatabaseCount('revenue_forecasts', 6);
    }

    public function test_revenue_forecast_applies_seasonal_factors_and_churn_discount()
    {
        $forecasts = $this->revenueService->snapshotMonthlyForecast();

        foreach ($forecasts as $forecast) {
            $this->assertGreaterThan(0, $forecast->predicted_amount);
            $this->assertGreaterThan(0, $forecast->confidence_level);
            $this->assertGreaterThan(0, $forecast->seasonal_factor);
            $this->assertGreaterThan(0, $forecast->estimated_churn_discount);
            $this->assertNotEmpty($forecast->forecast_month_label);
        }
    }

    public function test_get_forecasts_caches_results()
    {
        $this->revenueService->snapshotMonthlyForecast();

        $cached = $this->revenueService->getForecasts();
        $this->assertIsArray($cached);
        $this->assertCount(6, $cached);
    }
}
