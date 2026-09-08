<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\MentorProbationTracking;
use App\Services\MentorProbationService;
use Illuminate\Http\Request;

class AdminProbationController extends Controller
{
    public function __construct(
        protected MentorProbationService $probationService
    ) {}

    public function index()
    {
        $probations = MentorProbationTracking::with(['mentor.user'])->latest()->get();

        return view('admin.recruitment.probations.index', compact('probations'));
    }

    public function show($id)
    {
        $probation = MentorProbationTracking::with(['mentor.user'])->findOrFail($id);

        $this->probationService->syncTrackingStats($probation);

        $activeMentors = Mentor::with('user')
            ->where('id', '!=', $probation->mentor_id)
            ->where('is_active', true)
            ->get();

        return view('admin.recruitment.probations.show', compact('probation', 'activeMentors'));
    }

    public function syncLiveMetrics($id)
    {
        $probation = MentorProbationTracking::findOrFail($id);

        try {
            $this->probationService->syncTrackingStats($probation);

            return back()->with('success', 'Data metrik aktual LMS (presensi, rating orang tua, dan sesi) berhasil disinkronkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyinkronkan data LMS: '.$e->getMessage());
        }
    }

    public function updateScores(Request $request, $id)
    {
        $validated = $request->validate([
            'attendance_rate' => 'nullable|numeric|min:0|max:100',
            'average_rating' => 'nullable|numeric|min:1|max:5',
            'orientation_completed' => 'nullable|boolean',
            'system_training_completed' => 'nullable|boolean',
            'first_session_conducted' => 'nullable|boolean',
            'training_modules_completed' => 'nullable|integer|min:0|max:10',
            'notes' => 'nullable|string',
            // backwards compatibility for legacy scores test
            'teaching_score' => 'nullable|numeric|min:0|max:100',
            'attendance_score' => 'nullable|numeric|min:0|max:100',
            'admin_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $probation = MentorProbationTracking::findOrFail($id);

        try {
            $this->probationService->updateScores($probation, $validated);

            return back()->with('success', 'Checklist dan nilai masa percobaan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function completeProbation(Request $request, $id)
    {
        $request->validate([
            'decision' => 'nullable|in:passed,extended,terminated',
            'notes' => 'nullable|string',
            'substitute_mentor_id' => 'nullable|exists:mentors,id',
        ]);

        $probation = MentorProbationTracking::findOrFail($id);
        $decision = $request->input('decision', 'passed');
        $substituteMentorId = $request->filled('substitute_mentor_id') ? (int) $request->input('substitute_mentor_id') : null;

        try {
            $this->probationService->evaluateProbation($probation, $request->notes, $decision, $substituteMentorId);
            $msg = $decision === 'passed'
                ? 'Masa percobaan selesai. Mentor dinyatakan Lulus menjadi Guru Tetap dan memperoleh Badge M01.'
                : ($decision === 'extended' ? 'Masa percobaan mentor berhasil diperpanjang.' : 'Mentor dinyatakan tidak melanjutkan dan santri berhasil dialihkan.');

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
