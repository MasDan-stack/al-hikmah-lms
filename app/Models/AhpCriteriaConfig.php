<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpCriteriaConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'criteria_key',
        'criteria_name',
        'description',
        'weight',
        'pairwise_values',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pairwise_values' => 'array',
            'is_active' => 'boolean',
            'weight' => 'float',
        ];
    }
}
