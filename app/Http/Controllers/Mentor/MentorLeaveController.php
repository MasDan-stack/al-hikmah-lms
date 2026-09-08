<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentorLeave;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorLeaveController extends Controller
{
    /**
     * Tampilkan halaman riwayat & form pengajuan cuti mentor.
     */
    public function index(): View
    {
        $mentor = auth()->user()?->mentor;

        if (! $mentor) {
            abort(403, 'Akses khusus pendamping / guru.');
        }

        $leaves = MentorLeave::with(['substituteMentor.user'])
            ->where('mentor_id', $mentor->id)
            ->orderBy('leave_date', 'desc')
            ->get();

        $totalLeaves = $leaves->count();
        $approvedLeaves = $leaves->where('status', 'approved')->count();
        $pendingLeaves = $leaves->where('status', 'pending')->count();
        $rejectedLeaves = $leaves->where('status', 'rejected')->count();

        return view('mentor.leaves.index', compact(
            'mentor',
            'leaves',
            'totalLeaves',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves'
        ));
    }

    /**
     * Simpan pengajuan cuti baru mentor (mendukung rentang tanggal).
     */
    public function store(Request $request): RedirectResponse
    {
        $mentor = auth()->user()?->mentor;

        if (! $mentor) {
            abort(403, 'Akses khusus pendamping / guru.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
        ], [
            'start_date.required' => 'Tanggal mulai cuti wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal cuti tidak boleh di masa lampau.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'reason.required' => 'Alasan cuti wajib diisi.',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = ! empty($validated['end_date']) ? Carbon::parse($validated['end_date']) : $startDate;

        $period = CarbonPeriod::create($startDate, $endDate);
        $createdCount = 0;

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');

            // Cek apakah sudah ada permohonan di tanggal ini
            $exists = MentorLeave::where('mentor_id', $mentor->id)
                ->where('leave_date', $dateStr)
                ->exists();

            if (! $exists) {
                MentorLeave::create([
                    'mentor_id' => $mentor->id,
                    'leave_date' => $dateStr,
                    'reason' => $validated['reason'],
                    'status' => 'pending',
                ]);
                $createdCount++;
            }
        }

        if ($createdCount === 0) {
            return back()->with('warning', 'Pengajuan cuti untuk tanggal yang dipilih sudah pernah diajukan sebelumnya.');
        }

        return redirect()->route('mentor.leaves.index')
            ->with('success', "Permohonan cuti ({$createdCount} hari) berhasil diajukan dan sedang menunggu persetujuan manajemen.");
    }

    /**
     * Batalkan permohonan cuti jika masih berstatus pending.
     */
    public function destroy(MentorLeave $leave): RedirectResponse
    {
        $mentor = auth()->user()?->mentor;

        if (! $mentor || $leave->mentor_id !== $mentor->id) {
            abort(403, 'Anda tidak memiliki hak untuk membatalkan cuti ini.');
        }

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Hanya permohonan cuti berstatus "Menunggu Persetujuan" yang dapat dibatalkan.');
        }

        $leave->delete();

        return redirect()->route('mentor.leaves.index')
            ->with('success', 'Permohonan cuti berhasil dibatalkan.');
    }
}
