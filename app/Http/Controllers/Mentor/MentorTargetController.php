<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\HifzTarget;
use App\Models\Session;
use App\Models\Student;
use App\Services\GamificationService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorTargetController extends Controller
{
    public function __construct(
        protected GamificationService $gamificationService
    ) {}

    protected function getMentorStudents()
    {
        $mentor = auth()->user()->mentor;

        return $mentor
            ? Student::where(function ($q) use ($mentor) {
                $q->whereHas('mentors', fn ($m) => $m->where('mentors.id', $mentor->id))
                    ->orWhereHas('enrollments', fn ($e) => $e->where('mentor_id', $mentor->id)->whereIn('status', [
                        EnrollmentStatus::CONFIRMED->value,
                        EnrollmentStatus::ACTIVE->value,
                    ]));
            })->with(['user', 'parent.user', 'programs'])->get()
            : collect();
    }

    public function index(): View
    {
        $mentorId = auth()->id();
        $targets = HifzTarget::with(['student.user', 'session'])
            ->where('mentor_id', $mentorId)
            ->latest('target_date')
            ->paginate(20);

        return view('mentor.targets.index', compact('targets'));
    }

    public function create(Request $request): View
    {
        $students = $this->getMentorStudents();
        $sessions = Session::where('mentor_id', auth()->user()->mentor?->id)
            ->whereDate('date', '>=', now()->toDateString())
            ->get();

        return view('mentor.targets.create', compact('students', 'sessions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'learning_session_id' => 'nullable|exists:learning_sessions,id',
            'target_date' => 'required|date',
            'surah_name' => 'required|string|max:100',
            'start_ayat' => 'required|integer|min:1',
            'end_ayat' => 'required|integer|min:1|gte:start_ayat',
            'notes' => 'nullable|string|max:500',
            'scheduled_time' => 'nullable',
        ]);

        $validated['mentor_id'] = auth()->id();
        $validated['total_ayat'] = ($validated['end_ayat'] - $validated['start_ayat']) + 1;
        $validated['status'] = 'pending';

        $target = HifzTarget::create($validated);

        // Notifikasi ke Santri & Orang Tua
        $student = Student::with(['user', 'parent.user'])->find($validated['student_id']);
        if ($student?->parent?->user_id) {
            NotificationService::send(
                $student->parent->user_id,
                'Target Hafalan Baru untuk Ananda 🎯',
                "Ustadz/Ustadzah telah menetapkan target hafalan baru: {$target->surah_name} (Ayat {$target->start_ayat}-{$target->end_ayat}) untuk tanggal {$target->target_date->format('d/m/Y')}.",
                NotificationType::INFO,
                route('parent.dashboard'),
                'target',
                true
            );
        }

        return redirect()->route('mentor.targets.index')
            ->with('success', "Target hafalan untuk ananda {$student->getDisplayName()} berhasil ditetapkan!");
    }

    public function bulkAssign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|exists:students,id',
            'target_date' => 'required|date',
            'surah_name' => 'required|string|max:100',
            'start_ayat' => 'required|integer|min:1',
            'end_ayat' => 'required|integer|min:1|gte:start_ayat',
            'notes' => 'nullable|string|max:500',
        ]);

        $totalAyat = ($validated['end_ayat'] - $validated['start_ayat']) + 1;
        $count = 0;

        foreach ($validated['student_ids'] as $studentId) {
            HifzTarget::create([
                'student_id' => $studentId,
                'mentor_id' => auth()->id(),
                'target_date' => $validated['target_date'],
                'surah_name' => $validated['surah_name'],
                'start_ayat' => $validated['start_ayat'],
                'end_ayat' => $validated['end_ayat'],
                'total_ayat' => $totalAyat,
                'notes' => $validated['notes'],
                'status' => 'pending',
            ]);
            $count++;
        }

        return redirect()->route('mentor.targets.index')
            ->with('success', "Berhasil menetapkan target hafalan untuk {$count} santri binaan sekaligus!");
    }

    public function evaluate(Request $request, HifzTarget $target): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:completed,in_progress,missed,pending',
            'notes' => 'nullable|string|max:500',
        ]);

        $target->notes = $validated['notes'] ?? $target->notes;

        if ($validated['status'] === 'completed') {
            $this->gamificationService->completeTarget($target);
        } else {
            $target->status = $validated['status'];
            $target->save();
        }

        return back()->with('success', 'Status target hafalan berhasil diperbarui!');
    }
}
