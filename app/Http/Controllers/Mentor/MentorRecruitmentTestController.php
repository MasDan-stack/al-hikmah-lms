<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentorTestSession;
use App\Services\MentorTestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorRecruitmentTestController extends Controller
{
    public function __construct(
        protected MentorTestService $testService
    ) {}

    public function showTest($sessionId): View|RedirectResponse
    {
        $user = auth()->user();
        $session = MentorTestSession::with('application')->findOrFail($sessionId);

        // Validasi kepemilikan sesi tes
        if ($session->application->user_id !== $user->id && $session->application->email !== $user->email) {
            abort(403, 'Anda tidak memiliki akses ke sesi tes ini.');
        }

        if ($session->status === 'completed') {
            return redirect()->route('mentor.dashboard')->with('info', 'Anda telah menyelesaikan sesi tes ini. Nilai Anda: '.$session->score.'/100.');
        }

        $payload = $session->ai_question_payload ?? [];
        $questions = $payload['questions'] ?? [];

        return view('mentor.recruitment.take-test', compact('session', 'questions', 'payload'));
    }

    public function submitTest(Request $request, $sessionId)
    {
        $user = auth()->user();
        $session = MentorTestSession::with('application')->findOrFail($sessionId);

        // Validasi kepemilikan sesi tes
        if ($session->application->user_id !== $user->id && $session->application->email !== $user->email) {
            abort(403, 'Anda tidak memiliki akses ke sesi tes ini.');
        }

        if ($session->status === 'completed') {
            return redirect()->route('mentor.dashboard')->with('info', 'Sesi tes ini sudah dikerjakan sebelumnya.');
        }

        $answers = $request->input('answers', []);
        $result = $this->testService->submitApplicantAnswers($session, $answers);

        return redirect()->route('mentor.dashboard')->with('success', "Alhamdulillah! Jawaban tes kompetensi Anda telah berhasil dikirim. Skor Anda: {$result['score']}/100. Tim penguji akan segera meninjau hasil tes Anda.");
    }
}
