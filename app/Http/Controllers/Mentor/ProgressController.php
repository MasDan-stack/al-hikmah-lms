<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Mentor;
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
        $selectedStudentId = $request->query('student_id');
        $selectedSessionId = $request->query('session_id');

        if ($mentor) {
            $students = Student::where(function ($q) use ($mentor) {
                $q->whereHas('mentors', fn ($m) => $m->where('mentors.id', $mentor->id))
                    ->orWhereHas('enrollments', fn ($e) => $e->where('mentor_id', $mentor->id)->whereIn('status', [
                        EnrollmentStatus::CONFIRMED->value,
                        EnrollmentStatus::ACTIVE->value,
                    ]))
                    ->orWhereHas('sessions', fn ($s) => $s->where('mentor_id', $mentor->id));
            })->with(['user', 'parent.user', 'programs'])->get();

            $sessions = Session::where('mentor_id', $mentor->id)
                ->with(['student.user'])
                ->orderBy('date', 'desc')
                ->get();
        } else {
            // Jika diakses oleh Admin / Super-Admin
            $students = Student::with(['user', 'parent.user', 'programs'])->get();
            $sessions = Session::with(['student.user'])
                ->orderBy('date', 'desc')
                ->take(30)
                ->get();
        }

        // Pastikan santri dari query parameter (?student_id=X) selalu tersedia dan terpilih
        if ($selectedStudentId && ! $students->contains('id', (int) $selectedStudentId)) {
            $extraStudent = Student::with(['user', 'parent.user', 'programs'])->find($selectedStudentId);
            if ($extraStudent) {
                $students->push($extraStudent);
            }
        }

        // Jika ada santri terpilih, prioritaskan sesi santri tersebut
        if ($selectedStudentId) {
            $studentSessions = Session::where('student_id', $selectedStudentId)
                ->with(['student.user'])
                ->orderBy('date', 'desc')
                ->get();
            $sessions = $sessions->merge($studentSessions)->unique('id')->values();
        }

        return view('mentor.progress.create', compact('students', 'sessions', 'selectedStudentId', 'selectedSessionId'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->has('nilai_adab')) {
            $request->merge([
                'nilai_adab' => $this->sanitizeAdabValue($request->input('nilai_adab')),
            ]);
        }

        if ($request->filled('nilai_kelancaran') && ! $request->has('nilai_fluent')) {
            $request->merge(['nilai_fluent' => (int) $request->input('nilai_kelancaran')]);
        }

        if ($request->has('is_mutqin') && ! $request->has('is_mutqin_test')) {
            $request->merge(['is_mutqin_test' => $request->boolean('is_mutqin')]);
        }

        if ($request->filled('catatan') && ! $request->has('catatan_evaluasi')) {
            $request->merge(['catatan_evaluasi' => $request->input('catatan')]);
        }

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
            'is_mutqin_test' => 'nullable|boolean',
            'juz_number' => 'nullable|integer|min:1|max:30',
            'catatan_evaluasi' => 'nullable|string|max:1000',
            'homework' => 'nullable|string|max:500',
        ], [
            'student_id.required' => 'Silakan pilih santri terlebih dahulu.',
            'student_id.exists' => 'Santri yang dipilih tidak valid.',
            'kategori.required' => 'Kategori bimbingan/setoran wajib dipilih.',
            'juz.integer' => 'Nomor Juz harus berupa angka bulat antara 1 sampai 30.',
            'juz.min' => 'Nomor Juz minimal 1.',
            'juz.max' => 'Nomor Juz maksimal 30.',
            'nilai_fluent.integer' => 'Nilai kelancaran harus berupa angka bulat 0 - 100.',
            'nilai_tajwid.integer' => 'Nilai tajwid harus berupa angka bulat 0 - 100.',
            'nilai_adab.integer' => 'Nilai adab harus berupa angka bulat 0 - 100.',
        ]);

        $mentor = auth()->user()->mentor;
        $mentorId = $mentor?->id;

        // Jika user bukan mentor (misal Admin/Super Admin testing form), tentukan mentor dari santri atau mentor default
        if (! $mentorId) {
            $student = Student::with(['mentors', 'enrollments'])->find($validated['student_id']);
            $mentorId = $student?->mentors()->first()?->id
                ?? $student?->enrollments()->whereNotNull('mentor_id')->latest()->value('mentor_id')
                ?? Mentor::first()?->id;
        }

        // Sanitasi tipe data numerik integer agar aman ke MySQL
        $validated['mentor_id'] = $mentorId;
        $validated['surah_end'] = ! empty($validated['surah_end']) ? $validated['surah_end'] : ($validated['surah_start'] ?? null);
        $validated['ayat_start'] = (! empty($validated['ayat_start']) && is_numeric($validated['ayat_start'])) ? (int) $validated['ayat_start'] : null;
        $validated['ayat_end'] = (! empty($validated['ayat_end']) && is_numeric($validated['ayat_end'])) ? (int) $validated['ayat_end'] : null;
        $validated['juz'] = (! empty($validated['juz']) && is_numeric($validated['juz'])) ? (int) $validated['juz'] : null;
        $validated['nilai_fluent'] = $validated['nilai_fluent'] ?? 85;
        $validated['nilai_tajwid'] = $validated['nilai_tajwid'] ?? 85;
        $validated['nilai_adab'] = $validated['nilai_adab'] ?? 85;
        $validated['is_mutqin_test'] = $request->boolean('is_mutqin_test');

        if ($validated['is_mutqin_test'] && ! empty($validated['juz'])) {
            $validated['juz_number'] = (int) $validated['juz'];
        }

        DB::transaction(function () use ($validated) {
            Progress::create($validated);

            if (! empty($validated['session_id'])) {
                Session::where('id', $validated['session_id'])->update(['status' => 'completed']);
            }
        });

        // Notifikasi ke Orang Tua Santri via NotificationService
        $student = Student::with('parent.user')->find($validated['student_id']);
        if ($student?->parent?->user_id) {
            $mentorName = $mentor?->getDisplayName() ?? 'Guru Pembimbing';
            NotificationService::send(
                $student->parent->user_id,
                'Laporan Progres Belajar Santri',
                "Pendamping {$mentorName} telah menambahkan catatan progres {$validated['kategori']} untuk ananda {$student->getDisplayName()}.",
                NotificationType::SUCCESS,
                route('parent.dashboard'),
                'progress',
                true
            );
        }

        if ($mentorId) {
            MentorActivityLog::log(
                $mentorId,
                'catat_progres',
                'Mencatat progres santri ID #'.$validated['student_id'].' ('.$validated['kategori'].')'
            );
        }

        return redirect()
            ->route('mentor.progress.create', ['student_id' => $validated['student_id']])
            ->with('success', 'Catatan progres hafalan/bacaan santri berhasil disimpan ke database!');
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
        if ($request->has('entries') && is_array($request->entries)) {
            $entries = $request->entries;
            foreach ($entries as $index => $entry) {
                if (isset($entry['nilai_adab'])) {
                    $entries[$index]['nilai_adab'] = $this->sanitizeAdabValue($entry['nilai_adab']);
                }
            }
            $request->merge(['entries' => $entries]);
        }

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

    /**
     * Mengubah nilai adab teks (misal: 'Sangat Baik', 'Baik') menjadi angka bulat (0-100)
     */
    protected function sanitizeAdabValue(mixed $value): ?int
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $lower = strtolower(trim((string) $value));

        return match (true) {
            str_contains($lower, 'sangat baik') || str_contains($lower, 'mumtaz') || str_contains($lower, 'istimewa') => 95,
            str_contains($lower, 'baik sekali') || str_contains($lower, 'jayyid jiddan') => 90,
            str_contains($lower, 'baik') || str_contains($lower, 'jayyid') => 80,
            str_contains($lower, 'cukup') || str_contains($lower, 'maqbul') => 70,
            str_contains($lower, 'kurang') || str_contains($lower, 'bimbingan') => 60,
            default => 80,
        };
    }
}
