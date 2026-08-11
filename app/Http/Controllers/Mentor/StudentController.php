<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Student;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $mentor = auth()->user()->mentor;
        $students = $mentor ? $mentor->students()->with(['user'])->paginate(10) : collect();

        return view('mentor.students.index', compact('students'));
    }

    public function show(int $id): View
    {
        $mentor = auth()->user()->mentor;
        $student = Student::with(['user', 'parent.user'])->findOrFail($id);

        $progresses = Progress::where('student_id', $id)
            ->where('mentor_id', $mentor?->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mentor.students.show', compact('student', 'progresses'));
    }
}
