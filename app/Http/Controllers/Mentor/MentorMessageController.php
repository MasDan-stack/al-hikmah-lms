<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Student;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MentorMessageController extends Controller
{
    /**
     * Tampilan Kotak Masuk Pesan Mentor
     */
    public function index(): View
    {
        $user = auth()->user();

        // Ambil daftar pesan masuk & keluar mentor
        $messages = Message::with(['sender', 'receiver', 'student.user'])
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest()
            ->get();

        return view('mentor.messages.index', compact('messages', 'user'));
    }

    /**
     * Tampilan Form Buat Pesan Baru ke Orang Tua
     */
    public function create(): View
    {
        $mentor = auth()->user()->mentor;

        if (! $mentor) {
            $parents = collect();
            $students = collect();

            return view('mentor.messages.create', compact('parents', 'students'));
        }

        // Ambil santri binaan yang terdaftar pada mentor ini
        $students = Student::where(function ($q) use ($mentor) {
            $q->whereHas('mentors', fn ($m) => $m->where('mentors.id', $mentor->id))
                ->orWhereHas('enrollments', fn ($e) => $e->where('mentor_id', $mentor->id)->whereIn('status', [
                    EnrollmentStatus::CONFIRMED->value,
                    EnrollmentStatus::ACTIVE->value,
                ]));
        })->with(['user', 'parent.user'])->get();

        // Kumpulkan orang tua dari santri-santri binaan
        $parents = collect();
        foreach ($students as $student) {
            if ($student->parent && $student->parent->user) {
                if (! $parents->contains('id', $student->parent->user->id)) {
                    $parents->push($student->parent->user);
                }
            }
        }

        return view('mentor.messages.create', compact('parents', 'students'));
    }

    /**
     * Ruang Chat / Percakapan Langsung dengan Orang Tua
     */
    public function chat(int $parent_user_id): View
    {
        $user = auth()->user();
        $parentUser = User::with('parentProfile.students')->findOrFail($parent_user_id);

        $chatMessages = Message::where(function ($q) use ($user, $parentUser) {
            $q->where('sender_id', $user->id)->where('receiver_id', $parentUser->id);
        })->orWhere(function ($q) use ($user, $parentUser) {
            $q->where('sender_id', $parentUser->id)->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();

        // Tandai pesan dari orang tua sebagai dibaca
        Message::where('sender_id', $parentUser->id)
            ->where('receiver_id', $user->id)
            ->update(['is_read' => true]);

        return view('mentor.messages.chat', compact('parentUser', 'chatMessages'));
    }

    /**
     * Simpan / Kirim Pesan Balasan
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'student_id' => 'nullable|exists:students,id',
            'message' => 'required|string|min:1|max:2000',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'student_id' => $request->student_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Notifikasi ke Orang Tua
        $mentorName = auth()->user()->name;
        NotificationService::send(
            $request->receiver_id,
            "Pesan Baru dari Pendamping ({$mentorName})",
            Str::limit($request->message, 100),
            NotificationType::INFO,
            route('parent.messages.chat', auth()->user()->mentor?->id ?? 1),
            'chat'
        );

        return redirect()
            ->route('mentor.messages.chat', $request->receiver_id)
            ->with('success', 'Pesan berhasil dikirim kepada Orang Tua.');
    }
}
