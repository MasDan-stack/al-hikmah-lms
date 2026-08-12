<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionConfirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentScheduleController extends Controller
{
    public function index(): View
    {
        $parent = auth()->user()->parentProfile;
        $childIds = $parent ? $parent->students()->pluck('id')->toArray() : [];

        $sessions = count($childIds) > 0
            ? Session::with(['student.user', 'mentor.user'])
                ->whereIn('student_id', $childIds)
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get()
            : collect();

        return view('parent.schedules.index', compact('sessions'));
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

        $session = Session::findOrFail($id);
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

        return redirect()->back()->with('success', 'Konfirmasi kehadiran berhasil dikirim!');
    }
}
