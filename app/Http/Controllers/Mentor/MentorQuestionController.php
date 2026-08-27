<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Question;
use App\Services\GeminiQuestionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MentorQuestionController extends Controller
{
    public function __construct(
        protected GeminiQuestionService $geminiService
    ) {}

    /**
     * Halaman Bank Soal Mentor
     */
    public function index(Request $request): View
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();

        $query = Question::with('program')
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('topic')) {
            $query->where('topic', 'like', '%'.$request->topic.'%');
        }

        $questions = $query->paginate(15);
        $trashCount = Question::onlyTrashed()->where('user_id', Auth::id())->count();

        return view('mentor.questions.index', compact('questions', 'programs', 'trashCount'));
    }

    /**
     * Halaman Form AI Generator Soal
     */
    public function create(): View
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();

        return view('mentor.questions.generate', compact('programs'));
    }

    /**
     * AJAX Endpoint untuk Generate Preview Soal
     */
    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'topic' => 'required|string|min:3|max:255',
            'count' => 'required|integer|min:5|max:20',
            'difficulty' => 'required|in:Mudah,Sedang,Sulit',
        ]);

        $program = Program::findOrFail($validated['program_id']);

        try {
            $questions = $this->geminiService->generateQuestions(
                program: $program->name,
                topic: $validated['topic'],
                count: (int) $validated['count'],
                difficulty: $validated['difficulty']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menghasilkan '.count($questions).' butir soal.',
                'data' => $questions,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 'GEMINI_API_ERROR',
            ], 500);
        }
    }

    /**
     * Simpan Kumpulan Soal yang Telah Direview ke Database
     */
    public function storeBatch(Request $request): RedirectResponse
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'topic' => 'required|string|max:255',
            'difficulty' => 'required|in:Mudah,Sedang,Sulit',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*' => 'required|string',
            'questions.*.correct_answer' => 'required|integer|min:0|max:3',
            'questions.*.explanation' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $userId = Auth::id();
            $programId = $request->program_id;
            $topic = $request->topic;
            $difficulty = $request->difficulty;

            foreach ($request->questions as $item) {
                Question::create([
                    'program_id' => $programId,
                    'user_id' => $userId,
                    'topic' => $topic,
                    'difficulty' => $difficulty,
                    'question' => $item['question'],
                    'options' => $item['options'],
                    'correct_answer' => (int) $item['correct_answer'],
                    'explanation' => $item['explanation'] ?? null,
                    'created_by_ai' => true,
                ]);
            }
        });

        return redirect()->route('mentor.questions.index')
            ->with('success', 'Alhamdulillah! '.count($request->questions).' butir soal berhasil disimpan ke Bank Soal.');
    }

    /**
     * Soft Delete Soal ke Tong Sampah
     */
    public function destroy(Question $question): RedirectResponse
    {
        if ($question->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus soal ini.');
        }

        $question->delete();

        return redirect()->route('mentor.questions.index')
            ->with('success', 'Butir soal berhasil dipindahkan ke Tong Sampah.');
    }

    /**
     * Halaman Tong Sampah Bank Soal
     */
    public function trash(): View
    {
        $trashedQuestions = Question::onlyTrashed()
            ->with('program')
            ->where('user_id', Auth::id())
            ->latest('deleted_at')
            ->paginate(15);

        return view('mentor.questions.trash', compact('trashedQuestions'));
    }

    /**
     * Pulihkan Soal dari Tong Sampah
     */
    public function restore(int $id): RedirectResponse
    {
        $question = Question::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $question->restore();

        return redirect()->route('mentor.questions.trash')
            ->with('success', 'Butir soal berhasil dipulihkan ke Bank Soal.');
    }

    /**
     * Hapus Permanen Soal
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $question = Question::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $question->forceDelete();

        return redirect()->route('mentor.questions.trash')
            ->with('success', 'Butir soal berhasil dihapus secara permanen.');
    }
}
