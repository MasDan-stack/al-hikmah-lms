<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentMessageController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Ambil daftar pesan masuk / keluar
        $messages = Message::with(['sender', 'receiver', 'student.user'])
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest()
            ->get();

        return view('parent.messages.index', compact('messages', 'user'));
    }

    public function create(): View
    {
        $parent = auth()->user()->parentProfile;
        $children = $parent ? $parent->students()->with('mentors.user')->get() : collect();

        // Kumpulkan mentor-mentor dari anak-anak binaan
        $mentors = collect();
        foreach ($children as $child) {
            foreach ($child->mentors as $mentor) {
                if (! $mentors->contains('id', $mentor->id)) {
                    $mentors->push($mentor);
                }
            }
        }

        return view('parent.messages.create', compact('children', 'mentors'));
    }

    public function chat(int $mentor_id): View
    {
        $user = auth()->user();
        $mentor = Mentor::with('user')->findOrFail($mentor_id);
        $mentorUser = $mentor->user;

        $chatMessages = Message::where(function ($q) use ($user, $mentorUser) {
            $q->where('sender_id', $user->id)->where('receiver_id', $mentorUser->id);
        })->orWhere(function ($q) use ($user, $mentorUser) {
            $q->where('sender_id', $mentorUser->id)->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();

        // Tandai pesan sebagai dibaca
        Message::where('sender_id', $mentorUser->id)
            ->where('receiver_id', $user->id)
            ->update(['is_read' => true]);

        return view('parent.messages.chat', compact('mentor', 'mentorUser', 'chatMessages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'student_id' => 'nullable|exists:students,id',
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'student_id' => $request->student_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pesan berhasil dikirim!');
    }
}
