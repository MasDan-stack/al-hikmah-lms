<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function create(Request $request): View
    {
        $mentor = auth()->user()->mentor;
        $students = $mentor ? $mentor->students()->with('user')->get() : collect();

        $selectedStudentId = $request->query('student_id');
        $sessions = $mentor
            ? Session::where('mentor_id', $mentor->id)->orderBy('date', 'desc')->get()
            : collect();

        return view('mentor.progress.create', compact('students', 'sessions', 'selectedStudentId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'session_id' => 'nullable|exists:learning_sessions,id',
            'kategori' => 'required|string|max:50',
            'surah_start' => 'nullable|string|max:100',
            'surah_end' => 'nullable|string|max:100',
            'ayat_start' => 'nullable|string|max:50',
            'ayat_end' => 'nullable|string|max:50',
            'juz' => 'nullable|integer|min:1|max:30',
            'nilai_fluent' => 'nullable|integer|min:0|max:100',
            'nilai_tajwid' => 'nullable|integer|min:0|max:100',
            'nilai_adab' => 'nullable|string|max:50',
            'catatan_evaluasi' => 'nullable|string|max:1000',
            'homework' => 'nullable|string|max:500',
        ]);

        $mentor = auth()->user()->mentor;
        $validated['mentor_id'] = $mentor?->id;

        Progress::create($validated);

        if (! empty($validated['session_id'])) {
            Session::where('id', $validated['session_id'])->update(['status' => 'completed']);
        }

        return redirect()
            ->route('mentor.dashboard')
            ->with('success', 'Catatan progres hafalan/bacaan santri berhasil disimpan!');
    }
}
