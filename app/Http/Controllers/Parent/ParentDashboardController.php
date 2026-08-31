<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Progress;
use App\Models\Session;
use Illuminate\View\View;

class ParentDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $parent = $user->parentProfile;
        $hasPaidProgram = $user->hasActivePaidProgram();
        $hasPendingEnrollment = $user->hasPendingInvoiceOrEnrollment();
        $latestEnrollment = $user->getLatestEnrollment();

        $children = $parent
            ? $parent->students()->with(['user', 'mentors.user'])->get()
            : collect();

        $childIds = $children->pluck('id')->toArray();

        // 1. Data Statistik (Hanya dihitung jika akun sudah lunas, hemat resource)
        $totalChildrenCount = $children->count();

        $monthSessionsCount = ($hasPaidProgram && count($childIds) > 0)
            ? Session::whereIn('student_id', $childIds)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count()
            : 0;

        $avgTajwidScore = ($hasPaidProgram && count($childIds) > 0)
            ? round(Progress::whereIn('student_id', $childIds)->avg('nilai_tajwid') ?? 0, 1)
            : 0;

        $pendingPaymentsCount = count($childIds) > 0
            ? Payment::whereIn('student_id', $childIds)
                ->where('status', 'pending')
                ->count()
            : 0;

        // 2. Progres Anak Terbaru
        $recentProgresses = ($hasPaidProgram && count($childIds) > 0)
            ? Progress::with(['student.user', 'mentor.user'])
                ->whereIn('student_id', $childIds)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        // 3. Jadwal Bimbingan Mendatang (7 Hari Ke Depan)
        $upcomingSessions = ($hasPaidProgram && count($childIds) > 0)
            ? Session::with(['student.user', 'mentor.user'])
                ->whereIn('student_id', $childIds)
                ->whereDate('date', '>=', today())
                ->whereDate('date', '<=', today()->addDays(7))
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get()
            : collect();

        // 4. Pesan Masuk
        $unreadMessagesCount = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('parent.dashboard', compact(
            'user',
            'parent',
            'children',
            'hasPaidProgram',
            'hasPendingEnrollment',
            'latestEnrollment',
            'totalChildrenCount',
            'monthSessionsCount',
            'avgTajwidScore',
            'pendingPaymentsCount',
            'recentProgresses',
            'upcomingSessions',
            'unreadMessagesCount'
        ));
    }
}
