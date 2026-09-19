<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\MentorActivityLog;
use App\Models\Session;
use App\Models\SessionConfirmation;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(Request $request): View
    {
        $mentor = auth()->user()->mentor;
        $status = $request->query('status', 'all');

        $query = Session::with([
            'student.user',
            'student.parent.user',
            'student.programs',
            'confirmation',
            'student.enrollments' => function ($q) {
                $q->where('status', EnrollmentStatus::ACTIVE->value)->with('program');
            },
        ])->where('mentor_id', $mentor?->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $sessions = $query->orderBy('date', 'desc')->orderBy('time', 'asc')->get();

        return view('mentor.sessions.index', compact('sessions', 'status'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,scheduled,in_progress,completed,cancelled',
        ]);

        $mentor = auth()->user()->mentor;
        $session = Session::where('mentor_id', $mentor?->id)->findOrFail($id);
        $session->update(['status' => $request->status]);

        MentorActivityLog::log(
            $mentor?->id,
            'update_status_sesi',
            "Mengubah status sesi ID #{$session->id} menjadi ".ucfirst(str_replace('_', ' ', $request->status))
        );

        return redirect()->back()->with('success', 'Status sesi belajar berhasil diperbarui!');
    }

    /**
     * Tampilkan halaman formulir presensi mandiri & upload bukti foto bimbingan oleh Mentor.
     */
    public function showConfirmAttendance(int $id): View
    {
        $user = auth()->user();
        $isAdmin = $user->role?->name === 'admin';
        $mentor = $user->mentor;

        $query = Session::with([
            'student.user',
            'student.parent.user',
            'student.programs',
            'confirmation',
            'mentor.user',
            'student.enrollments' => function ($q) {
                $q->where('status', EnrollmentStatus::ACTIVE->value)->with('program');
            },
        ]);

        if (! $isAdmin) {
            $query->where('mentor_id', $mentor?->id);
        }

        $session = $query->findOrFail($id);
        $confirmation = $session->confirmation
            ?? SessionConfirmation::where('session_id', $session->id)->first();

        return view('mentor.sessions.confirm-attendance', compact('session', 'confirmation'));
    }

    /**
     * Input presensi mandiri oleh Mentor (dengan upload foto bukti pengajaran di lokasi).
     */
    public function confirmAttendance(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:hadir,izin,sakit',
            'notes' => 'nullable|string|max:1000',
            'date' => 'nullable|date',
            'time' => 'nullable|string',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = auth()->user();
        $isAdmin = $user->role?->name === 'admin';
        $mentor = $user->mentor;

        $query = Session::with(['student.parent.user', 'student.user', 'mentor.user']);
        if (! $isAdmin) {
            $query->where('mentor_id', $mentor?->id);
        }
        $session = $query->findOrFail($id);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('attendance_proofs', 'public');
        }

        // Simpan / update ke session_confirmations
        $confirmation = SessionConfirmation::firstOrNew(['session_id' => $session->id]);
        $confirmation->parent_id = $session->student?->parent_id ?? $confirmation->parent_id;
        $confirmation->status = $request->status;
        $confirmation->notes = $request->notes;
        $confirmation->confirmed_by = 'mentor';
        $confirmation->verified_at = now();
        if ($proofPath) {
            $confirmation->proof_image = $proofPath;
        }
        $confirmation->save();

        // Update tanggal/waktu dan status sesi
        $sessionUpdates = [];
        if ($request->filled('date')) {
            $sessionUpdates['date'] = $request->date;
        }
        if ($request->filled('time')) {
            $sessionUpdates['time'] = $request->time;
        }
        if ($request->status === 'hadir') {
            $sessionUpdates['status'] = 'completed';
        } elseif (in_array($request->status, ['izin', 'sakit'])) {
            $sessionUpdates['status'] = 'cancelled';
        }
        if (! empty($sessionUpdates)) {
            $session->update($sessionUpdates);
        }

        // Catat aktivitas mentor
        $targetMentor = $session->mentor ?? $mentor;
        if ($targetMentor) {
            MentorActivityLog::log(
                $targetMentor->id,
                'input_presensi_mandiri',
                'Menginput presensi mandiri ('.ucfirst($request->status).') untuk santri '.($session->student?->getDisplayName() ?? 'Santri')." pada sesi #{$session->id}."
            );
        }

        // Kirim notifikasi ke wali santri
        if ($session->student?->parent?->user_id) {
            NotificationService::send(
                $session->student->parent->user_id,
                'Presensi Sesi: '.($session->student->getDisplayName() ?? 'Santri'),
                'Ustadz/ah '.($targetMentor?->getDisplayName() ?? 'Mentor').' telah mengonfirmasi presensi ('.ucfirst($request->status).') untuk sesi tanggal '.($session->date?->format('d/m/Y') ?? '').($request->notes ? ". Catatan: {$request->notes}" : ''),
                NotificationType::SUCCESS,
                route('parent.schedules.show', $session->id),
                'attendance',
                true
            );
        }

        return redirect()->back()->with('success', 'Presensi & bukti foto pengajaran di rumah santri berhasil disimpan dan terintegrasi otomatis ke Slip Honor & Dashboard!');
    }
}
