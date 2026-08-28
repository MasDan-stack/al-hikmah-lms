<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Message;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Session;
use App\Models\SessionConfirmation;
use App\Models\Student;
use App\Models\User;
use App\Services\AlertService;
use App\Services\RevenueAnalyticsService;
use App\Services\StaffAnalyticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected RevenueAnalyticsService $revenueService,
        protected StaffAnalyticsService $staffService,
        protected AlertService $alertService
    ) {}

    public function index(): View
    {
        $totalStudents = Student::count();
        $totalMentors = Mentor::count();
        $todaySessions = Session::whereDate('date', today())->count();
        $totalParents = ParentProfile::count();
        $totalUsers = User::count();

        // 📊 Analitik Eksekutif (v8.2)
        $revenueMetrics = $this->revenueService->getSummaryMetrics();
        $staffSummary = $this->staffService->getStaffSummary();
        $allAlerts = $this->alertService->getAllAlerts();

        // 📌 Widget Monitor User Terdaftar & Role
        $recentUsers = User::with('role')->latest()->take(5)->get();

        // 📌 Widget Monitor Aktivitas Orang Tua (Parent Monitoring Activity)
        $recentConfirmations = SessionConfirmation::with(['session.student.user', 'parent.user'])
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['student.user', 'program'])
            ->latest()
            ->take(5)
            ->get();

        $recentParentMessages = Message::with(['sender', 'student.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalMentors',
            'todaySessions',
            'totalParents',
            'totalUsers',
            'revenueMetrics',
            'staffSummary',
            'allAlerts',
            'recentUsers',
            'recentConfirmations',
            'recentPayments',
            'recentParentMessages'
        ));
    }
}
