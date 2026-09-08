<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\RevenueAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRevenueController extends Controller
{
    public function __construct(
        protected RevenueAnalyticsService $revenueService
    ) {}

    /**
     * Tampilkan halaman utama analitik pendapatan & keuangan
     */
    public function index(Request $request): View
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : null;

        $metrics = $this->revenueService->getSummaryMetrics($startDate, $endDate);
        $programBreakdown = $this->revenueService->getProgramBreakdown();
        $statusDistribution = $this->revenueService->getPaymentStatusDistribution();
        $auditLogs = $this->revenueService->getRecentFinancialAuditLogs(8);
        $programs = Program::all();

        return view('admin.revenue.index', compact(
            'metrics',
            'programBreakdown',
            'statusDistribution',
            'auditLogs',
            'programs',
            'startDate',
            'endDate'
        ));
    }
}
