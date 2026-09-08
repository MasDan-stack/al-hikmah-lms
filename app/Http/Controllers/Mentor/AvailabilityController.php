<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentorAvailability;
use App\Services\MentorAvailabilityService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function __construct(
        protected MentorAvailabilityService $availabilityService
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        $days = MentorAvailability::DAYS_ORDER;
        $dayLabels = MentorAvailability::DAYS;
        $slotMap = MentorAvailability::SLOT_MAP;

        // Ambil santri yang teralokasi per hari dan slot
        $rawAssigned = $mentor ? DB::table('mentor_student')
            ->join('students', 'students.id', '=', 'mentor_student.student_id')
            ->leftJoin('programs', 'programs.id', '=', 'mentor_student.program_id')
            ->select(
                'mentor_student.id',
                'mentor_student.day_assigned',
                'mentor_student.slot_number',
                'mentor_student.time_label',
                'mentor_student.time_assigned',
                'students.id as student_id',
                'students.full_name as student_name',
                'programs.name as program_name'
            )
            ->where('mentor_student.mentor_id', $mentor->id)
            ->where('mentor_student.is_active', true)
            ->get() : collect();

        // Auto-heal data lama yang slot_number nya masih null
        $assignedRows = $rawAssigned->map(function ($row) use ($mentor) {
            if ($row->slot_number === null && $row->time_assigned) {
                $inferredSlot = MentorAvailability::getSlotNumberFromTime($row->time_assigned);
                $timeLabel = MentorAvailability::SLOT_MAP[$inferredSlot]['time'] ?? substr($row->time_assigned, 0, 5);
                DB::table('mentor_student')->where('id', $row->id)->update([
                    'slot_number' => $inferredSlot,
                    'time_label' => $timeLabel,
                ]);
                $row->slot_number = $inferredSlot;
                $row->time_label = $timeLabel;

                // Pastikan mentor_availabilities juga memiliki slot ini
                $avail = MentorAvailability::firstOrCreate(
                    ['mentor_id' => $mentor->id, 'day' => $row->day_assigned],
                    ['slot_numbers' => [$inferredSlot], 'max_students' => 5, 'is_available' => true]
                );
                $curr = $avail->slot_numbers ?? [];
                if (! in_array($inferredSlot, $curr, true)) {
                    $curr[] = $inferredSlot;
                    sort($curr);
                    $avail->update(['slot_numbers' => array_values(array_unique($curr)), 'is_available' => true, 'is_holiday' => false]);
                }
            }

            return $row;
        })->groupBy('day_assigned');

        // Ambil ketersediaan mentor
        $availabilities = $mentor
            ? $this->availabilityService->getMentorAvailability($mentor->id)
            : collect();

        $whatsappText = $mentor ? $this->availabilityService->exportToWhatsAppFormat($mentor->id) : '';

        return view('mentor.availability.index', compact(
            'mentor',
            'availabilities',
            'days',
            'dayLabels',
            'slotMap',
            'assignedRows',
            'whatsappText'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (! $mentor) {
            return back()->with('error', 'Profil mentor tidak ditemukan.');
        }

        $request->validate([
            'max_students' => 'nullable|integer|min:1|max:20',
            'days' => 'nullable|array',
            'availability' => 'nullable|array',
        ]);

        try {
            $results = $this->availabilityService->saveAvailability($mentor->id, $request->all());

            // Kirim notifikasi ke Admin jika mentor menetapkan hari libur
            $holidayDays = collect($results)->filter(fn ($avail) => $avail->is_holiday)->keys();
            if ($holidayDays->isNotEmpty()) {
                $dayLabels = $holidayDays->map(fn ($d) => MentorAvailability::DAYS[$d] ?? ucfirst($d))->implode(', ');
                $mentorName = $mentor->getDisplayName();
                NotificationService::notifyAdmins(
                    'Pengajuan Hari Bebas / Libur Mentor',
                    "Guru {$mentorName} telah memperbarui jadwal mengajar dan menetapkan hari libur pada: {$dayLabels}.",
                    'info',
                    route('admin.mentors.availability', ['search' => $mentorName]),
                    'mentor_availability'
                );
            }

            return redirect()->route('mentor.availability.index')
                ->with('success', '✅ Ketersediaan jadwal mengajar berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    public function importFromWhatsApp(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (! $mentor) {
            return back()->with('error', 'Profil mentor tidak ditemukan.');
        }

        $request->validate([
            'raw_text' => 'required|string',
            'max_students' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $parsed = $this->availabilityService->parseWhatsAppFormat($request->input('raw_text'));

            if (empty($parsed)) {
                return back()->with('error', 'Format teks tidak terbaca. Pastikan terdapat format seperti "Senin : 1 2 3 4".');
            }

            $results = $this->availabilityService->saveAvailability($mentor->id, [
                'availability' => $parsed,
                'max_students' => $request->input('max_students', 5),
            ]);

            // Kirim notifikasi ke Admin jika terdapat hari libur
            $holidayDays = collect($results)->filter(fn ($avail) => $avail->is_holiday)->keys();
            if ($holidayDays->isNotEmpty()) {
                $dayLabels = $holidayDays->map(fn ($d) => MentorAvailability::DAYS[$d] ?? ucfirst($d))->implode(', ');
                $mentorName = $mentor->getDisplayName();
                NotificationService::notifyAdmins(
                    'Pengajuan Hari Bebas / Libur Mentor (WhatsApp)',
                    "Guru {$mentorName} telah mengimpor jadwal mengajar via WhatsApp dan menetapkan hari libur pada: {$dayLabels}.",
                    'info',
                    route('admin.mentors.availability', ['search' => $mentorName]),
                    'mentor_availability'
                );
            }

            return redirect()->route('mentor.availability.index')
                ->with('success', '✅ Jadwal berhasil diimpor otomatis dari format WhatsApp!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor: '.$e->getMessage());
        }
    }
}
