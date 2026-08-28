<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StaffAnalyticsService;
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
}
