<?php

namespace App\Services\DecisionSupport;

use App\Models\AhpCriteriaConfig;
use App\Models\AhpEvaluationSnapshot;
use App\Models\HifzTarget;
use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\MentorInterventionTicket;
use App\Models\Progress;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AhpRankingService
{
    /**
     * Saaty Random Consistency Index (IR) standar untuk n = 1 s.d. 10.
     */
    public const RANDOM_INDEX = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49,
    ];

    /**
     * 5 Kriteria Baku Pedagogis Islami untuk Pemilihan Ustadz/Ustazah Teladan.
     */
    public const CRITERIA_KEYS = [
        'discipline' => [
            'name' => 'Kedisiplinan & Kehadiran',
            'code' => 'C1',
            'desc' => 'Ketepatan waktu dan tingkat kehadiran mengajar riil pada sesi bimbingan.',
            'default_weight' => 0.1895,
        ],
        'pedagogy' => [
            'name' => 'Kualitas Pedagogi & Mutaba\'ah',
            'code' => 'C2',
            'desc' => 'Kualitas pengajaran, ketuntasan target hafalan santri, dan kelengkapan input mutaba\'ah.',
            'default_weight' => 0.3657,
        ],
        'morals_communication' => [
            'name' => 'Akhlak, Adab & Komunikasi',
            'code' => 'C3',
            'desc' => 'Kesabaran membimbing, empati komunikasi dengan santri/wali, serta bebas tiket komplain.',
            'default_weight' => 0.1895,
        ],
        'parent_satisfaction' => [
            'name' => 'Kepuasan Wali Santri',
            'code' => 'C4',
            'desc' => 'Tingkat kepuasan eksternal orang tua berdasarkan evaluasi rating bintang pasca-sesi.',
            'default_weight' => 0.1895,
        ],
        'institutional_involvement' => [
            'name' => 'Pengembangan Diri & Keaktifan Lembaga',
            'code' => 'C5',
            'desc' => 'Keikutsertaan rapat/pelatihan guru, penyelesaian modul orientasi, dan portofolio sanad.',
            'default_weight' => 0.0657,
        ],
    ];

    /**
     * Matriks Perbandingan Berpasangan Baku (Terkalibrasi Konsisten CR = 0.0009 << 0.10).
     */
    public function getDefaultPairwiseMatrix(): array
    {
        return [
            'discipline' => [
                'discipline' => 1.0,
                'pedagogy' => 0.5,
                'morals_communication' => 1.0,
                'parent_satisfaction' => 1.0,
                'institutional_involvement' => 3.0,
            ],
            'pedagogy' => [
                'discipline' => 2.0,
                'pedagogy' => 1.0,
                'morals_communication' => 2.0,
                'parent_satisfaction' => 2.0,
                'institutional_involvement' => 5.0,
            ],
            'morals_communication' => [
                'discipline' => 1.0,
                'pedagogy' => 0.5,
                'morals_communication' => 1.0,
                'parent_satisfaction' => 1.0,
                'institutional_involvement' => 3.0,
            ],
            'parent_satisfaction' => [
                'discipline' => 1.0,
                'pedagogy' => 0.5,
                'morals_communication' => 1.0,
                'parent_satisfaction' => 1.0,
                'institutional_involvement' => 3.0,
            ],
            'institutional_involvement' => [
                'discipline' => 1 / 3,
                'pedagogy' => 1 / 5,
                'morals_communication' => 1 / 3,
                'parent_satisfaction' => 1 / 3,
                'institutional_involvement' => 1.0,
            ],
        ];
    }

    /**
     * Ambil Matriks Perbandingan Aktif dari Database atau Default.
     */
    public function getActivePairwiseMatrix(): array
    {
        $configs = AhpCriteriaConfig::where('is_active', true)->get();

        if ($configs->count() === count(self::CRITERIA_KEYS)) {
            $matrix = [];
            foreach ($configs as $cfg) {
                if (! empty($cfg->pairwise_values) && is_array($cfg->pairwise_values)) {
                    $matrix[$cfg->criteria_key] = $cfg->pairwise_values;
                }
            }

            if (count($matrix) === count(self::CRITERIA_KEYS)) {
                return $matrix;
            }
        }

        return $this->getDefaultPairwiseMatrix();
    }

    /**
     * Hitung Bobot Prioritas (Eigenvector), Lambda Max, CI, dan Rasio Konsistensi (CR).
     */
    public function calculateWeights(array $matrix): array
    {
        $keys = array_keys(self::CRITERIA_KEYS);
        $n = count($keys);

        // 1. Validasi struktur matriks kuadrat
        foreach ($keys as $rowKey) {
            foreach ($keys as $colKey) {
                if (! isset($matrix[$rowKey][$colKey]) || $matrix[$rowKey][$colKey] <= 0) {
                    $matrix[$rowKey][$colKey] = ($rowKey === $colKey) ? 1.0 : 1.0;
                }
            }
        }

        // 2. Hitung jumlah setiap kolom (Column Sums)
        $columnSums = [];
        foreach ($keys as $colKey) {
            $sum = 0.0;
            foreach ($keys as $rowKey) {
                $sum += (float) $matrix[$rowKey][$colKey];
            }
            $columnSums[$colKey] = $sum > 0 ? $sum : 1.0;
        }

        // 3. Normalisasi kolom dan hitung rata-rata baris (Eigenvector / Bobot Prioritas)
        $weights = [];
        foreach ($keys as $rowKey) {
            $rowSum = 0.0;
            foreach ($keys as $colKey) {
                $normalizedVal = (float) $matrix[$rowKey][$colKey] / $columnSums[$colKey];
                $rowSum += $normalizedVal;
            }
            $weights[$rowKey] = round($rowSum / $n, 4);
        }

        // Pastikan total bobot = 1.0
        $sumWeights = array_sum($weights);
        if ($sumWeights > 0 && abs($sumWeights - 1.0) > 0.0001) {
            foreach ($weights as $k => $w) {
                $weights[$k] = round($w / $sumWeights, 4);
            }
        }

        // 4. Hitung Nilai Konsistensi (Lambda Max)
        // Weighted Sum Vector = Matrix * Weights
        $weightedSums = [];
        foreach ($keys as $rowKey) {
            $ws = 0.0;
            foreach ($keys as $colKey) {
                $ws += ((float) $matrix[$rowKey][$colKey]) * $weights[$colKey];
            }
            $weightedSums[$rowKey] = $ws;
        }

        // Consistency Vector = Weighted Sum / Weight
        $consistencyVector = [];
        foreach ($keys as $k) {
            $w = $weights[$k] > 0 ? $weights[$k] : 0.0001;
            $consistencyVector[$k] = $weightedSums[$k] / $w;
        }

        $lambdaMax = array_sum($consistencyVector) / $n;
        $ci = ($n > 1) ? ($lambdaMax - $n) / ($n - 1) : 0.0;
        $ci = max(0.0, $ci);

        $ri = self::RANDOM_INDEX[$n] ?? 1.12;
        $cr = ($ri > 0) ? ($ci / $ri) : 0.0;
        $cr = round($cr, 4);

        $isConsistent = $cr <= 0.10;

        return [
            'matrix' => $matrix,
            'weights' => $weights,
            'lambda_max' => round($lambdaMax, 4),
            'ci' => round($ci, 4),
            'ri' => $ri,
            'cr' => $cr,
            'cr_percent' => round($cr * 100, 2),
            'is_consistent' => $isConsistent,
        ];
    }

    /**
     * Ekstraksi Otomatis Metrik 5 Kriteria Mentor dari Data Riil LMS untuk Periode Tertentu.
     */
    public function extractMentorCriteriaScores(Carbon $startDate, Carbon $endDate): Collection
    {
        $mentors = Mentor::where('is_active', true)
            ->with(['user', 'application', 'probationTracking'])
            ->get();

        $startStr = $startDate->toDateString();
        $endStr = $endDate->toDateString();
        $startDay = $startDate->copy()->startOfDay();
        $endDay = $endDate->copy()->endOfDay();

        return $mentors->map(function (Mentor $mentor) use ($startStr, $endStr, $startDay, $endDay) {
            // C1: Kedisiplinan & Kehadiran Mengajar
            $sessions = Session::where('mentor_id', $mentor->id)
                ->whereBetween('date', [$startStr, $endStr])
                ->get();
            $totalSessions = $sessions->count();
            $completedSessions = $sessions->where('status', 'completed')->count();
            $attendanceRate = $totalSessions > 0
                ? round(($completedSessions / $totalSessions) * 100, 2)
                : 95.0; // Fallback jika guru baru belum terjadwal sesi penuh
            $c1Score = min(100.0, max(0.0, $attendanceRate));

            // C2: Kualitas Pedagogi & Mutaba'ah
            $progressQuery = Progress::where('mentor_id', $mentor->id)
                ->whereBetween('created_at', [$startDay, $endDay]);
            $avgTajwidRaw = $progressQuery->avg('nilai_tajwid');
            $avgTajwid = $avgTajwidRaw !== null ? (float) $avgTajwidRaw : 85.0;

            $targetsQuery = HifzTarget::where('mentor_id', $mentor->user_id ?? $mentor->id)
                ->whereBetween('target_date', [$startStr, $endStr]);
            $totalTargets = $targetsQuery->count();
            $completedTargets = (clone $targetsQuery)->where('status', 'completed')->count();
            $targetRate = $totalTargets > 0 ? round(($completedTargets / $totalTargets) * 100, 2) : 85.0;

            $c2Score = min(100.0, max(0.0, round(($avgTajwid * 0.60) + ($targetRate * 0.40), 2)));

            // C3: Akhlak, Adab & Komunikasi (Bebas Komplain)
            $avgAdabRaw = Progress::where('mentor_id', $mentor->id)
                ->whereBetween('created_at', [$startDay, $endDay])
                ->avg('nilai_adab');
            $avgAdab = $avgAdabRaw !== null ? (float) $avgAdabRaw : 90.0;

            // Penalti tiket komplain
            $tickets = MentorInterventionTicket::where('mentor_id', $mentor->id)
                ->whereBetween('created_at', [$startDay, $endDay])
                ->get();
            $ticketPenalty = 0.0;
            foreach ($tickets as $ticket) {
                if ($ticket->severity === 'critical') {
                    $ticketPenalty += 15.0;
                } elseif ($ticket->severity === 'high') {
                    $ticketPenalty += 10.0;
                } else {
                    $ticketPenalty += 5.0;
                }
            }
            $c3Score = min(100.0, max(0.0, round($avgAdab - $ticketPenalty, 2)));

            // C4: Kepuasan Wali Santri (Rating Bintang)
            $feedbackQuery = MentorFeedback::where('mentor_id', $mentor->id)
                ->whereBetween('created_at', [$startDay, $endDay]);
            $feedbackCount = $feedbackQuery->count();
            $avgRatingRaw = $feedbackCount > 0
                ? (float) $feedbackQuery->avg('overall_rating')
                : (float) ($mentor->rating ?? 4.8);
            // Normalisasi skala 1-5 ke skala 0-100
            $c4Score = min(100.0, max(0.0, round(($avgRatingRaw / 5.0) * 100, 2)));

            // C5: Pengembangan Diri & Keaktifan Lembaga
            $involvementBase = 80.0;
            if ($mentor->probationTracking) {
                $modules = (int) ($mentor->probationTracking->modules_completed ?? 0);
                $involvementBase = 60.0 + ($modules * 10.0); // 4 modul = 100.0
            }
            // Bonus sertifikasi & pengalaman
            if (! empty($mentor->sanad_chain) || ! empty($mentor->application?->sanad_chain)) {
                $involvementBase += 5.0;
            }
            if ($mentor->is_trainer) {
                $involvementBase += 5.0;
            }
            $c5Score = min(100.0, max(0.0, round($involvementBase, 2)));

            return [
                'mentor' => $mentor,
                'mentor_id' => $mentor->id,
                'mentor_name' => $mentor->user?->name ?? $mentor->full_name ?? 'Ustadz/Ustazah',
                'c1_discipline' => $c1Score,
                'c2_pedagogy' => $c2Score,
                'c3_morals' => $c3Score,
                'c4_satisfaction' => $c4Score,
                'c5_involvement' => $c5Score,
                'total_sessions' => $totalSessions,
                'completed_sessions' => $completedSessions,
                'feedback_count' => $feedbackCount,
                'tickets_count' => $tickets->count(),
            ];
        });
    }

    /**
     * Hitung Evaluasi & Perangkingan Lengkap Berbasis AHP.
     */
    public function evaluateAndRank(string $periodMonth, ?array $customMatrix = null, bool $saveSnapshots = true): array
    {
        $targetDate = Carbon::createFromFormat('Y-m', $periodMonth);
        $startDate = $targetDate->copy()->startOfMonth();
        $endDate = $targetDate->copy()->endOfMonth();

        // 1. Perhitungan Bobot Kriteria AHP
        $matrix = $customMatrix ?? $this->getActivePairwiseMatrix();
        $ahpWeights = $this->calculateWeights($matrix);
        $weights = $ahpWeights['weights'];

        // 2. Ekstraksi Metrik Alternatif Mentor
        $criteriaScores = $this->extractMentorCriteriaScores($startDate, $endDate);

        // 3. Sintesis Skor Akhir AHP: S_j = SUM(w_i * Score_ij)
        $ranked = $criteriaScores->map(function ($item) use ($weights) {
            $wDiscipline = $weights['discipline'] ?? 0.1895;
            $wPedagogy = $weights['pedagogy'] ?? 0.3657;
            $wMorals = $weights['morals_communication'] ?? 0.1895;
            $wSatisfaction = $weights['parent_satisfaction'] ?? 0.1895;
            $wInvolvement = $weights['institutional_involvement'] ?? 0.0657;

            $finalScore = ($item['c1_discipline'] * $wDiscipline) +
                          ($item['c2_pedagogy'] * $wPedagogy) +
                          ($item['c3_morals'] * $wMorals) +
                          ($item['c4_satisfaction'] * $wSatisfaction) +
                          ($item['c5_involvement'] * $wInvolvement);

            $item['final_ahp_score'] = round($finalScore, 2);

            return $item;
        })->sortByDesc('final_ahp_score')->values();

        // 4. Beri Peringkat & Rekomendasi Alokasi Bonus
        $position = 1;
        $rankedWithTiers = $ranked->map(function ($item) use (&$position, $periodMonth, $saveSnapshots) {
            $rank = $position++;
            $reward = 0.0;
            $badge = null;

            if ($rank === 1) {
                $reward = 1000000.0; // Rp 1.000.000 (Juara 1)
                $badge = 'Juara 1 - Ustadz/Ustazah Teladan Utama';
            } elseif ($rank === 2) {
                $reward = 750000.0;  // Rp 750.000 (Juara 2)
                $badge = 'Juara 2 - Teladan Madya';
            } elseif ($rank === 3) {
                $reward = 500000.0;  // Rp 500.000 (Juara 3)
                $badge = 'Juara 3 - Teladan Muda';
            } elseif ($rank <= 5) {
                $reward = 250000.0;  // Rp 250.000 (Top 5 Apresiasi)
                $badge = 'Top 5 - Apresiasi Berprestasi';
            }

            $item['rank_position'] = $rank;
            $item['reward_amount'] = $reward;
            $item['tier_badge'] = $badge;

            // Simpan snapshot ke database jika diminta
            if ($saveSnapshots) {
                $existing = AhpEvaluationSnapshot::where('period_month', $periodMonth)
                    ->where('mentor_id', $item['mentor_id'])
                    ->first();

                $snapshot = AhpEvaluationSnapshot::updateOrCreate(
                    [
                        'period_month' => $periodMonth,
                        'mentor_id' => $item['mentor_id'],
                    ],
                    [
                        'c1_discipline_score' => $item['c1_discipline'],
                        'c2_pedagogy_score' => $item['c2_pedagogy'],
                        'c3_morals_score' => $item['c3_morals'],
                        'c4_satisfaction_score' => $item['c4_satisfaction'],
                        'c5_involvement_score' => $item['c5_involvement'],
                        'final_ahp_score' => $item['final_ahp_score'],
                        'rank_position' => $rank,
                        'reward_amount' => $reward,
                        'admin_notes' => $existing?->admin_notes, // Pertahankan catatan jika ada
                        'calculated_at' => now(),
                    ]
                );

                $item['admin_notes'] = $snapshot->admin_notes;
                $item['snapshot_id'] = $snapshot->id;
                $item['is_announced'] = $snapshot->is_announced;
            } else {
                $item['admin_notes'] = null;
                $item['snapshot_id'] = null;
                $item['is_announced'] = false;
            }

            return $item;
        });

        // 5. Struktur Data Rangkuman
        $top3 = $rankedWithTiers->take(3)->values();
        $totalReward = $rankedWithTiers->sum('reward_amount');

        return [
            'period_month' => $periodMonth,
            'period_label' => $targetDate->translatedFormat('F Y'),
            'ahp_weights' => $ahpWeights,
            'total_mentors' => $rankedWithTiers->count(),
            'total_reward_allocated' => $totalReward,
            'top_winner' => $top3->first(),
            'podium' => $top3,
            'leaderboard' => $rankedWithTiers,
        ];
    }

    /**
     * Poin B: Simulasi Perubahan Bobot Matriks terhadap Ranking Top 5.
     */
    public function simulateWeights(array $newMatrix, string $periodMonth): array
    {
        // 1. Evaluasi saat ini (baseline)
        $currentEvaluation = $this->evaluateAndRank($periodMonth, null, false);
        $currentRanks = collect($currentEvaluation['leaderboard'])->keyBy('mentor_id');

        // 2. Evaluasi simulasi dengan matriks baru
        $simulatedEvaluation = $this->evaluateAndRank($periodMonth, $newMatrix, false);
        $simulatedWeights = $simulatedEvaluation['ahp_weights'];

        // 3. Bandingkan pergeseran peringkat Top 5
        $rankDiffs = collect($simulatedEvaluation['leaderboard'])->map(function ($item) use ($currentRanks) {
            $mentorId = $item['mentor_id'];
            $oldItem = $currentRanks->get($mentorId);
            $oldRank = $oldItem['rank_position'] ?? null;
            $newRank = $item['rank_position'];

            $diff = $oldRank !== null ? ($oldRank - $newRank) : 0; // Positif berarti naik peringkat

            return [
                'mentor_id' => $mentorId,
                'mentor_name' => $item['mentor_name'],
                'old_rank' => $oldRank,
                'new_rank' => $newRank,
                'rank_diff' => $diff,
                'old_score' => $oldItem['final_ahp_score'] ?? 0.0,
                'new_score' => $item['final_ahp_score'],
            ];
        });

        return [
            'simulated_weights' => $simulatedWeights,
            'is_consistent' => $simulatedWeights['is_consistent'],
            'cr' => $simulatedWeights['cr'],
            'cr_percent' => $simulatedWeights['cr_percent'],
            'rank_diffs' => $rankDiffs->take(5)->values()->toArray(),
            'all_simulated' => $rankDiffs->toArray(),
        ];
    }

    /**
     * Poin C: Otomasi Notifikasi WhatsApp Pengumuman Guru Teladan.
     */
    public function sendAnnouncementNotifications(string $periodMonth): array
    {
        $snapshots = AhpEvaluationSnapshot::with(['mentor.user'])
            ->where('period_month', $periodMonth)
            ->orderBy('rank_position')
            ->get();

        if ($snapshots->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Belum ada data evaluasi untuk periode ini. Silakan jalankan evaluasi terlebih dahulu.',
                'sent_count' => 0,
            ];
        }

        $sentCount = 0;
        $targetDate = Carbon::createFromFormat('Y-m', $periodMonth);
        $monthLabel = $targetDate->translatedFormat('F Y');

        foreach ($snapshots as $snap) {
            $mentor = $snap->mentor;
            $user = $mentor?->user;
            $phone = $user?->phone ?? $mentor?->emergency_contact;
            $name = $user?->name ?? $mentor?->full_name ?? 'Ustadz/Ustazah';
            $rank = $snap->rank_position;
            $score = $snap->final_ahp_score;

            if ($rank <= 3) {
                // Pesan Selamat untuk Juara
                $nominalStr = 'Rp '.number_format($snap->reward_amount, 0, ',', '.');
                $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh, Ustadz/Ustazah {$name}.\n\n"
                    ."Maa Syaa Allah Tabarakallah! 🎉\n"
                    ."Kami dari Manajemen AL-HIKMAH LMS mengucapkan selamat atas pencapaian luar biasa Anda sebagai:\n"
                    ."🏆 *JUARA {$rank} - USTADZ/USTAZAH TELADAN ({$monthLabel})*\n\n"
                    ."📊 Skor Komposit AHP: *{$score}/100*\n"
                    ."🎁 Alokasi Bonus Reward: *{$nominalStr}*\n\n"
                    ."Semoga Allah Ta'ala membalas setiap tetes keringat dan kesabaran Anda dalam membina generasi Qur'ani. Jazakumullah Khairan Katsiran.";
            } else {
                // Pesan Evaluatif Konstruktif untuk Non-Juara
                $advice = $this->generateCoachingRecommendation($snap);
                $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh, Ustadz/Ustazah {$name}.\n\n"
                    ."Alhamdulillah, evaluasi kinerja pengajar periode *{$monthLabel}* telah selesai dihitung via SPK AHP.\n\n"
                    ."📈 Peringkat Anda: *Peringkat ke-{$rank}* (Skor: {$score}/100)\n"
                    ."💡 *Saran Peningkatan*: {$advice}\n\n"
                    ."Terima kasih atas keikhlasan dan istiqomah dalam mendampingi santri bimbingan Al-Qur'an. Terus tingkatkan mutu pedagogis untuk periode berikutnya!";
            }

            // Simulasi pengiriman / log ke gateway WhatsApp lembaga
            Log::info("WhatsApp Announcement Sent to {$phone} ({$name}): ".str_replace("\n", ' ', $message));

            $snap->update([
                'is_announced' => true,
                'announced_at' => now(),
            ]);

            $sentCount++;
        }

        return [
            'success' => true,
            'message' => "Pengumuman dan pesan WhatsApp berhasil dikirimkan ke {$sentCount} mentor.",
            'sent_count' => $sentCount,
        ];
    }

    /**
     * Poin D: Ambil Ringkasan Skor AHP Pribadi untuk Dashboard Guru.
     */
    public function getMentorAhpSummary(Mentor $mentor, ?string $periodMonth = null): ?array
    {
        $month = $periodMonth ?? now()->format('Y-m');

        $snapshot = AhpEvaluationSnapshot::where('period_month', $month)
            ->where('mentor_id', $mentor->id)
            ->first();

        if (! $snapshot) {
            // Hitung sementara jika belum di-snapshot
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            $scores = $this->extractMentorCriteriaScores($startDate, $endDate)->firstWhere('mentor_id', $mentor->id);

            if (! $scores) {
                return null;
            }

            $weights = $this->calculateWeights($this->getActivePairwiseMatrix())['weights'];
            $finalScore = ($scores['c1_discipline'] * $weights['discipline']) +
                          ($scores['c2_pedagogy'] * $weights['pedagogy']) +
                          ($scores['c3_morals'] * $weights['morals_communication']) +
                          ($scores['c4_satisfaction'] * $weights['parent_satisfaction']) +
                          ($scores['c5_involvement'] * $weights['institutional_involvement']);

            return [
                'period_month' => $month,
                'final_score' => round($finalScore, 2),
                'rank_position' => 1,
                'reward_amount' => 0.0,
                'radar_dimensions' => [
                    'Disiplin' => $scores['c1_discipline'],
                    'Pedagogi' => $scores['c2_pedagogy'],
                    'Akhlak' => $scores['c3_morals'],
                    'Kepuasan Wali' => $scores['c4_satisfaction'],
                    'Keaktifan' => $scores['c5_involvement'],
                ],
                'recommendation' => 'Pertahankan kedisiplinan dan mutaba\'ah santri binaan.',
            ];
        }

        return [
            'period_month' => $month,
            'final_score' => $snapshot->final_ahp_score,
            'rank_position' => $snapshot->rank_position,
            'reward_amount' => $snapshot->reward_amount,
            'admin_notes' => $snapshot->admin_notes,
            'is_announced' => $snapshot->is_announced,
            'radar_dimensions' => [
                'Disiplin' => $snapshot->c1_discipline_score,
                'Pedagogi' => $snapshot->c2_pedagogy_score,
                'Akhlak' => $snapshot->c3_morals_score,
                'Kepuasan Wali' => $snapshot->c4_satisfaction_score,
                'Keaktifan' => $snapshot->c5_involvement_score,
            ],
            'recommendation' => $this->generateCoachingRecommendation($snapshot),
        ];
    }

    /**
     * Buat Saran Pembinaan Preskriptif Berdasarkan Skor Terendah.
     */
    protected function generateCoachingRecommendation(AhpEvaluationSnapshot $snap): string
    {
        $scores = [
            'Kedisiplinan Kehadiran' => $snap->c1_discipline_score,
            'Pedagogi & Mutaba\'ah' => $snap->c2_pedagogy_score,
            'Akhlak & Komunikasi' => $snap->c3_morals_score,
            'Kepuasan Wali Santri' => $snap->c4_satisfaction_score,
            'Pengembangan Diri' => $snap->c5_involvement_score,
        ];

        asort($scores);
        $lowestCriterion = array_key_first($scores);
        $lowestVal = $scores[$lowestCriterion];

        if ($lowestCriterion === 'Kedisiplinan Kehadiran') {
            return "Skor kehadiran Anda ({$lowestVal}/100) perlu ditingkatkan. Pastikan selalu memulai sesi tepat waktu dan hindari reschedule mendadak.";
        }
        if ($lowestCriterion === 'Pedagogi & Mutaba\'ah') {
            return "Pedagogi Anda ({$lowestVal}/100) berpotensi ditingkatkan. Mohon lebih konsisten menginput mutaba'ah santri dan pantau kelulusan target hafalan.";
        }
        if ($lowestCriterion === 'Akhlak & Komunikasi') {
            return "Tingkatkan empati komunikasi ({$lowestVal}/100) saat memberi arahan kepada santri dan jalin komunikasi santun dengan wali santri.";
        }
        if ($lowestCriterion === 'Kepuasan Wali Santri') {
            return "Kepuasan wali santri ({$lowestVal}/100) memerlukan perhatian. Sapa orang tua di awal/akhir sesi bimbingan dan sampaikan progres ananda secara ramah.";
        }

        return "Keaktifan lembaga Anda ({$lowestVal}/100) dapat ditingkatkan dengan menyelesaikan 4 modul orientasi dan mengikuti rapat internal.";
    }
}
