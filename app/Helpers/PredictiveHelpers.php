<?php

if (! function_exists('riskLevelColor')) {
    function riskLevelColor(string $level): string
    {
        return match (strtolower($level)) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
            default => 'secondary',
        };
    }
}

if (! function_exists('riskLevelLabel')) {
    function riskLevelLabel(string $level): string
    {
        return match (strtolower($level)) {
            'critical' => 'Kritis',
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            default => 'Tidak Diketahui',
        };
    }
}
