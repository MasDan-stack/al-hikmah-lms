<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HifzMilestone;
use App\Models\HifzTarget;
use App\Models\Student;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudentTargetController extends Controller
{
    public function __construct(
        protected GamificationService $gamificationService
    ) {}

    protected function getStudent(): ?Student
    {
        return auth()->user()->student ?? Student::where('user_id', auth()->id())->first();
    }

    public function store(Request $request): RedirectResponse
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(403);
        }

        $validated = $request->validate([
            'target_date' => 'required|date',
            'surah_name' => 'required|string|max:100',
            'start_ayat' => 'required|integer|min:1',
            'end_ayat' => 'required|integer|min:1|gte:start_ayat',
            'notes' => 'nullable|string|max:500',
            'scheduled_time' => 'nullable',
        ]);

        $validated['student_id'] = $student->id;
        $validated['mentor_id'] = $student->getActiveMentor()?->user_id ?: auth()->id();
        $validated['total_ayat'] = ($validated['end_ayat'] - $validated['start_ayat']) + 1;
        $validated['status'] = 'pending';

        HifzTarget::create($validated);

        return back()->with('success', 'Target hafalan berhasil ditambahkan!');
    }

    public function update(Request $request, HifzTarget $target): RedirectResponse
    {
        $student = $this->getStudent();
        if (! $student || $target->student_id !== $student->id) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
            'scheduled_time' => 'nullable',
        ]);

        $target->update($validated);

        return back()->with('success', 'Catatan target berhasil diperbarui!');
    }

    public function markComplete(HifzTarget $target): RedirectResponse
    {
        $student = $this->getStudent();
        if (! $student || $target->student_id !== $student->id) {
            abort(403);
        }

        $this->gamificationService->completeTarget($target);

        return back()->with('success', 'Alhamdulillah! Target hafalan berhasil diselesaikan. Poin bonus telah ditambahkan!');
    }

    public function storeMilestone(Request $request): RedirectResponse
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'target_type' => 'required|in:juz_completion,ayat_milestone,exam,custom',
            'target_date' => 'required|date|after:now',
            'progress_goal' => 'required|integer|min:1',
        ]);

        $validated['student_id'] = $student->id;
        $validated['mentor_id'] = $student->getActiveMentor()?->user_id;
        $validated['progress_current'] = 0;
        $validated['status'] = 'active';

        HifzMilestone::create($validated);

        return back()->with('success', 'Target jangka panjang (Milestone) berhasil dibuat!');
    }
}
