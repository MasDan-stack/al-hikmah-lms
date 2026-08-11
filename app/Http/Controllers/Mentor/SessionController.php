<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(Request $request): View
    {
        $mentor = auth()->user()->mentor;
        $status = $request->query('status', 'all');

        $query = Session::with(['student.user'])
            ->where('mentor_id', $mentor?->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $sessions = $query->orderBy('date', 'desc')->orderBy('time', 'asc')->paginate(10);

        return view('mentor.sessions.index', compact('sessions', 'status'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,scheduled,completed,cancelled',
        ]);

        $mentor = auth()->user()->mentor;
        $session = Session::where('mentor_id', $mentor?->id)->findOrFail($id);
        $session->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status sesi belajar berhasil diperbarui!');
    }
}
