<?php

namespace App\Services;

use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\MentorFeedbackRating;
use App\Models\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
