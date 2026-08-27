<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentPasswordController extends Controller
{
    public function show(): View
    {
        return view('student.password.index');
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*]/',
            ],
        ], [
            'new_password.regex' => 'Password baru harus mengandung kombinasi huruf besar, huruf kecil, angka, dan simbol (!@#$%^&*).',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
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

        return back()->with('success', 'Alhamdulillah! Password akun Anda berhasil diperbarui.');
    }
}
