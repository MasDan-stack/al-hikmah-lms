<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Services\MentorFeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParentFeedbackController extends Controller
{
    public function __construct(
        protected MentorFeedbackService $feedbackService
    ) {}

    /**
     * Simpan Ulasan Bimbingan Pasca Sesi dari Wali Santri.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Auto resolve student_id from session if not directly passed
        if (! $request->filled('student_id') && $request->filled('session_id')) {
            $session = Session::find($request->input('session_id'));
            if ($session) {
                $request->merge(['student_id' => $session->student_id]);
            }
        }

        $validated = $request->validate([
            'mentor_id' => 'required|exists:mentors,id',
            'student_id' => 'nullable|exists:students,id',
            'session_id' => 'nullable|exists:learning_sessions,id',
            'overall_rating' => 'required|integer|min:1|max:5',
            'categories' => 'nullable|array',
            'rating_communication' => 'nullable|integer|min:1|max:5',
            'rating_punctuality' => 'nullable|integer|min:1|max:5',
            'rating_teaching_method' => 'nullable|integer|min:1|max:5',
            'rating_child_progress' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'quick_tags' => 'nullable|array',
            'quick_tags.*' => 'string|max:50',
            'is_anonymous' => 'nullable',
        ]);

        $validated['parent_id'] = auth()->id();
        $validated['is_anonymous'] = $request->boolean('is_anonymous');

        $feedback = $this->feedbackService->submitFeedback($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jazakumullah khairan, ulasan dan penilaian antum telah berhasil dikirimkan.',
                'feedback_id' => $feedback->id,
            ]);
        }

        return back()->with('success', 'Jazakumullah khairan, ulasan dan penilaian antum telah berhasil dikirimkan.');
    }
}
