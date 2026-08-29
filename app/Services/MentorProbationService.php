<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\FinancialAuditLog;
use App\Models\MentorProbationTracking;
use App\Models\MentorTraining;
use Illuminate\Support\Facades\DB;

class MentorProbationService
{
    public function updateScores(MentorProbationTracking $probation, array $data): bool
    {
        return DB::transaction(function () use ($probation, $data) {
            $updateData = [];

            if (isset($data['average_rating'])) {
                $updateData['average_rating'] = $data['average_rating'];
            } elseif (isset($data['teaching_score'])) {
                $updateData['average_rating'] = round($data['teaching_score'] / 20, 2); // normalize 0-100 to 0-5
            }

            if (isset($data['attendance_rate'])) {
                $updateData['attendance_rate'] = $data['attendance_rate'];
            } elseif (isset($data['attendance_score'])) {
                $updateData['attendance_rate'] = $data['attendance_score'];
            }

            if (isset($data['orientation_completed'])) {
                $updateData['orientation_completed'] = (bool) $data['orientation_completed'];
            }
            if (isset($data['system_training_completed'])) {
                $updateData['system_training_completed'] = (bool) $data['system_training_completed'];
            }
            if (isset($data['first_session_conducted'])) {
                $updateData['first_session_conducted'] = (bool) $data['first_session_conducted'];
            }
            if (isset($data['training_modules_completed'])) {
                $updateData['training_modules_completed'] = (int) $data['training_modules_completed'];
            }

            if (isset($data['notes']) || isset($data['admin_notes'])) {
                $updateData['mid_review_notes'] = $data['notes'] ?? $data['admin_notes'];
                $updateData['mid_review_date'] = now();
            }

            $probation->update($updateData);

            FinancialAuditLog::log(
                userId: auth()->id(),
                action: 'mentor_probation_scores_updated',
                entityType: 'mentor_probation_tracking',
                entityId: $probation->id,
                oldValues: null,
                newValues: $updateData
            );

            return true;
        });
    }

    public function evaluateProbation(MentorProbationTracking $probation, ?string $notes = null, string $decision = 'passed'): bool
    {
        return DB::transaction(function () use ($probation, $notes, $decision) {
            $probation->update([
                'final_decision' => $decision,
                'final_notes' => $notes,
                'final_evaluation_date' => now(),
                'evaluated_by' => auth()->id(),
                'status' => $decision === 'passed' ? 'passed' : ($decision === 'extended' ? 'extended' : 'terminated'),
            ]);

            $mentor = $probation->mentor;

            if ($decision === 'passed') {
                $mentor->update([
                    'status' => 'active',
                    'is_active' => true,
                ]);

                // Berikan Badge M01 - Mentor Certified
                $badge = Badge::where('code', 'M01')->first();
                if ($badge) {
                    MentorTraining::firstOrCreate([
                        'mentor_id' => $mentor->id,
                        'badge_id' => $badge->id,
                    ], [
                        'title' => 'Sertifikasi Kelulusan Masa Percobaan Mentor',
                        'category' => 'pedagogy',
                        'training_date' => today(),
                        'duration_hours' => 12.0,
                        'notes' => 'Lulus evaluasi 90 hari masa percobaan dengan predikat Baik.',
                    ]);
                }
            } elseif ($decision === 'terminated') {
                $mentor->update([
                    'status' => 'inactive',
                    'is_active' => false,
                ]);
            } elseif ($decision === 'extended') {
                $mentor->update([
                    'status' => 'probation',
                    'probation_end_date' => today()->addMonths(1),
                ]);
            }

            FinancialAuditLog::log(
                userId: auth()->id(),
                action: 'mentor_probation_evaluated',
                entityType: 'mentor_probation_tracking',
                entityId: $probation->id,
                oldValues: null,
                newValues: ['decision' => $decision, 'mentor_id' => $mentor->id]
            );

            return true;
        });
    }

    public function review(MentorProbationTracking $probation, array $data): bool
    {
        $decision = $data['decision'] ?? 'passed';
        $notes = $data['notes'] ?? null;

        return $this->evaluateProbation($probation, $notes, $decision);
    }
}
