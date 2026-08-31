<?php

namespace App\Http\Controllers\Parent;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\SessionConfirmation;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentScheduleController extends Controller
{
    public function index(): View
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $activeEnrollments = count($childIds) > 0
            ? Enrollment::with(['student.user', 'mentor.user', 'program'])
                ->whereIn('student_id', $childIds)
                ->whereIn('status', [
                    EnrollmentStatus::CONFIRMED->value,
                    EnrollmentStatus::ACTIVE->value,
                ])
                ->get()
            : collect();

        $sessions = count($childIds) > 0
            ? Session::with(['student.user', 'mentor.user'])
                ->whereIn('student_id', $childIds)
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get()
            : collect();

        return view('parent.schedules.index', compact('sessions', 'activeEnrollments'));
    }

    public function list(Request $request): View
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];
        $status = $request->query('status', 'all');

        $query = Session::with(['student.user', 'mentor.user'])
            ->whereIn('student_id', $childIds);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $sessions = $query->orderBy('date', 'desc')->paginate(10);

        return view('parent.schedules.list', compact('sessions', 'status'));
    }

    public function show(int $id): View
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $session = Session::with(['student.user', 'mentor.user'])->findOrFail($id);

        if (! in_array($session->student_id, $childIds)) {
            abort(403, 'Akses sesi anak ditolak.');
        }

        $confirmation = SessionConfirmation::where('session_id', $session->id)
            ->where('parent_id', $parent?->id)
            ->first();

        return view('parent.schedules.show', compact('session', 'confirmation'));
    }

    public function confirm(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:hadir,izin,sakit',
            'notes' => 'nullable|string|max:500',
        ]);

        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $session = Session::with(['student.user', 'mentor.user'])->findOrFail($id);
        if (! in_array($session->student_id, $childIds)) {
            abort(403, 'Akses sesi anak ditolak.');
        }

        SessionConfirmation::updateOrCreate(
            [
                'session_id' => $session->id,
                'parent_id' => $parent->id,
            ],
            [
                'status' => $request->status,
                'notes' => $request->notes,
            ]
        );

        // Notifikasi ke Mentor Pembimbing via NotificationService
        if ($session->mentor?->user_id) {
            $studentName = $session->student?->getDisplayName() ?? 'Santri';
            $statusLabel = ucfirst($request->status);
            $sessionDate = Carbon::parse($session->date)->locale('id')->isoFormat('dddd, D MMMM Y');

            NotificationService::send(
                $session->mentor->user_id,
                "Konfirmasi Kehadiran: {$studentName} ({$statusLabel})",
                "Wali santri {$studentName} mengonfirmasi status kehadiran '{$statusLabel}' untuk sesi {$sessionDate}.".($request->notes ? " Catatan: {$request->notes}" : ''),
                $request->status === 'hadir' ? NotificationType::SUCCESS : NotificationType::WARNING,
                route('mentor.dashboard'),
                'attendance',
                true
            );
        }

        return redirect()->back()->with('success', 'Konfirmasi kehadiran berhasil dikirim ke Ustadz/Ustadzah!');
    }
}
