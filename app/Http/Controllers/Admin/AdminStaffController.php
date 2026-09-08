<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Models\Mentor;
use App\Models\MentorActivityLog;
use App\Models\MentorPerformanceSnapshot;
use App\Services\StaffAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function __construct(
        protected StaffAnalyticsService $staffService
    ) {}

    /**
     * Tampilkan dasbor beban kerja dan manajemen SDM guru
     */
    public function index(Request $request): View
    {
        $summary = $this->staffService->getStaffSummary();
        $mentors = $this->staffService->getMentorWorkloadList();
        $topMentors = $this->staffService->getTopPerformingMentors(5);
        $workloadByProgram = $this->staffService->getWorkloadDistributionByProgram();

        return view('admin.staff.index', compact(
            'summary',
            'mentors',
            'topMentors',
            'workloadByProgram'
        ));
    }

    /**
     * Tampilkan halaman detail profil lengkap mentor untuk admin
     */
    public function show(int $id): View
    {
        $mentor = Mentor::with([
            'user',
            'students.user',
            'mentorApplication.documents',
            'probationTracking',
        ])->findOrFail($id);

        $cvDoc = $mentor->mentorApplication?->documents
            ->where('document_type', 'cv')->first();
        $certDoc = $mentor->mentorApplication?->documents
            ->where('document_type', 'certificate')->first();
        $latestSnap = MentorPerformanceSnapshot::where('mentor_id', $id)
            ->latest()->first();

        // Ambil riwayat log verifikasi rekening terakhir jika ada
        $latestBankLog = MentorActivityLog::where('mentor_id', $mentor->id)
            ->where('action', 'bank_account_verification')
            ->latest()
            ->first();

        return view('admin.staff.show', compact('mentor', 'cvDoc', 'certDoc', 'latestSnap', 'latestBankLog'));
    }

    /**
     * Verifikasi status rekening bank mentor oleh admin
     */
    public function verifyBank(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:verified,unverified,needs_clarification',
            'notes' => 'nullable|string|max:500',
        ]);

        $mentor = Mentor::findOrFail($id);
        $status = $request->input('status');
        $notes = $request->input('notes');

        $labels = [
            'verified' => 'Terverifikasi',
            'unverified' => 'Belum Diverifikasi',
            'needs_clarification' => 'Perlu Klarifikasi',
        ];
        $statusLabel = $labels[$status] ?? $status;

        // Catat ke MentorActivityLog
        MentorActivityLog::log(
            $mentor->id,
            'bank_account_verification',
            "Verifikasi rekening bank oleh Admin: {$statusLabel}".($notes ? " (Catatan: {$notes})" : '')
        );

        // Catat ke FinancialAuditLog
        FinancialAuditLog::log(
            userId: auth()->id(),
            action: 'mentor_bank_verification',
            entityType: 'mentor',
            entityId: $mentor->id,
            oldValues: null,
            newValues: [
                'status' => $status,
                'status_label' => $statusLabel,
                'notes' => $notes,
                'bank_name' => $mentor->bank_name,
                'bank_account_number' => $mentor->bank_account_number,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Status verifikasi rekening berhasil diubah menjadi {$statusLabel}.",
            'status' => $status,
            'status_label' => $statusLabel,
        ]);
    }
}
