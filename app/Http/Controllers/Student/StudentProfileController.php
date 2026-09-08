<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetLog;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $student = $user->student ?? Student::where('user_id', $user->id)->first();

        $totalPoints = $student?->total_points ?? 0;
        $currentStreak = $student?->current_streak ?? 0;
        $programs = $student ? $student->programs : collect();
        $mentor = $student ? $student->getActiveMentor() : null;
        $parent = $student?->parent;

        return view('student.profile.edit', compact(
            'user',
            'student',
            'totalPoints',
            'currentStreak',
            'programs',
            'mentor',
            'parent'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $student = $user->student ?? Student::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'avatar.max' => 'Ukuran foto avatar maksimal adalah 2MB.',
            'avatar.image' => 'Berkas harus berupa gambar yang valid (JPG, PNG, atau WEBP).',
            'avatar.mimes' => 'Format foto avatar harus JPG, JPEG, PNG, atau WEBP.',
        ]);

        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'avatar' => $avatarPath,
        ]);

        if ($student) {
            $student->update([
                'full_name' => $request->name,
                'nickname' => $request->nickname,
            ]);
        }

        return redirect()->back()->with('success', 'Profil santri berhasil diperbarui!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Audit Log
        PasswordResetLog::create([
            'user_id' => $user->id,
            'changed_by' => null,
            'reset_method' => 'self',
            'ip_address' => $request->ip() ?: '127.0.0.1',
            'user_agent' => $request->userAgent(),
            'notification_channel' => 'whatsapp',
            'notification_status' => 'sent',
        ]);

        return redirect()->back()->with('success', 'Alhamdulillah! Password akun santri Anda berhasil diperbarui.');
    }
}
