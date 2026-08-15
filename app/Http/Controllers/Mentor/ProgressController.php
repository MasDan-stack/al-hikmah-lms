<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\MentorActivityLog;
use App\Models\Progress;
use App\Models\Session;
use App\Models\Student;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function create(Request $request): View
    {
        $mentor = auth()->user()->mentor;
        $students = $mentor
            ? Student::where(function ($q) use ($mentor) {
                $q->whereHas('mentors', fn ($m) => $m->where('mentors.id', $mentor->id))
                    ->orWhereHas('enrollments', fn ($e) => $e->where('mentor_id', $mentor->id)->whereIn('status', [
                        EnrollmentStatus::CONFIRMED->value,
                        EnrollmentStatus::ACTIVE->value,
                    ]));
            })->with(['user', 'parent.user', 'programs'])->get()
            : collect();

        $selectedStudentId = $request->query('student_id');
        $selectedSessionId = $request->query('session_id');

        $sessions = $mentor
            ? Session::where('mentor_id', $mentor->id)->orderBy('date', 'desc')->get()
            : collect();

        return view('mentor.progress.create', compact('students', 'sessions', 'selectedStudentId', 'selectedSessionId'));
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
            'nilai_adab' => 'nullable|integer|min:0|max:100',
            'catatan_evaluasi' => 'nullable|string|max:1000',
            'homework' => 'nullable|string|max:500',
        ]);

        $mentor = auth()->user()->mentor;
        $validated['mentor_id'] = $mentor?->id;

        $progress = Progress::create($validated);

        if (! empty($validated['session_id'])) {
            Session::where('id', $validated['session_id'])->update(['status' => 'completed']);
        }

        // Notifikasi ke Orang Tua Santri via NotificationService
        $student = Student::with('parent.user')->find($validated['student_id']);
        if ($student?->parent?->user_id) {
            NotificationService::send(
                $student->parent->user_id,
                'Laporan Progres Belajar Santri',
                "Pendamping {$mentor?->getDisplayName()} telah menambahkan catatan progres {$validated['kategori']} untuk ananda {$student->getDisplayName()}.",
                NotificationType::SUCCESS,
                route('parent.dashboard'),
                'progress',
                true
            );
        }

        MentorActivityLog::log(
            $mentor?->id,
            'catat_progres',
            'Mencatat progres santri ID #'.$validated['student_id'].' ('.$validated['kategori'].')'
        );

        return redirect()
            ->route('mentor.dashboard')
            ->with('success', 'Catatan progres hafalan/bacaan santri berhasil disimpan!');
    }

    public function createBulk(): View
    {
        $mentor = auth()->user()->mentor;
        $students = $mentor
            ? Student::where(function ($q) use ($mentor) {
                $q->whereHas('mentors', fn ($m) => $m->where('mentors.id', $mentor->id))
                    ->orWhereHas('enrollments', fn ($e) => $e->where('mentor_id', $mentor->id)->whereIn('status', [
                        EnrollmentStatus::CONFIRMED->value,
                        EnrollmentStatus::ACTIVE->value,
                    ]));
            })->with(['user', 'parent.user', 'programs'])->get()
            : collect();
        $sessions = $mentor ? Session::where('mentor_id', $mentor->id)->orderBy('date', 'desc')->get() : collect();

        return view('mentor.progress.bulk', compact('students', 'sessions'));
    }

    public function storeBulk(Request $request): RedirectResponse
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.student_id' => 'required|exists:students,id',
            'entries.*.session_id' => 'nullable|exists:learning_sessions,id',
            'entries.*.kategori' => 'required|string|max:50',
            'entries.*.surah_start' => 'nullable|string|max:100',
            'entries.*.surah_end' => 'nullable|string|max:100',
            'entries.*.ayat_start' => 'nullable|string|max:50',
            'entries.*.ayat_end' => 'nullable|string|max:50',
            'entries.*.juz' => 'nullable|integer|min:1|max:30',
            'entries.*.nilai_fluent' => 'nullable|integer|min:0|max:100',
            'entries.*.nilai_tajwid' => 'nullable|integer|min:0|max:100',
            'entries.*.nilai_adab' => 'nullable|integer|min:0|max:100',
            'entries.*.catatan_evaluasi' => 'nullable|string|max:1000',
            'entries.*.homework' => 'nullable|string|max:500',
        ]);

        $mentor = auth()->user()->mentor;
        $mentorId = $mentor?->id;
        $count = 0;

        DB::transaction(function () use ($request, $mentorId, &$count) {
            foreach ($request->entries as $entry) {
                $entry['mentor_id'] = $mentorId;
                Progress::create($entry);
                $count++;

                if (! empty($entry['session_id'])) {
                    Session::where('id', $entry['session_id'])->update(['status' => 'completed']);
                }
            }

            MentorActivityLog::log(
                $mentorId,
                'bulk_progres',
                "Mencatat progres massal untuk {$count} santri"
            );
        });

        return redirect()
            ->route('mentor.dashboard')
            ->with('success', "Berhasil menyimpan {$count} catatan progres massal!");
    }
}
