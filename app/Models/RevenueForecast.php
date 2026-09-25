<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'forecast_date',
        'forecast_month',
        'forecast_month_label',
        'predicted_amount',
        'confidence_level',
        'trend_value',
        'seasonal_factor',
        'estimated_churn_discount',
    ];

    protected function casts(): array
    {
        return [
            'forecast_date' => 'date',
            'predicted_amount' => 'float',
            'confidence_level' => 'float',
            'trend_value' => 'float',
            'seasonal_factor' => 'float',
            'estimated_churn_discount' => 'float',
        ];
    }
}
