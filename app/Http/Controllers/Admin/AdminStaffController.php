<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Models\Mentor;
use App\Models\MentorActivityLog;
use App\Models\MentorPerformanceSnapshot;
use App\Services\RevenueAnalyticsService;
use App\Services\StaffAnalyticsService;
use Carbon\Carbon;
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

        $slipMonth = (int) request('slip_month', now()->month);
        $slipYear = (int) request('slip_year', now()->year);
        $salarySlip = app(RevenueAnalyticsService::class)->getMentorSalarySlipData($mentor->id, $slipMonth, $slipYear);

        return view('admin.staff.show', compact('mentor', 'cvDoc', 'certDoc', 'latestSnap', 'latestBankLog', 'salarySlip'));
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

    /**
     * Tandai status pembayaran honor bulanan mentor (Lunas / Pending) oleh Admin
     */
    public function markSalaryPaid(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|integer|min:1|max:12',
            'status' => 'required|in:paid,pending',
        ]);

        $mentor = Mentor::findOrFail($id);
        $year = (int) $request->input('year');
        $month = (int) $request->input('month');
        $status = $request->input('status');

        app(RevenueAnalyticsService::class)->markMentorSalaryStatus(
            mentorId: $mentor->id,
            year: $year,
            month: $month,
            status: $status,
        );

        $statusLabel = $status === 'paid' ? 'Lunas' : 'Menunggu Verifikasi Admin';
        $periodLabel = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');

        MentorActivityLog::log(
            $mentor->id,
            'salary_status_updated',
            "Status honor periode {$periodLabel} ditandai '{$statusLabel}' oleh Admin."
        );

        return response()->json([
            'success' => true,
            'message' => "Honor {$mentor->full_name} periode {$periodLabel} berhasil ditandai sebagai {$statusLabel}.",
            'status' => $status,
            'status_label' => $statusLabel,
        ]);
    }

    /**
     * Cetak lembar slip gaji resmi mentor dari dashboard admin
     */
    public function printSalarySlip(int $id): View
    {
        $mentor = Mentor::findOrFail($id);
        $slipMonth = (int) request('slip_month', now()->month);
        $slipYear = (int) request('slip_year', now()->year);

        $salarySlip = app(RevenueAnalyticsService::class)->getMentorSalarySlipData($mentor->id, $slipMonth, $slipYear);

        return view('mentor.salary-slip-print', compact('salarySlip', 'mentor'));
    }
}
