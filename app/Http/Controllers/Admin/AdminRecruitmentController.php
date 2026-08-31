<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorApplication;
use App\Models\MentorTestSession;
use App\Services\MentorRecruitmentService;
use App\Services\MentorTestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminRecruitmentController extends Controller
{
    public function __construct(
        protected MentorRecruitmentService $recruitmentService,
        protected MentorTestService $testService
    ) {}

    // ==========================================
    // MANAJEMEN LAMARAN (APPLICATIONS)
    // ==========================================
    public function applications()
    {
        $applications = MentorApplication::latest()->get();

        return view('admin.recruitment.applications.index', compact('applications'));
    }

    public function exportCsv(Request $request)
    {
        $applications = MentorApplication::latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=mentor_applications_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['No', 'Nomor Registrasi', 'Nama Lengkap', 'Email', 'No. WhatsApp', 'Spesialisasi', 'Tahap Saat Ini', 'Status', 'Tanggal Daftar'];

        $callback = function () use ($applications, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $rowCounter = 1;
            foreach ($applications as $app) {
                fputcsv($file, [
                    $rowCounter++,
                    $app->application_code,
                    $app->full_name,
                    $app->email,
                    $app->phone,
                    $app->specialization,
                    'Tahap '.$app->current_stage,
                    $app->status,
                    $app->submitted_at ? $app->submitted_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function showApplication($id)
    {
        $application = MentorApplication::with(['documents', 'testSessions'])->findOrFail($id);

        return view('admin.recruitment.applications.show', compact('application'));
    }

    public function downloadDocument($id, $documentId)
    {
        $application = MentorApplication::findOrFail($id);
        $document = $application->documents()->findOrFail($documentId);

        if (! Storage::exists($document->file_path)) {
            // Check fallback for public/storage or default storage
            if (Storage::disk('public')->exists($document->file_path)) {
                return Storage::disk('public')->response($document->file_path, $document->file_name);
            }

            return back()->with('error', 'File berkas tidak ditemukan di server.');
        }

        return Storage::response($document->file_path, $document->file_name);
    }

    public function approveDocument($id)
    {
        $application = MentorApplication::findOrFail($id);

        try {
            $this->recruitmentService->processDocumentReview($application, true);

            return back()->with('success', 'Dokumen disetujui. Pelamar lanjut ke tahap tes.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectApplication(Request $request, $id)
    {
        $request->validate(['notes' => 'required|string']);
        $application = MentorApplication::findOrFail($id);

        try {
            $this->recruitmentService->rejectApplication($application, $request->notes);

            return back()->with('success', 'Lamaran berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function scheduleInterview(Request $request, $id)
    {
        $request->validate(['notes' => 'nullable|string']);
        $application = MentorApplication::findOrFail($id);

        try {
            $this->recruitmentService->scheduleInterview($application, $request->notes);

            return back()->with('success', 'Pelamar dijadwalkan wawancara.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function acceptApplication(Request $request, $id)
    {
        $request->validate(['notes' => 'nullable|string']);
        $application = MentorApplication::findOrFail($id);

        try {
            $this->recruitmentService->acceptApplication($application, $request->notes);

            return back()->with('success', 'Pelamar diterima dan akun mentor telah dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ==========================================
    // MANAJEMEN TES (TEST SESSIONS)
    // ==========================================
    public function testSessions()
    {
        $sessions = MentorTestSession::with('application')->latest()->get();

        return view('admin.recruitment.tests.index', compact('sessions'));
    }

    public function generateTest($applicationId)
    {
        $application = MentorApplication::findOrFail($applicationId);

        try {
            $this->testService->generateTest($application);

            return back()->with('success', 'Soal tes berhasil di-generate.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showTest($id)
    {
        $session = MentorTestSession::with('application')->findOrFail($id);

        return view('admin.recruitment.tests.show', compact('session'));
    }

    public function evaluateTest($id)
    {
        $session = MentorTestSession::findOrFail($id);

        try {
            $this->testService->evaluateTest($session);

            return back()->with('success', 'Evaluasi AI selesai.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
