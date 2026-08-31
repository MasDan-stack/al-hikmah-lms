<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Mentors\AssignStudentAction;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignStudentRequest;
use App\Models\Mentor;
use App\Models\MentorActivityLog;
use App\Models\MentorAvailability;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MentorAvailabilityController extends Controller
{
    /**
     * Tampilan Matriks Ketersediaan 7 Hari - Single Aggregated Query (0 N+1).
     */
    public function index(Request $request): View
    {
        // 1. Eager load mentor & ketersediaannya
        $mentors = Mentor::with(['user', 'availabilities'])
            ->where('is_active', true)
            ->get();

        // 2. Ambil rekap total santri aktif dalam 1 QUERY TUNGGAL (GROUP BY)
        $activeCounts = DB::table('mentor_student')
            ->select('mentor_id', 'day_assigned', DB::raw('count(*) as total_students'))
            ->where('is_active', true)
            ->groupBy('mentor_id', 'day_assigned')
            ->get()
            ->groupBy('mentor_id');

        $unassignedStudents = Student::whereDoesntHave('mentors', function ($q) {
            $q->where('mentor_student.is_active', true);
        })->whereDoesntHave('enrollments', function ($q) {
            $q->whereIn('status', [
                EnrollmentStatus::CONFIRMED->value,
                EnrollmentStatus::ACTIVE->value,
            ])->whereNotNull('mentor_id');
        })->get();

        $days = MentorAvailability::DAYS_ORDER;
        $dayLabels = MentorAvailability::DAYS;

        $availabilityData = [];
        foreach ($mentors as $mentor) {
            $row = [];
            $mentorCounts = $activeCounts->get($mentor->id)?->keyBy('day_assigned');

            foreach ($days as $day) {
                $availability = $mentor->availabilities->firstWhere('day', $day);
                $studentCount = $mentorCounts?->get($day)?->total_students ?? 0;

                $maxStudents = $availability?->max_students ?? $mentor->default_max_students_per_day ?? 5;
                $isAvailable = $availability ? $availability->isAvailable() : true;

                $row[$day] = [
                    'availability' => $availability,
                    'student_count' => $studentCount,
                    'max_students' => $maxStudents,
                    'is_available' => $isAvailable,
                    'has_quota' => $isAvailable && ($studentCount < $maxStudents),
                ];
            }

            $availabilityData[$mentor->id] = [
                'mentor' => $mentor,
                'schedule' => $row,
            ];
        }

        return view('admin.mentors.availability', compact(
            'availabilityData',
            'unassignedStudents',
            'days',
            'dayLabels'
        ));
    }

    /**
     * Delegasikan alokasi santri ke Action Service terproteksi locking & Form Request.
     */
    public function assignStudent(AssignStudentRequest $request, AssignStudentAction $action): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $action->execute(
                (int) $validated['mentor_id'],
                (int) $validated['student_id'],
                $validated['day']
            );

            return redirect()->route('admin.mentors.availability')
                ->with('success', 'Santri berhasil dialokasikan dengan aman ke mentor.');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }

    /**
     * Pelepasan santri dari mentor.
     */
    public function unassignStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'mentor_id' => 'required|exists:mentors,id',
        ]);

        DB::table('mentor_student')
            ->where('mentor_id', $validated['mentor_id'])
            ->where('student_id', $validated['student_id'])
            ->where('is_active', true)
            ->update(['is_active' => false, 'updated_at' => now()]);

        $student = Student::find($validated['student_id']);
        MentorActivityLog::log(
            (int) $validated['mentor_id'],
            'Pelepasan Santri',
            "Santri {$student?->getDisplayName()} dilepaskan dari mentor."
        );

        return back()->with('success', 'Santri berhasil dilepaskan dari mentor.');
    }

    /**
     * API JSON filter mentor tersedia per hari - Dioptimasi Bebas N+1 Query.
     */
    public function getAvailableMentors(Request $request): JsonResponse
    {
        $day = $request->query('day');
        if (! $day || ! in_array($day, MentorAvailability::DAYS_ORDER, true)) {
            return response()->json(['error' => 'Hari tidak valid'], 400);
        }

        // 1. Ambil seluruh mentor aktif beserta ketersediaan di hari terkait (1 query)
        $mentors = Mentor::with(['user', 'availabilities' => function ($q) use ($day) {
            $q->where('day', $day);
        }])->where('is_active', true)->get();

        // 2. Hitung jumlah santri aktif untuk hari tersebut dalam 1 query
        $studentCounts = DB::table('mentor_student')
            ->select('mentor_id', DB::raw('count(*) as total'))
            ->where('day_assigned', $day)
            ->where('is_active', true)
            ->groupBy('mentor_id')
            ->pluck('total', 'mentor_id');

        // 3. Filter kuota secara in-memory (0 query DB tambahan)
        $availableMentors = $mentors->filter(function ($mentor) use ($studentCounts) {
            $availability = $mentor->availabilities->first();
            $isAvailable = $availability ? $availability->isAvailable() : true;

            if (! $isAvailable) {
                return false;
            }

            $maxStudents = $availability?->max_students ?? $mentor->default_max_students_per_day ?? 5;
            $currentCount = $studentCounts[$mentor->id] ?? 0;

            return $currentCount < $maxStudents;
        })->values();

        return response()->json([
            'day' => $day,
            'mentors' => $availableMentors->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->getDisplayName(),
                'specialization' => $m->specialization,
                'student_count' => $studentCounts[$m->id] ?? 0,
                'max_students' => $m->availabilities->first()?->max_students ?? $m->default_max_students_per_day ?? 5,
            ]),
        ]);
    }
}
