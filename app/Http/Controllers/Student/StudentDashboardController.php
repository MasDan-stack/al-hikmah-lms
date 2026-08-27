<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\HifzMilestone;
use App\Models\HifzTarget;
use App\Models\JuzProgress;
use App\Models\Student;
use App\Services\HifzProgressService;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __construct(
        protected HifzProgressService $hifzProgressService,
        protected LeaderboardService $leaderboardService
    ) {}

    protected function getStudent(): ?Student
    {
        return auth()->user()->student ?? Student::where('user_id', auth()->id())->first();
    }

    /**
     * Dashboard Utama Santri dengan Widget Lengkap
     */
    public function index(): View
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(404, 'Profil Santri tidak ditemukan.');
        }

        // 1. Target Hari Ini
        $todayTarget = HifzTarget::where('student_id', $student->id)
            ->where('target_date', now()->toDateString())
            ->first();

        // 2. Milestone Countdown Terdekat
        $activeMilestone = HifzMilestone::where('student_id', $student->id)
            ->where('status', 'active')
            ->where('target_date', '>=', now())
            ->orderBy('target_date', 'asc')
            ->first();

        // 3. Ringkasan Progress Hafalan Per Juz
        $progressSummary = $this->hifzProgressService->getSummary($student);

        // 4. Leaderboard Snapshot & My Rank
        $leaderboard = $this->leaderboardService->getLeaderboard('overall', 10);
        $myRankEntry = $leaderboard->firstWhere('student_id', $student->id);

        // 5. Badge Koleksi Terbaru
        $earnedBadges = $student->earnedBadges()->latest('student_badges.created_at')->take(4)->get();
        $totalBadgesCount = Badge::where('is_active', true)->count();

        return view('student.dashboard', compact(
            'student',
            'todayTarget',
            'activeMilestone',
            'progressSummary',
            'leaderboard',
            'myRankEntry',
            'earnedBadges',
            'totalBadgesCount'
        ));
    }

    /**
     * Halaman Detail Target Hafalan Hari Ini & Riwayat
     */
    public function targetHariIni(): View
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(404);
        }

        $todayTarget = HifzTarget::where('student_id', $student->id)
            ->where('target_date', now()->toDateString())
            ->first();

        $historyTargets = HifzTarget::where('student_id', $student->id)
            ->where('target_date', '<', now()->toDateString())
            ->orderBy('target_date', 'desc')
            ->paginate(15);

        return view('student.targets.today', compact('student', 'todayTarget', 'historyTargets'));
    }

    /**
     * Halaman Visualisasi Progres 30 Juz
     */
    public function progressPerJuz(): View
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(404);
        }

        $progressSummary = $this->hifzProgressService->getSummary($student);
        $juzList = JuzProgress::where('student_id', $student->id)->orderBy('juz_number')->get();

        return view('student.progress.juz', compact('student', 'progressSummary', 'juzList'));
    }

    /**
     * Halaman Leaderboard Santri
     */
    public function leaderboard(Request $request): View
    {
        $student = $this->getStudent();
        $category = $request->query('category', 'overall');

        $validCategories = ['overall', 'anak', 'dewasa', 'streak'];
        if (! in_array($category, $validCategories)) {
            $category = 'overall';
        }

        $leaderboard = $this->leaderboardService->getLeaderboard($category, 50);
        $myRankEntry = $student ? $leaderboard->firstWhere('student_id', $student->id) : null;

        return view('student.leaderboard', compact('student', 'category', 'leaderboard', 'myRankEntry'));
    }

    /**
     * Halaman Koleksi Badge & Pencapaian
     */
    public function badges(): View
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(404);
        }

        $allBadges = Badge::where('is_active', true)->get();
        $earnedBadges = $student->earnedBadges()->get()->keyBy('id');

        return view('student.badges.index', compact('student', 'allBadges', 'earnedBadges'));
    }

    /**
     * Halaman Milestone Targets
     */
    public function milestones(): View
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(404);
        }

        $milestones = HifzMilestone::where('student_id', $student->id)
            ->orderBy('target_date', 'asc')
            ->get();

        return view('student.targets.milestones', compact('student', 'milestones'));
    }
}
