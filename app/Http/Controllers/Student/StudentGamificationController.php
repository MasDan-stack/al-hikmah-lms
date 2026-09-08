<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\GamificationPoint;
use App\Models\Student;
use App\Models\StudentBadge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentGamificationController extends Controller
{
    protected function getStudent(): ?Student
    {
        return auth()->user()->student ?? Student::where('user_id', auth()->id())->first();
    }

    public function hallOfFame(string $badgeCode): View
    {
        $badge = Badge::where('code', $badgeCode)->firstOrFail();
        $recipients = StudentBadge::with('student.user')
            ->where('badge_id', $badge->id)
            ->latest('earned_at')
            ->paginate(20);

        return view('student.badges.hall-of-fame', compact('badge', 'recipients'));
    }

    public function myStats(): View
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(404);
        }

        $pointLogs = GamificationPoint::where('student_id', $student->id)
            ->latest('created_at')
            ->paginate(20);

        return view('student.stats', compact('student', 'pointLogs'));
    }

    public function togglePrivacy(Request $request): RedirectResponse
    {
        $student = $this->getStudent();
        if (! $student) {
            abort(404);
        }

        $student->privacy_leaderboard = ! $student->privacy_leaderboard;
        $student->save();

        $statusText = $student->privacy_leaderboard ? 'disamarkan (Anonim)' : 'ditampilkan publik';

        return back()->with('success', "Pengaturan privasi leaderboard berhasil diperbarui: Nama Anda kini {$statusText}.");
    }
}
