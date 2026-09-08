<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RevenueReportExport;
use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Models\Payment;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    /**
     * Tampilkan antarmuka generator laporan keuangan
     */
    public function index(Request $request): View
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();
        $programId = $request->filled('program_id') ? (int) $request->program_id : null;
        $status = $request->query('status', 'paid');

        $programs = Program::all();

        // Data transaksi untuk preview tabel
        $query = Payment::with(['student.user', 'student.parent.user', 'program'])
            ->latest('payment_date');

        if ($startDate && $endDate) {
            $query->whereBetween('payment_date', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
        }

        if ($programId) {
            $query->where('program_id', $programId);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $payments = $query->paginate(20)->withQueryString();
        $totalAmount = (float) $query->sum('amount');
        $totalCount = (int) $query->count();

        return view('admin.reports.index', compact(
            'payments',
            'programs',
            'startDate',
            'endDate',
            'programId',
            'status',
            'totalAmount',
            'totalCount'
        ));
    }

    /**
     * Ekspor laporan ke format Excel / CSV spreadsheet
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : null;
        $programId = $request->filled('program_id') ? (int) $request->program_id : null;
        $status = $request->query('status', 'paid');

        $exporter = new RevenueReportExport($startDate, $endDate, $programId, $status);
        $filename = 'Laporan_Keuangan_ALHIKMAH_'.now()->format('Ymd_His').'.csv';

        // Catat audit log ekspor
        FinancialAuditLog::log(
            auth()->id(),
            'export_report_excel',
            'payment',
            0,
            null,
            ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'format' => 'excel_csv']
        );

        return $exporter->download($filename);
    }

    /**
     * Ekspor laporan ke format PDF resmi dengan Kop Surat
     */
    public function exportPdf(Request $request): View
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();
        $programId = $request->filled('program_id') ? (int) $request->program_id : null;
        $status = $request->query('status', 'paid');

        $query = Payment::with(['student.user', 'student.parent.user', 'program'])
            ->latest('payment_date');

        if ($startDate && $endDate) {
            $query->whereBetween('payment_date', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
        }

        if ($programId) {
            $query->where('program_id', $programId);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $payments = $query->get();
        $totalAmount = (float) $payments->sum('amount');
        $totalRegistration = (float) $payments->sum('registration_fee');
        $totalProgram = (float) $payments->sum('program_fee');

        $selectedProgram = $programId ? Program::find($programId) : null;

        // Catat audit log ekspor
        FinancialAuditLog::log(
            auth()->id(),
            'export_report_pdf',
            'payment',
            0,
            null,
            ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'format' => 'pdf']
        );

        return view('admin.reports.pdf.revenue-pdf', [
            'payments' => $payments,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedProgram' => $selectedProgram,
            'status' => $status,
            'totalAmount' => $totalAmount,
            'totalRegistration' => $totalRegistration,
            'totalProgram' => $totalProgram,
            'generatedAt' => now()->translatedFormat('d F Y H:i').' WIB',
            'adminUser' => auth()->user(),
        ]);
    }
}
