<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ParentProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $parent = $user->parentProfile;

        return view('parent.profile.edit', compact('user', 'parent'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $parent = $user->parentProfile;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'emergency_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($parent) {
            $parent->update([
                'emergency_phone' => $request->emergency_phone,
                'address' => $request->address,
            ]);
        }

        return redirect()->back()->with('success', 'Profil orang tua berhasil diperbarui!');
    }

    public function notifications(): View
    {
        $user = auth()->user();

        return view('parent.profile.notifications', compact('user'));
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        return redirect()->back()->with('success', 'Preferensi notifikasi berhasil disimpan!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password akun berhasil diubah!');
    }

    public function children(): View
    {
        $parent = auth()->user()->parentProfile;
        $children = $parent ? $parent->students()->with(['user', 'mentors.user'])->get() : collect();

        return view('parent.profile.children', compact('children'));
    }

    public function storeChild(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer|min:4|max:25',
            'gender' => 'required|in:L,P',
            'location' => 'nullable|string|max:255',
        ]);

        $parent = auth()->user()->parentProfile;
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Student']);

        $studentUser = User::create([
            'name' => $request->full_name,
            'email' => Str::slug($request->full_name).rand(100, 999).'@alhikmah.com',
            'password' => Hash::make('password'),
            'role_id' => $studentRole->id,
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parent?->id,
            'full_name' => $request->full_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'location' => $request->location,
        ]);

        return redirect()->back()->with('success', 'Data anak baru berhasil ditambahkan!');
    }
}
