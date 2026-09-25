<?php

namespace App\Services;

use App\Models\Mentor;
use App\Models\MentorInsight;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MentorInsightsService
{
    /**
     * Generate Wawasan & Rekomendasi Coaching AI untuk Mentor.
     * Mendukung signature fleksibel: (Mentor $mentor, array $metrics, ?string $period) atau (int $mentorId, string $period, array $metrics)
     */
    public function generateInsights(Mentor|int $mentor, mixed $second = null, mixed $third = null): MentorInsight
    {
        if (is_int($mentor)) {
            $mentor = Mentor::findOrFail($mentor);
        }

        if (is_string($second) && is_array($third)) {
            $period = $second;
            $metrics = $third;
        } elseif (is_array($second)) {
            $metrics = $second;
            $period = is_string($third) ? $third : now()->format('Y-m');
        } else {
            $metrics = [];
            $period = now()->format('Y-m');
        }

        $mentorName = $mentor->getDisplayName();

        // Ensure default metrics keys exist
        $metrics = array_merge([
            'composite_score' => 85.0,
            'retention_rate' => 95.0,
            'avg_tajwid_score' => 85.0,
            'avg_adab_score' => 90.0,
            'attendance_rate' => 95.0,
            'avg_rating_bayesian' => 4.8,
            'active_students' => 10,
        ], $metrics);

        // 1. Rate Limiting Check (Max 10 AI Insights generations per minute globally/system)
        $rateLimitKey = 'ai_insights_generation_rate_limit';
        $currentHits = (int) Cache::get($rateLimitKey, 0);

        $apiKey = config('services.gemini.api_key');
        $canCallAi = ! empty($apiKey) && $currentHits < 10;

        // Default Rule-Based Fallback Data
        $fallbackRisk = $metrics['composite_score'] < 70.0 ? 'high' : ($metrics['composite_score'] < 85.0 ? 'medium' : 'low');
        $fallbackPredicted = round(min(100.0, max(0.0, $metrics['composite_score'] + ($metrics['retention_rate'] >= 95 ? 1.0 : -1.0))), 1);

        $fallbackRecommendations = [
            'Pertahankan konsistensi bimbingan dan hubungan baik dengan santri.',
            'Tingkatkan keterlibatan aktif wali santri melalui catatan mutabaah harian.',
        ];

        if ($metrics['attendance_rate'] < 90.0) {
            $fallbackRecommendations[] = 'Disarankan mengoptimalkan ketepatan waktu jadwal bimbingan halaqah.';
        }
        if ($metrics['avg_adab_score'] < 85.0) {
            $fallbackRecommendations[] = 'Perkuat pembiasaan adab dan doa harian sebelum mengaji.';
        }

        $parsedResult = [
            'summary' => "Alhamdulillah, Ustadz/Ustadzah {$mentorName} menunjukkan performa solid dengan skor komposit {$metrics['composite_score']} dan retensi santri {$metrics['retention_rate']}%.",
            'recommendations' => $fallbackRecommendations,
            'risk_level' => $fallbackRisk,
            'predicted_score_next_month' => $fallbackPredicted,
            'model_used' => 'rule-based-heuristic',
        ];

        if ($canCallAi) {
            Cache::put($rateLimitKey, $currentHits + 1, 60);

            $prompt = "Sebagai konsultan penjaminan mutu lembaga Al-Qur'an AL-HIKMAH, berikan analisis evaluasi kinerja dan pembinaan (coaching) untuk guru bimbingan berikut:\n\n"
                ."Nama Guru: {$mentorName}\n"
                ."Spesialisasi: {$mentor->specialization}\n"
                ."Santri Aktif: {$metrics['active_students']} Santri\n"
                ."Retention Rate: {$metrics['retention_rate']}%\n"
                ."Rata-rata Nilai Tajwid: {$metrics['avg_tajwid_score']}\n"
                ."Rata-rata Nilai Adab: {$metrics['avg_adab_score']}\n"
                ."Kepatuhan Sesi/Kehadiran: {$metrics['attendance_rate']}%\n"
                ."Rating Kepuasan Wali Santri: {$metrics['avg_rating_bayesian']}/5.0\n"
                ."Skor Komposit Kinerja: {$metrics['composite_score']}/100\n\n"
                ."Keluarkan respon dalam format JSON murni TANPA markdown block:\n"
                ."{\n"
                .'  "summary": "Ringkasan performa 2-3 kalimat santun dan objektif...",'."\n"
                .'  "recommendations": ["Rekomendasi tindakan 1", "Rekomendasi tindakan 2", "Rekomendasi tindakan 3"],'."\n"
                .'  "risk_level": "low|medium|high",'."\n"
                .'  "predicted_score_next_month": 93.5'."\n"
                .'}';

            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(8)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                    ]);

                if ($response->successful()) {
                    $rawText = $response->json('candidates.0.content.parts.0.text') ?? '';
                    $cleanJson = preg_replace('/```json\s*|\s*```/', '', trim($rawText));
                    $decoded = json_decode($cleanJson, true);

                    if (is_array($decoded) && ! empty($decoded['summary'])) {
                        $parsedResult['summary'] = (string) $decoded['summary'];
                        $parsedResult['recommendations'] = is_array($decoded['recommendations'] ?? null) ? $decoded['recommendations'] : $fallbackRecommendations;
                        $parsedResult['risk_level'] = in_array($decoded['risk_level'] ?? '', ['low', 'medium', 'high'], true) ? $decoded['risk_level'] : $fallbackRisk;
                        $parsedResult['predicted_score_next_month'] = (float) ($decoded['predicted_score_next_month'] ?? $fallbackPredicted);
                        $parsedResult['model_used'] = 'gemini-2.5-flash';
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini AI Insights API fallback triggered: {$e->getMessage()}");
            }
        }

        return MentorInsight::updateOrCreate(
            [
                'mentor_id' => $mentor->id,
                'period' => $period,
            ],
            [
                'ai_summary' => $parsedResult['summary'],
                'coaching_recommendations' => $parsedResult['recommendations'],
                'risk_level' => $parsedResult['risk_level'],
                'predicted_score_next_month' => $parsedResult['predicted_score_next_month'],
                'ai_model_used' => $parsedResult['model_used'],
                'generated_at' => now(),
            ]
        );
    }
}
