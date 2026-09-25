<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\FinancialAuditLog;
use App\Models\Mentor;
use App\Models\MentorActivityLog;
use App\Models\MentorProbationTracking;
use App\Models\MentorTraining;
use App\Models\Session;
use App\Models\StudentMutationLog;
use Illuminate\Support\Facades\DB;

class MentorProbationService
{
    public function syncTrackingStats(MentorProbationTracking $probation): bool
    {
        $mentor = $probation->mentor;

        if (! $mentor) {
            return false;
        }

        // 1. Total sesi selesai mengajar
        $totalCompletedSessions = DB::table('learning_sessions')
            ->where('mentor_id', $mentor->id)
            ->where('status', 'completed')
            ->count();

        // 2. Total jadwal sesi yang telah lewat
        $totalPastSessions = DB::table('learning_sessions')
            ->where('mentor_id', $mentor->id)
            ->where('date', '<=', today())
            ->where('status', '!=', 'cancelled')
            ->count();

        // Formula Kehadiran Aktual (%)
        $attendanceRate = $totalPastSessions > 0
            ? round(($totalCompletedSessions / $totalPastSessions) * 100, 1)
            : 100.0;

        // Batasi rentang 0 - 100%
        $attendanceRate = min(100.0, max(0.0, $attendanceRate));

        // 3. Rata-rata rating wali santri dari mentor_feedback
        $avgRating = DB::table('mentor_feedback')
            ->where('mentor_id', $mentor->id)
            ->avg('overall_rating');
        $averageRating = $avgRating !== null ? round((float) $avgRating, 2) : ($probation->average_rating ?? 5.00);

        // 4. Santri aktif bimbingan
        $activeStudents = DB::table('mentor_student')
            ->where('mentor_id', $mentor->id)
            ->where('is_active', true)
            ->count();

        if ($activeStudents === 0) {
            $activeStudents = DB::table('learning_sessions')
                ->where('mentor_id', $mentor->id)
                ->where('status', 'completed')
                ->distinct('student_id')
                ->count('student_id');
        }

        // 5. Deteksi otomatis sesi perdana telah terlaksana
        $firstSessionConducted = $probation->first_session_conducted || ($totalCompletedSessions > 0);

        return $probation->update([
            'total_sessions_conducted' => $totalCompletedSessions,
            'attendance_rate' => $attendanceRate,
            'average_rating' => $averageRating,
            'active_students_assigned' => $activeStudents,
            'first_session_conducted' => $firstSessionConducted,
            'last_synced_at' => now(),
        ]);
    }

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

    public function evaluateProbation(
        MentorProbationTracking $probation,
        ?string $notes = null,
        string $decision = 'passed',
        ?int $substituteMentorId = null
    ): bool {
        return DB::transaction(function () use ($probation, $notes, $decision, $substituteMentorId) {
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

                // Hand-over Wizard: alihkan santri bimbingan aktif jika ada
                $activeStudents = $mentor->students()->wherePivot('is_active', true)->get();
                $substituteMentor = $substituteMentorId ? Mentor::find($substituteMentorId) : null;

                if ($activeStudents->isNotEmpty()) {
                    foreach ($activeStudents as $student) {
                        $pivot = $student->pivot;

                        if ($substituteMentor) {
                            StudentMutationLog::create([
                                'parent_id' => $student->parent_id,
                                'student_id' => $student->id,
                                'previous_mentor_id' => $mentor->id,
                                'new_mentor_id' => $substituteMentor->id,
                                'reason_category' => 'probation_terminated',
                                'notes' => 'Hand-over otomatis masa percobaan berakhir: '.($notes ?? '-'),
                            ]);

                            $substituteMentor->students()->syncWithoutDetaching([
                                $student->id => [
                                    'day_assigned' => $pivot->day_assigned,
                                    'time_assigned' => $pivot->time_assigned,
                                    'slot_number' => $pivot->slot_number,
                                    'time_label' => $pivot->time_label,
                                    'program_id' => $pivot->program_id,
                                    'notes' => 'Dialihkan dari masa percobaan mentor ID: '.$mentor->id,
                                    'is_active' => true,
                                ],
                            ]);

                            Enrollment::where('student_id', $student->id)
                                ->where('mentor_id', $mentor->id)
                                ->where('status', 'active')
                                ->update(['mentor_id' => $substituteMentor->id]);

                            Session::where('mentor_id', $mentor->id)
                                ->where('student_id', $student->id)
                                ->where('status', 'scheduled')
                                ->update(['mentor_id' => $substituteMentor->id]);
                        }

                        // Deactivate old pivot
                        $mentor->students()->updateExistingPivot($student->id, ['is_active' => false]);
                    }

                    if ($substituteMentor) {
                        MentorActivityLog::log(
                            mentorId: $mentor->id,
                            action: 'probation_terminated_handover',
                            description: "Santri aktif ({$activeStudents->count()} santri) dialihkan ke {$substituteMentor->getDisplayName()}."
                        );
                    }
                }
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
                newValues: [
                    'decision' => $decision,
                    'mentor_id' => $mentor->id,
                    'substitute_mentor_id' => $substituteMentorId,
                ]
            );

            return true;
        });
    }

    public function review(MentorProbationTracking $probation, array $data): bool
    {
        $decision = $data['decision'] ?? 'passed';
        $notes = $data['notes'] ?? null;
        $substituteMentorId = isset($data['substitute_mentor_id']) ? (int) $data['substitute_mentor_id'] : null;

        return $this->evaluateProbation($probation, $notes, $decision, $substituteMentorId);
    }
}
