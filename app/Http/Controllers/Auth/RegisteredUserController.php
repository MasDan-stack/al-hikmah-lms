<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role as RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Handle data awal dari Modal Pendaftaran Umum (Step 1)
     */
    public function preRegister(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_anak' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'usia' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:L,P',
            'lokasi' => 'required|string|max:255',
            'program' => 'nullable|string|max:100',
            'metode' => 'nullable|string|max:100',
        ]);

        session(['pre_registration' => $validated]);

        return redirect()->route('register');
    }

    /**
     * Handle data awal dari Modal Pendaftaran Khusus Tahfidz
     */
    public function preRegisterTahfidz(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_anak' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'usia' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:L,P',
            'lokasi' => 'required|string|max:255',
            'target_tahfidz' => 'nullable|string|max:100',
            'level_tahfidz' => 'nullable|string|max:100',
            'metode' => 'nullable|string|max:100',
        ]);

        $validated['program'] = "Tahfidz Al-Qur'an";
        $validated['program_slug'] = 'tahfidz';
        $validated['is_tahfidz'] = true;

        session(['pre_registration' => $validated]);

        return redirect()->route('register');
    }

    /**
     * Show the registration page (Step 2).
     */
    public function create(): View
    {
        $preRegData = session('pre_registration', []);

        return view('auth.register', compact('preRegData'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => 'nullable|string|in:parent,student',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:30',
        ]);

        $preRegData = session('pre_registration', []);

        $selectedRole = $request->input('role', 'parent');
        $selectedRoleKey = $selectedRole === 'student' ? RoleEnum::STUDENT : RoleEnum::PARENT;

        return DB::transaction(function () use ($request, $selectedRole, $selectedRoleKey, $preRegData) {
            $targetRole = Role::firstOrCreate(
                ['name' => $selectedRoleKey->value],
                ['label' => $selectedRoleKey->label()]
            );

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $targetRole->id,
                'phone' => $request->phone ?? ($preRegData['whatsapp'] ?? null),
            ]);

            if ($selectedRole === 'parent') {
                $parentProfile = ParentProfile::create([
                    'user_id' => $user->id,
                    'address' => $preRegData['lokasi'] ?? null,
                    'emergency_phone' => $request->phone ?? ($preRegData['whatsapp'] ?? null),
                ]);

                if (! empty($preRegData)) {
                    $childName = $preRegData['nama_anak'] ?? ('Anak dari '.$user->name);

                    $age = 10;
                    if (! empty($preRegData['usia']) && preg_match('/^(\d+)/', $preRegData['usia'], $matches)) {
                        $age = (int) $matches[1];
                    }

                    $targetInfo = ! empty($preRegData['target_tahfidz']) ? ' | Target: '.$preRegData['target_tahfidz'] : '';
                    $levelInfo = ! empty($preRegData['level_tahfidz']) ? ' | Level: '.$preRegData['level_tahfidz'] : '';
                    $notes = 'Program Pilihan: '.($preRegData['program'] ?? '-')."{$targetInfo}{$levelInfo} | Metode: ".($preRegData['metode'] ?? '-');

                    // Unique Student Email Generator
                    $baseSlug = Str::slug($childName);
                    $studentEmail = $baseSlug.'.'.Str::random(5).'@alhikmah.com';
                    while (User::where('email', $studentEmail)->exists()) {
                        $studentEmail = $baseSlug.'.'.Str::random(5).'@alhikmah.com';
                    }

                    // Random Secure Student Password Generator
                    $randomPassword = Str::random(10);

                    $studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => RoleEnum::STUDENT->label()]);
                    $studentUser = User::create([
                        'name' => $childName,
                        'email' => $studentEmail,
                        'password' => Hash::make($randomPassword),
                        'role_id' => $studentRole->id,
                    ]);

                    $student = Student::create([
                        'user_id' => $studentUser->id,
                        'parent_id' => $parentProfile->id,
                        'full_name' => $childName,
                        'age' => $age,
                        'gender' => $preRegData['gender'] ?? 'L',
                        'location' => $preRegData['lokasi'] ?? null,
                        'notes' => $notes,
                    ]);

                    // Attach to Program Tahfidz if available
                    if (! empty($preRegData['is_tahfidz'])) {
                        $tahfidzProgram = Program::where('name', 'like', '%Tahfidz%')->first();
                        if ($tahfidzProgram) {
                            $student->programs()->syncWithoutDetaching([$tahfidzProgram->id => ['status' => 'active', 'enrolled_at' => now()]]);
                        }
                    }
                }

                session()->forget('pre_registration');

                event(new Registered($user));
                Auth::login($user);

                return redirect()->route('parent.dashboard');
            }

            Student::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'age' => 10,
                'gender' => 'L',
            ]);

            session()->forget('pre_registration');

            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('student.dashboard');
        });
    }
}
