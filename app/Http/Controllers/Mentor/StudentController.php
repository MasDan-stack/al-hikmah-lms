<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Student;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $mentor = auth()->user()->mentor;

        if (! $mentor) {
            $students = collect();

            return view('mentor.students.index', compact('students'));
        }

        $students = Student::where(function ($q) use ($mentor) {
            $q->whereHas('mentors', fn ($m) => $m->where('mentors.id', $mentor->id)->where('mentor_student.is_active', true))
                ->orWhereHas('enrollments', fn ($e) => $e->where('mentor_id', $mentor->id)->where('status', EnrollmentStatus::ACTIVE->value));
        })->with(['user', 'parent.user', 'programs', 'enrollments' => fn ($e) => $e->where('mentor_id', $mentor->id)->where('status', EnrollmentStatus::ACTIVE->value)])
            ->paginate(10);

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

    public function parents(): View
    {
        $mentor = auth()->user()->mentor;

        if (! $mentor) {
            $parents = collect();

            return view('mentor.students.parents', compact('parents'));
        }

        $students = Student::where(function ($q) use ($mentor) {
            $q->whereHas('mentors', fn ($m) => $m->where('mentors.id', $mentor->id)->where('mentor_student.is_active', true))
                ->orWhereHas('enrollments', fn ($e) => $e->where('mentor_id', $mentor->id)->where('status', EnrollmentStatus::ACTIVE->value));
        })->with(['user', 'parent.user'])->get();

        $parents = $students->map(function ($student) {
            return [
                'student' => $student,
                'parent' => $student->parent,
                'parent_user' => $student->parent?->user,
            ];
        })->filter(fn ($item) => $item['parent'] !== null)->unique('parent.id');

        return view('mentor.students.parents', compact('parents'));
    }
}
