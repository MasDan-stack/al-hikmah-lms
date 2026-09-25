<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActiveEnrollmentController extends Controller
{
    /**
     * Tampilkan seluruh data santri & jadwal bimbingan aktif
     */
    public function index(Request $request): View
    {
        $query = Enrollment::with([
            'student.user',
            'student.parent.user',
            'program',
            'mentor.user',
            'payment',
        ])->where('status', EnrollmentStatus::ACTIVE->value);

        // Filter pencarian nama santri atau nama wali
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('full_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
                })->orWhereHas('student.parent.user', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter Mentor
        if ($request->filled('mentor_id')) {
            $query->where('mentor_id', $request->mentor_id);
        }

        // Filter Program
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $activeEnrollments = $query->latest('paid_at')->paginate(15)->withQueryString();
        $mentors = Mentor::with('user')->where('is_active', true)->get();
        $programs = Program::where('is_active', true)->get();

        return view('admin.enrollments.active', compact('activeEnrollments', 'mentors', 'programs'));
    }
}
