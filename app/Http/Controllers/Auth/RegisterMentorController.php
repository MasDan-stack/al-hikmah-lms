<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role as RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterMentorController extends Controller
{
    /**
     * Tampilkan halaman pendaftaran pendamping / guru.
     */
    public function create(): View
    {
        return view('bergabung');
    }

    /**
     * Tangani permintaan pendaftaran pendamping / guru baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:30',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $mentorRole = Role::firstOrCreate(
            ['name' => RoleEnum::MENTOR->value],
            ['label' => RoleEnum::MENTOR->label()]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $mentorRole->id,
            'phone' => $request->phone,
        ]);

        Mentor::create([
            'user_id' => $user->id,
            'full_name' => $request->name,
            'specialization' => $request->specialization ?? 'Tahsin & Tajwid Al-Qur\'an',
            'bio' => $request->bio,
            'rating' => 5.0,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('mentor.dashboard');
    }
}
