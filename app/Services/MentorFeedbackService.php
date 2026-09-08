<?php

namespace App\Services;

use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\MentorFeedbackRating;
use App\Models\MentorInterventionTicket;
use App\Models\MentorProbationTracking;
use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MentorFeedbackService
{
    /**
     * Simpan Ulasan Pasca Sesi dari Wali Santri dengan Multi-Rating & Quick Tags.
     */
    public function submitFeedback(array $data): MentorFeedback
    {
        return DB::transaction(function () use ($data) {
            $feedback = MentorFeedback::create([
                'mentor_id' => $data['mentor_id'],
                'student_id' => $data['student_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'session_id' => $data['session_id'] ?? null,
                'overall_rating' => (int) ($data['overall_rating'] ?? 5),
                'comment' => $data['comment'] ?? null,
                'quick_tags' => $data['quick_tags'] ?? [],
                'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
            ]);

            // Simpan Rincian Rating Multi-Kategori jika tersedia
            $categories = $data['categories'] ?? [
                'communication' => (int) ($data['rating_communication'] ?? $data['overall_rating'] ?? 5),
                'punctuality' => (int) ($data['rating_punctuality'] ?? $data['overall_rating'] ?? 5),
                'teaching_method' => (int) ($data['rating_teaching_method'] ?? $data['overall_rating'] ?? 5),
                'child_progress' => (int) ($data['rating_child_progress'] ?? $data['overall_rating'] ?? 5),
            ];

            foreach ($categories as $category => $score) {
                MentorFeedbackRating::create([
                    'feedback_id' => $feedback->id,
                    'category' => $category,
                    'rating' => min(5, max(1, (int) $score)),
                ]);
            }

            // Update rating mentor rata-rata
            $mentor = Mentor::find($data['mentor_id']);
            if ($mentor) {
                $avg = MentorFeedback::where('mentor_id', $mentor->id)->avg('overall_rating');
                $mentor->update(['rating' => round((float) ($avg ?? 5.0), 2)]);

                $probation = MentorProbationTracking::where('mentor_id', $mentor->id)
                    ->where('status', 'active')
                    ->first();

                if ($probation) {
                    $probation->update([
                        'average_rating' => round((float) ($avg ?? 5.0), 2),
                    ]);
                }
            }

            // Deteksi Komplain & Analisis Sentimen Ulasan
            $analysis = $this->analyzeSentimentAndDetectComplaint(
                (int) ($data['overall_rating'] ?? 5),
                $data['comment'] ?? null,
                $data['quick_tags'] ?? []
            );

            if ($analysis['is_complaint']) {
                $countToday = MentorInterventionTicket::whereDate('created_at', today())->count() + 1;
                $ticketNumber = 'TIK-'.now()->format('Ymd').'-'.str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);

                $ticket = MentorInterventionTicket::create([
                    'ticket_number' => $ticketNumber,
                    'feedback_id' => $feedback->id,
                    'mentor_id' => $data['mentor_id'],
                    'student_id' => $data['student_id'] ?? null,
                    'parent_id' => $data['parent_id'] ?? null,
                    'session_id' => $data['session_id'] ?? null,
                    'severity' => $analysis['severity'],
                    'complaint_category' => $analysis['category'],
                    'sentiment_label' => $analysis['sentiment'],
                    'parent_comment' => $data['comment'] ?? null,
                    'detected_keywords' => $analysis['keywords'],
                    'status' => 'open',
                ]);

                // Notifikasi WhatsApp ke Admin / Koordinator Pengajar
                try {
                    $adminPhone = config('services.admin.phone', env('ADMIN_PHONE'))
                        ?? User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->whereNotNull('phone')->first()?->phone;

                    if ($adminPhone) {
                        $studentName = $feedback->student?->getDisplayName() ?? 'Santri';
                        $mentorName = $mentor?->getDisplayName() ?? 'Mentor';
                        $catLabel = $ticket->getCategoryLabel();
                        $kwStr = ! empty($analysis['keywords']) ? implode(', ', $analysis['keywords']) : 'Rating Bintang Rendah (⭐ '.$feedback->overall_rating.'/5)';
                        $commentExcerpt = $feedback->comment ? Str::limit($feedback->comment, 80) : 'Tidak ada catatan tertulis.';

                        $msg = "🚨 *TIKET INTERVENSI KOMPLAIN WALI SANTRI*\n\n"
                            ."No Tiket: *#{$ticket->ticket_number}*\n"
                            ."Kategori: *{$catLabel}* (Urgensi: ".strtoupper($ticket->severity).")\n"
                            ."Santri: *{$studentName}*\n"
                            ."Mentor: *{$mentorName}*\n"
                            ."Rating Sesi: ⭐ *{$feedback->overall_rating}/5*\n"
                            ."Catatan Wali: \"{$commentExcerpt}\"\n"
                            ."Indikasi: {$kwStr}\n\n"
                            ."Harap segera ditindaklanjuti sebelum terjadi mutasi santri:\n"
                            .route('admin.tickets.show', $ticket->id);

                        app(WhatsAppService::class)->sendMessage($adminPhone, $msg);
                    }
                } catch (\Throwable $e) {
                    Log::warning('[Complaint Ticket Alert] '.$e->getMessage());
                }
            }

            // Clear Cache
            if (! empty($data['student_id'])) {
                Cache::forget("parent_feedback_pending_{$data['student_id']}");
            }
            Cache::forget("mentor_composite_score_{$data['mentor_id']}_".now()->format('Y-m'));

            return $feedback;
        });
    }

    /**
     * Analisis Sentimen & Deteksi Kata Kunci Keluhan Wali Santri
     *
     * @return array{is_complaint: bool, category: string, severity: string, keywords: list<string>, sentiment: string}
     */
    public function analyzeSentimentAndDetectComplaint(int $overallRating, ?string $comment, array $quickTags = []): array
    {
        $commentLower = strtolower($comment ?? '');
        $tagsLower = array_map('strtolower', $quickTags);
        $fullText = $commentLower.' '.implode(' ', $tagsLower);

        $detectedKeywords = [];
        $category = 'dissatisfaction';
        $severity = 'low';
        $isComplaint = false;

        $attendanceKeywords = [
            'terlambat', 'telat', 'tidak hadir', 'absen', 'tidak masuk',
            'batal mendadak', 'sering telat', 'sering terlambat', 'menghilang', 'molor',
        ];

        $attitudeKeywords = [
            'kasar', 'marah', 'membentak', 'tidak sabar', 'kurang sabar',
            'cuek', 'main hp', 'tidak fokus', 'kecewa', 'buruk', 'komplain',
        ];

        $dissatisfactionKeywords = [
            'tidak jelas', 'tidak mengajar', 'bingung', 'keberatan',
            'minta ganti', 'ganti guru', 'mutasi',
        ];

        foreach ($attendanceKeywords as $kw) {
            if (str_contains($fullText, $kw)) {
                $detectedKeywords[] = $kw;
                $category = 'attendance_late';
                $isComplaint = true;
            }
        }

        foreach ($attitudeKeywords as $kw) {
            if (str_contains($fullText, $kw)) {
                $detectedKeywords[] = $kw;
                if ($category !== 'attendance_late') {
                    $category = 'attitude_pedagogy';
                }
                $isComplaint = true;
            }
        }

        foreach ($dissatisfactionKeywords as $kw) {
            if (str_contains($fullText, $kw)) {
                $detectedKeywords[] = $kw;
                $isComplaint = true;
            }
        }

        if ($overallRating <= 2) {
            $isComplaint = true;
            $severity = ($overallRating === 1) ? 'critical' : 'high';
        } elseif ($isComplaint) {
            $severity = 'medium';
        }

        return [
            'is_complaint' => $isComplaint,
            'category' => $category,
            'severity' => $severity,
            'keywords' => array_values(array_unique($detectedKeywords)),
            'sentiment' => $isComplaint ? 'negative' : ($overallRating >= 4 ? 'positive' : 'neutral'),
        ];
    }

    /**
     * Dapatkan sesi terakhir yang perlu di-review oleh wali santri (Pending Feedback).
     */
    public function getPendingReviewSession(int $studentId): ?Session
    {
        return Cache::remember("parent_feedback_pending_{$studentId}", 60, function () use ($studentId) {
            return Session::where('student_id', $studentId)
                ->where('status', 'completed')
                ->whereDoesntHave('feedback')
                ->latest('date')
                ->first();
        });
    }

    /**
     * Tanggapan / Balasan Mentor atas Ulasan Wali.
     */
    public function respondToFeedback(int $feedbackId, int $mentorId, string $response): MentorFeedback
    {
        $feedback = MentorFeedback::where('id', $feedbackId)
            ->where('mentor_id', $mentorId)
            ->firstOrFail();

        $feedback->update([
            'mentor_response' => $response,
            'responded_at' => now(),
        ]);

        return $feedback;
    }
}
