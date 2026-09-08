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
        $programs = Program::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        $query = Question::with('program')
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('topic')) {
            $query->where('topic', 'like', '%'.$request->topic.'%');
        }

        $questions = $query->paginate(15)->withQueryString();
        $trashCount = Question::onlyTrashed()->where('user_id', Auth::id())->count();

        return view('mentor.questions.index', compact('questions', 'programs', 'trashCount'));
    }

    /**
     * Halaman Form AI Generator Soal
     */
    public function create(): View
    {
        $programs = Program::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $configuredProviders = $this->geminiService->getConfiguredProviders();
        $activeProvider = $this->geminiService->getActiveProvider();
        $activeModel = $this->geminiService->getActiveModel();

        return view('mentor.questions.generate', compact('programs', 'configuredProviders', 'activeProvider', 'activeModel'));
    }

    /**
     * AJAX Endpoint untuk Generate Preview Soal
     */
    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'topic' => 'nullable|string|max:255',
            'count' => 'required|integer|min:3|max:25',
            'difficulty' => 'required|in:Mudah,Sedang,Sulit',
            'question_type' => 'nullable|in:multiple_choice,essay,mixed',
            'ai_provider' => 'nullable|string|in:auto,gemini,qwen,deepseek,openai,claude',
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $questionType = $validated['question_type'] ?? 'multiple_choice';
        $requestedProvider = $validated['ai_provider'] ?? 'auto';

        try {
            $questions = $this->geminiService->generateQuestions(
                program: $program->name,
                topic: $validated['topic'] ?? null,
                count: (int) $validated['count'],
                difficulty: $validated['difficulty'],
                questionType: $questionType,
                requestedProvider: $requestedProvider
            );

            $activeProvider = $this->geminiService->getActiveProvider();
            $activeModel = $this->geminiService->getActiveModel();
            $isFallback = $this->geminiService->isFallbackUsed();
            $aiError = $this->geminiService->getLastError();

            $providerLabels = [
                'deepseek' => 'DeepSeek AI (Platform DeepSeek)',
                'qwen' => 'Alibaba Qwen AI (DashScope)',
                'openai' => 'OpenAI ChatGPT (GPT-4o Mini)',
                'gemini' => 'Google Gemini AI (Gemini Flash)',
                'claude' => 'Anthropic Claude (Claude 3.5 Sonnet)',
                'curated_fallback' => 'Kurikulum Terkurasi Al-Hikmah (Standar Kurikulum)',
            ];
            $providerLabel = $providerLabels[$activeProvider] ?? strtoupper($activeProvider);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menghasilkan '.count($questions).' butir soal.',
                'program_name' => $program->name,
                'active_provider' => $activeProvider,
                'provider_label' => $providerLabel,
                'active_model' => $activeModel,
                'is_fallback' => $isFallback,
                'ai_error' => $aiError,
                'data' => $questions,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 'AI_API_ERROR',
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
            'questions.*.type' => 'nullable|in:multiple_choice,essay',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'nullable|integer|min:0|max:3',
            'questions.*.essay_answer' => 'nullable|string',
            'questions.*.rubric' => 'nullable|string',
            'questions.*.explanation' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $userId = Auth::id();
            $programId = $request->program_id;
            $topic = $request->topic;
            $difficulty = $request->difficulty;

            foreach ($request->questions as $item) {
                $type = $item['type'] ?? 'multiple_choice';
                $options = null;
                $correctAnswer = null;

                if ($type === 'multiple_choice') {
                    $rawOptions = $item['options'] ?? [];
                    $options = is_array($rawOptions) ? array_values($rawOptions) : [];
                    while (count($options) < 4) {
                        $options[] = '-';
                    }
                    $options = array_slice($options, 0, 4);
                    $correctAnswer = isset($item['correct_answer']) ? (int) $item['correct_answer'] : 0;
                }

                Question::create([
                    'program_id' => $programId,
                    'user_id' => $userId,
                    'topic' => $topic,
                    'difficulty' => $difficulty,
                    'type' => $type,
                    'question' => $item['question'],
                    'options' => $options,
                    'correct_answer' => $correctAnswer,
                    'essay_answer' => $item['essay_answer'] ?? null,
                    'rubric' => $item['rubric'] ?? null,
                    'explanation' => $item['explanation'] ?? null,
                    'created_by_ai' => true,
                ]);
            }
        });

        return redirect()->route('mentor.questions.index')
            ->with('success', 'Berhasil menyimpan '.count($request->questions).' butir soal ke Bank Soal Anda!');
    }

    /**
     * Halaman Cetak Lembar Soal & Kunci Jawaban (Print PDF Sheet)
     */
    public function print(Request $request): View
    {
        $programId = $request->input('program_id');
        $topic = $request->input('topic');
        $type = $request->input('type');
        $difficulty = $request->input('difficulty');
        $selectedIds = $request->input('ids');

        $program = null;
        if ($programId) {
            $program = Program::find($programId);
        }

        // Jika dikirim langsung dari Preview Workspace (In-Memory array soal)
        if ($request->has('questions') && is_array($request->input('questions'))) {
            $rawQuestions = $request->input('questions');
            $questions = collect($rawQuestions)->unique('question')->map(function ($q) {
                $qType = $q['type'] ?? 'multiple_choice';
                $options = $q['options'] ?? ['-', '-', '-', '-'];
                if (is_array($options)) {
                    $options = array_values($options);
                }
                $corrAns = isset($q['correct_answer']) ? (int) $q['correct_answer'] : 0;

                return (object) [
                    'question' => $q['question'] ?? '',
                    'type' => $qType,
                    'options' => $options,
                    'correct_answer' => $corrAns,
                    'essay_answer' => $q['essay_answer'] ?? null,
                    'rubric' => $q['rubric'] ?? null,
                    'explanation' => $q['explanation'] ?? null,
                    'isMultipleChoice' => fn () => $qType === 'multiple_choice',
                    'isEssay' => fn () => $qType === 'essay',
                    'correct_option_label' => ['A', 'B', 'C', 'D'][$corrAns] ?? 'A',
                    'correct_option_text' => $options[$corrAns] ?? '-',
                ];
            })->values();
        } else {
            // Ambil dari database dengan filter dan proteksi anti-duplikasi
            $query = Question::with('program')->where('user_id', Auth::id());

            if (is_array($selectedIds) && count($selectedIds) > 0) {
                $query->whereIn('id', $selectedIds);
            } else {
                if ($programId) {
                    $query->where('program_id', $programId);
                }
                if ($topic) {
                    $query->where('topic', 'like', '%'.$topic.'%');
                }
                if ($type) {
                    $query->where('type', $type);
                }
                if ($difficulty) {
                    $query->where('difficulty', $difficulty);
                }
            }

            $questions = $query->orderBy('type')->orderBy('id')->get()->unique('question')->values();
        }

        return view('mentor.questions.print', [
            'questions' => $questions,
            'program' => $program,
            'topic' => $topic ?: ($questions->first()?->topic ?? 'Evaluasi Pembelajaran Al-Qur\'an'),
            'difficulty' => $difficulty ?: ($questions->first()?->difficulty ?? 'Sedang'),
            'includeKey' => $request->boolean('include_key', true),
        ]);
    }

    /**
     * Halaman Tong Sampah (Soft-deleted)
     */
    public function trash(): View
    {
        $questions = Question::onlyTrashed()
            ->with('program')
            ->where('user_id', Auth::id())
            ->latest('deleted_at')
            ->paginate(15);

        return view('mentor.questions.trash', compact('questions'));
    }

    /**
     * Restore Butir Soal dari Tong Sampah
     */
    public function restore(int $id): RedirectResponse
    {
        $question = Question::onlyTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $question->restore();

        return redirect()->route('mentor.questions.trash')
            ->with('success', 'Butir soal berhasil dipulihkan ke Bank Soal aktif.');
    }

    /**
     * Hapus Permanen Butir Soal
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $question = Question::onlyTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $question->forceDelete();

        return redirect()->route('mentor.questions.trash')
            ->with('success', 'Butir soal telah dihapus secara permanen.');
    }

    /**
     * Pindahkan Soal ke Tong Sampah (Soft Delete)
     */
    public function destroy(Question $question): RedirectResponse
    {
        if ($question->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $question->delete();

        return redirect()->route('mentor.questions.index')
            ->with('success', 'Soal berhasil dipindahkan ke Tong Sampah.');
    }
}
