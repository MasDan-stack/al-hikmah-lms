<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignStudentRequest;
use App\Models\Mentor;
use App\Models\MentorActivityLog;
use App\Models\MentorAvailability;
use App\Models\Program;
use App\Models\Student;
use App\Services\MentorAvailabilityService;
use App\Services\MentorMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MentorAvailabilityController extends Controller
{
    public function __construct(
        protected MentorAvailabilityService $availabilityService
    ) {}

    /**
     * Tampilan Matriks Ketersediaan 7 Hari Berbasis Angka Slot (0-6).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $filterDay = $request->query('day');
        $filterSlot = $request->query('slot');
        $filterProgramId = $request->query('program_id');

        $mentors = $this->availabilityService->getAllAvailabilities([
            'search' => $search,
            'day' => $filterDay,
            'slot' => $filterSlot,
        ]);

        // Ambil data santri aktif teralokasi per [mentor_id][day_assigned][slot_number]
        $assignedRows = DB::table('mentor_student')
            ->join('students', 'students.id', '=', 'mentor_student.student_id')
            ->leftJoin('programs', 'programs.id', '=', 'mentor_student.program_id')
            ->select(
                'mentor_student.mentor_id',
                'mentor_student.day_assigned',
                'mentor_student.slot_number',
                'mentor_student.time_label',
                'students.id as student_id',
                'students.full_name as student_name',
                'programs.name as program_name'
            )
            ->where('mentor_student.is_active', true)
            ->get()
            ->groupBy(['mentor_id', 'day_assigned']);

        $unassignedStudents = $this->availabilityService->getUnassignedStudents();

        $days = MentorAvailability::DAYS_ORDER;
        $dayLabels = MentorAvailability::DAYS;
        $slotMap = MentorAvailability::SLOT_MAP;
        $programs = Program::where('is_active', true)->get();

        $allSlotNumbers = [0, 1, 2, 3, 4, 5, 6];

        // Bangun struktur matriks dan deteksi guru yang belum mengisi jadwal
        $matrix = [];
        $unfilledMentors = collect();

        foreach ($mentors as $mentor) {
            $schedule = [];
            $totalMentorActiveSlots = 0;

            foreach ($days as $dayKey) {
                $avail = $mentor->availabilities->firstWhere('day', $dayKey);
                $slotNumbers = $avail?->slot_numbers ?? [];
                $totalMentorActiveSlots += count($slotNumbers);
                $maxQuota = 1; // Sistem bimbingan privat 1-on-1: 1 slot = 1 santri
                $dayAssigned = $assignedRows->get($mentor->id)?->get($dayKey) ?? collect();

                // Slot yang diisi/dibuka mengajar
                $slotsData = [];
                foreach ($slotNumbers as $slotNum) {
                    if (isset($slotMap[$slotNum])) {
                        $studentsInSlot = $dayAssigned->where('slot_number', $slotNum);
                        $count = $studentsInSlot->count();
                        $isFull = $count >= 1;
                        $isAlmostFull = false;

                        $slotsData[$slotNum] = [
                            'slot' => $slotNum,
                            'time' => $slotMap[$slotNum]['time'],
                            'badge' => $slotMap[$slotNum]['badge'],
                            'count' => $count,
                            'max' => 1,
                            'is_full' => $isFull,
                            'is_almost_full' => $isAlmostFull,
                            'students' => $studentsInSlot->values(),
                        ];
                    }
                }

                // Sisa slot yang KOSONG / TIDAK DIBUKA MENGAJAR di hari tersebut
                $emptySlotNumbers = array_values(array_diff($allSlotNumbers, $slotNumbers));
                $emptySlotsData = [];
                foreach ($emptySlotNumbers as $eNum) {
                    if (isset($slotMap[$eNum])) {
                        $emptySlotsData[$eNum] = $slotMap[$eNum];
                    }
                }

                // Tentukan status ketersediaan di hari ini
                $statusType = 'filled';
                if ($avail === null) {
                    $statusType = 'unfilled'; // Belum pernah diatur sama sekali
                } elseif (empty($slotsData)) {
                    $statusType = 'empty'; // Dikosongkan / Libur semua
                }

                $schedule[$dayKey] = [
                    'availability' => $avail,
                    'status_type' => $statusType,
                    'is_available' => ! empty($slotsData),
                    'slots' => $slotsData,
                    'empty_slots' => $emptySlotsData,
                    'empty_slot_numbers' => $emptySlotNumbers,
                    'total_students' => $dayAssigned->count(),
                ];
            }

            if ($totalMentorActiveSlots === 0) {
                $unfilledMentors->push($mentor);
            }

            $matrix[$mentor->id] = [
                'mentor' => $mentor,
                'schedule' => $schedule,
                'total_active_slots' => $totalMentorActiveSlots,
            ];
        }

        return view('admin.mentors.availability', compact(
            'matrix',
            'unfilledMentors',
            'unassignedStudents',
            'days',
            'dayLabels',
            'slotMap',
            'programs',
            'filterDay',
            'filterSlot',
            'filterProgramId',
            'search'
        ));
    }

    /**
     * Eksekusi alokasi santri ke mentor pada slot jam tertentu.
     */
    public function assignStudent(AssignStudentRequest $request): RedirectResponse
    {
        try {
            $this->availabilityService->assignStudent($request->validated());

            return redirect()->route('admin.mentors.availability')
                ->with('success', '✅ Santri berhasil dialokasikan ke jadwal mentor!');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal alokasi: '.$e->getMessage())->withInput();
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
     * API AJAX untuk mencari mentor yang membuka slot tertentu dan masih punya kuota.
     */
    public function getAvailableMentors(Request $request): JsonResponse
    {
        $day = $request->query('day');
        $slot = $request->query('slot');
        $studentId = $request->query('student_id');

        if (! $day || $slot === null || ! is_numeric($slot)) {
            return response()->json(['error' => 'Parameter hari dan angka slot (0-6) wajib diisi.'], 400);
        }

        $slotNum = (int) $slot;
        $mentors = $this->availabilityService->getAvailableMentors($day, $slotNum);

        // Filter berdasarkan aturan gender & umur jika student_id disertakan
        if ($studentId) {
            $student = Student::find($studentId);
            if ($student) {
                $matchingService = app(MentorMatchingService::class);
                $mentors = $mentors->filter(function ($m) use ($student, $matchingService) {
                    return $matchingService->calculateGenderScore($m, $student) > 0.0;
                })->values();
            }
        }

        // Ambil kuota santri per mentor di slot ini
        $dayKey = MentorAvailability::INDONESIAN_TO_ENGLISH[strtolower($day)] ?? strtolower($day);
        $counts = DB::table('mentor_student')
            ->select('mentor_id', DB::raw('count(*) as total'))
            ->where('day_assigned', $dayKey)
            ->where('slot_number', $slotNum)
            ->where('is_active', true)
            ->groupBy('mentor_id')
            ->pluck('total', 'mentor_id');

        return response()->json([
            'day' => $dayKey,
            'slot' => $slotNum,
            'time' => MentorAvailability::SLOT_MAP[$slotNum]['time'] ?? '08:00',
            'mentors' => $mentors->map(function ($m) use ($counts) {
                $current = $counts[$m->id] ?? 0;

                return [
                    'id' => $m->id,
                    'name' => $m->getDisplayName(),
                    'gender' => $m->gender ?? $m->user?->gender ?? 'L',
                    'specialization' => $m->specialization ?? 'Al-Qur\'an',
                    'current' => $current,
                    'max' => 1,
                    'remaining' => max(0, 1 - $current),
                ];
            }),
        ]);
    }
}
