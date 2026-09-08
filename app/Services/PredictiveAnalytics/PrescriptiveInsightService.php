<?php

namespace App\Services\PredictiveAnalytics;

use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrescriptiveInsightService
{
    protected array $fallbackActions = [
        'critical' => [
            'Hubungi wali santri via WhatsApp segera untuk evaluasi kendala belajar ananda.',
            'Koordinasikan jadwal bimbingan alternatif bersama guru pembimbing.',
            'Tinjau ulang beban target hafalan agar ananda tidak mengalami kejenuhan.',
            'Berikan motivasi khusus dan apresiasi pencapaian yang pernah diraih.',
        ],
        'high' => [
            'Kirimkan pengingat ramah mengenai jadwal sesi bimbingan berikutnya.',
            'Berikan perhatian ekstra pada murojaah bacaan yang belum lancar.',
            'Tanyakan kendala waktu atau perangkat yang dihadapi orang tua.',
        ],
        'medium' => [
            'Pantau konsistensi kehadiran dan setoran dalam 7 hari ke depan.',
            'Berikan semangat dan motivasi belajar pada setiap akhir sesi.',
        ],
        'low' => [
            'Pertahankan ritme bimbingan yang sudah sangat baik.',
            'Berikan apresiasi dan dorongan untuk melampaui target bulanan.',
        ],
    ];

    public function generateInsights(Student $student, array $riskData): array
    {
        $level = $riskData['risk_level'] ?? 'low';

        // 1. Coba panggil Gemini 2.5 Flash API jika API key tersedia
        try {
            $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
            if ($apiKey) {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
                $response = Http::timeout(5)->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $this->buildPrompt($student, $riskData)],
                            ],
                        ],
                    ],
                ]);

                if ($response->successful()) {
                    return $this->parseGeminiResponse($response->json());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API timeout/error for prescriptive insights, fallback to heuristics: '.$e->getMessage());
        }

        // 2. Fallback aman ke Heuristic Rule Engine
        return [
            'actions' => $this->buildHeuristicActions($level, $riskData['risk_factors'] ?? []),
            'source' => 'heuristic_fallback',
        ];
    }

    private function buildPrompt(Student $student, array $riskData): string
    {
        $factors = implode(', ', $riskData['risk_factors'] ?? ['Umum']);

        return "Buat 3-4 butir rekomendasi tindakan preskriptif singkat (1 kalimat per butir) dalam Bahasa Indonesia untuk guru & admin AL-HIKMAH LMS dalam mendampingi santri {$student->full_name} dengan level risiko {$riskData['risk_level']} (skor: {$riskData['risk_score']}%). Faktor pemicu: {$factors}. Tuliskan langsung butir-butir tindakan tanpa pengantar.";
    }

    private function parseGeminiResponse(array $response): array
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $lines = explode("\n", $text);
        $actions = [];

        foreach ($lines as $line) {
            $cleaned = trim(preg_replace('/^[\d\.\-\*\•\s]+/', '', trim($line)));
            if (! empty($cleaned) && strlen($cleaned) > 5) {
                $actions[] = $cleaned;
            }
        }

        if (empty($actions)) {
            $actions = $this->fallbackActions['medium'];
        }

        return [
            'actions' => array_slice($actions, 0, 4),
            'source' => 'gemini_ai',
        ];
    }

    private function buildHeuristicActions(string $level, array $factors): array
    {
        $base = $this->fallbackActions[$level] ?? $this->fallbackActions['low'];
        $custom = [];

        foreach ($factors as $factor) {
            $factorLower = strtolower($factor);
            if (str_contains($factorLower, 'presensi')) {
                $custom[] = 'Tawarkan opsi penyesuaian waktu bimbingan (reschedule) yang fleksibel.';
            }
            if (str_contains($factorLower, 'pembayaran')) {
                $custom[] = 'Kirimkan notifikasi tagihan dengan pilihan metode pembayaran via portal wali.';
            }
            if (str_contains($factorLower, 'progres')) {
                $custom[] = 'Gunakan metode murojaah bertahap yang lebih ringan untuk memulihkan kepercayaan diri santri.';
            }
        }

        return ! empty($custom) ? array_values(array_unique(array_merge($custom, $base))) : $base;
    }
}
