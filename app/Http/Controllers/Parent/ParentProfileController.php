<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'phone' => 'nullable|string|max:20',
            'emergency_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'maps_link' => 'nullable|url|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'maps_link.url' => 'Format tautan peta tidak valid. Pastikan diawali dengan http:// atau https://',
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

        $phone = $request->phone ?? $request->emergency_phone;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $phone,
            'avatar' => $avatarPath,
        ]);

        if ($parent) {
            $parent->update([
                'phone' => $phone,
                'emergency_phone' => $request->emergency_phone ?? $phone,
                'address' => $request->address,
                'maps_link' => $request->maps_link,
            ]);
        } else {
            ParentProfile::create([
                'user_id' => $user->id,
                'phone' => $phone,
                'address' => $request->address,
                'maps_link' => $request->maps_link,
                'emergency_phone' => $request->emergency_phone ?? $phone,
            ]);
        }

        return redirect()->back()->with('success', 'Profil orang tua dan titik lokasi rumah berhasil diperbarui!');
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
            'age' => 'required|integer|min:3|max:25',
            'gender' => 'required|in:L,P',
            'location' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $parent = $user->parentProfile;
        if (! $parent) {
            $parent = ParentProfile::create([
                'user_id' => $user->id,
                'address' => $request->location,
                'emergency_phone' => $user->phone,
            ]);
        }

        $studentRole = Role::firstOrCreate(
            ['name' => \App\Enums\Role::STUDENT->value],
            ['label' => \App\Enums\Role::STUDENT->label()]
        );

        $studentAccountService = app(StudentAccountService::class);
        $studentEmail = $studentAccountService->generateEmail($request->full_name);
        $defaultPassword = StudentAccountService::DEFAULT_PASSWORD;

        $studentUser = User::create([
            'name' => $request->full_name,
            'email' => $studentEmail,
            'password' => Hash::make($defaultPassword),
            'role_id' => $studentRole->id,
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parent->id,
            'full_name' => $request->full_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'location' => $request->location ?? $parent->address ?? 'Indonesia',
        ]);

        return redirect()->route('parent.profile.children')
            ->with('success', "Data ananda ({$request->full_name}) berhasil ditambahkan dengan email login: {$studentEmail} dan password default: {$defaultPassword}. Disarankan segera mengganti password demi keamanan.");
    }
}
