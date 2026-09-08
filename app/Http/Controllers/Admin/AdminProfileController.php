<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'avatar.max' => 'Ukuran foto profil maksimal adalah 2MB.',
            'avatar.image' => 'Berkas harus berupa gambar yang valid (JPG, PNG, atau WEBP).',
            'avatar.mimes' => 'Format foto profil harus JPG, JPEG, PNG, atau WEBP.',
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
            'email' => $request->email,
            'phone' => $request->phone,
            'avatar' => $avatarPath,
        ]);

        return redirect()->back()->with('success', 'Profil administrator berhasil diperbarui!');
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

        return redirect()->back()->with('success', 'Password administrator berhasil diperbarui!');
    }
}
