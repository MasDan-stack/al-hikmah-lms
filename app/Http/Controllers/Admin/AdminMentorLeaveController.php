<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\MentorLeave;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMentorLeaveController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    /**
     * Tampilkan daftar seluruh permohonan cuti guru/mentor.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = MentorLeave::with(['mentor.user', 'substituteMentor.user'])
            ->orderBy('leave_date', 'desc');

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $leaves = $query->get();

        $today = now()->format('Y-m-d');
        $leavesToday = MentorLeave::where('leave_date', $today)->where('status', 'approved')->count();
        $pendingCount = MentorLeave::where('status', 'pending')->count();
        $approvedCount = MentorLeave::where('status', 'approved')->count();
        $rejectedCount = MentorLeave::where('status', 'rejected')->count();

        // Daftar mentor aktif yang siap menjadi guru pengganti
        $availableSubstitutes = Mentor::with('user')
            ->where('is_active', true)
            ->whereIn('status', ['active', 'probation'])
            ->get();

        return view('admin.mentors.leaves.index', compact(
            'leaves',
            'status',
            'leavesToday',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'availableSubstitutes'
        ));
    }

    /**
     * Setujui permohonan cuti dan tunjuk guru pengganti.
     */
    public function approve(Request $request, MentorLeave $leave): RedirectResponse
    {
        $validated = $request->validate([
            'substitute_mentor_id' => 'nullable|exists:mentors,id',
            'notify_parents' => 'nullable|boolean',
        ]);

        $leave->update([
            'status' => 'approved',
            'substitute_mentor_id' => $validated['substitute_mentor_id'] ?? null,
        ]);

        // Kirim notifikasi WhatsApp ke orang tua santri binaan jika diminta dan ada guru pengganti
        if (! empty($validated['notify_parents']) && $leave->substitute_mentor_id) {
            $substitute = Mentor::with('user')->find($leave->substitute_mentor_id);
            $primaryMentor = $leave->mentor;

            if ($substitute && $primaryMentor) {
                $substituteName = $substitute->getDisplayName();
                $primaryName = $primaryMentor->getDisplayName();
                $leaveDateFormatted = $leave->leave_date->format('d M Y');

                // Dapatkan santri binaan aktif
                $students = $primaryMentor->students()
                    ->wherePivot('is_active', true)
                    ->with('parentProfile.user')
                    ->get();

                foreach ($students as $student) {
                    $parentUser = $student->parentProfile?->user;
                    $parentPhone = $student->parentProfile?->phone ?? $parentUser?->phone;

                    if ($parentPhone) {
                        $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\n"
                            ."Ayah/Bunda dari Ananda *{$student->name}*,\n"
                            ."Kami informasikan bahwa pada tanggal *{$leaveDateFormatted}*, sesi bimbingan Al-Qur'an akan didampingi sementara oleh *{$substituteName}* menggantikan *{$primaryName}* yang sedang cuti/berhalangan.\n\n"
                            ."Jadwal dan materi belajar tetap berjalan sesuai kurikulum. Jazakumullahu khairan katsiran.\n\n"
                            .'_Manajemen AL-HIKMAH LMS_';

                        $this->whatsAppService->sendMessage($parentPhone, $message);
                    }
                }
            }
        }

        $msg = 'Permohonan cuti berhasil disetujui';
        if ($leave->substitute_mentor_id) {
            $msg .= ' dan guru pengganti berhasil ditugaskan.';
        } else {
            $msg .= '.';
        }

        return redirect()->route('admin.mentors.leaves.index')->with('success', $msg);
    }

    /**
     * Tolak permohonan cuti.
     */
    public function reject(Request $request, MentorLeave $leave): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_note' => 'nullable|string|max:255',
        ]);

        $reason = $leave->reason;
        if (! empty($validated['rejection_note'])) {
            $reason .= ' [Catatan Admin: '.$validated['rejection_note'].']';
        }

        $leave->update([
            'status' => 'rejected',
            'reason' => $reason,
        ]);

        return redirect()->route('admin.mentors.leaves.index')
            ->with('success', 'Permohonan cuti guru telah ditolak.');
    }

    /**
     * Hapus data permohonan cuti.
     */
    public function destroy(MentorLeave $leave): RedirectResponse
    {
        $leave->delete();

        return redirect()->route('admin.mentors.leaves.index')
            ->with('success', 'Data cuti berhasil dihapus.');
    }
}
