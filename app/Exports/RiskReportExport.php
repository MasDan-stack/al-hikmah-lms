<?php

namespace App\Exports;

use App\Models\StudentDropoutPrediction;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RiskReportExport
{
    public function download(string $filename, string $format = 'csv'): StreamedResponse
    {
        $predictions = StudentDropoutPrediction::with('student.enrollments.program')
            ->whereDate('prediction_date', today())
            ->orderByDesc('risk_score')
            ->get();

        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function () use ($predictions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID Santri',
                'Nama',
                'Program',
                'Tingkat Risiko',
                'Skor (0-100)',
                'Faktor Risiko',
                'Kehadiran (%)',
                'Status Intervensi WA',
            ]);

            foreach ($predictions as $p) {
                $programName = $p->student?->enrollments?->first()?->program?->name ?? '-';

                fputcsv($file, [
                    $p->student_id,
                    $p->student?->full_name,
                    $programName,
                    strtoupper($p->risk_level),
                    $p->risk_score,
                    implode(', ', (array) $p->risk_factors),
                    $p->attendance_score,
                    $p->is_alerted ? 'Sudah Diingatkan' : 'Belum',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
