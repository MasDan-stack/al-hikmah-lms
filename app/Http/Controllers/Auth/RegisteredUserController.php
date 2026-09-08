<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role as RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentAccountService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            'program_id' => 'nullable|exists:programs,id',
            'program' => 'nullable|string|max:100',
            'metode' => 'nullable|string|max:100',
        ]);

        if (! empty($validated['program_id'])) {
            $program = Program::find($validated['program_id']);
            if ($program) {
                $validated['program'] = $program->name;
                $validated['program_name'] = $program->name;
            }
        } elseif (! empty($validated['program'])) {
            $matchedProgram = Program::where('name', 'like', "%{$validated['program']}%")->first();
            if ($matchedProgram) {
                $validated['program_id'] = $matchedProgram->id;
                $validated['program'] = $matchedProgram->name;
                $validated['program_name'] = $matchedProgram->name;
            }
        }

        $rawMetode = strtolower($validated['metode'] ?? 'offline');
        if (str_contains($rawMetode, 'online')) {
            $validated['learning_method'] = 'online';
        } elseif (str_contains($rawMetode, 'hybrid')) {
            $validated['learning_method'] = 'hybrid';
        } else {
            $validated['learning_method'] = 'offline';
        }

        session(['pre_registration' => $validated]);

        return redirect()->route('register');
    }

    /**
     * Handle data awal dari Modal Pendaftaran Program di Halaman Biaya (Step 1)
     */
    public function preRegisterProgram(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nama' => 'required|string|max:255',
            'nama_anak' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'usia' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:L,P',
            'lokasi' => 'required|string|max:255',
            'metode' => 'nullable|string|max:100',
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $validated['program'] = $program->name;
        $validated['program_name'] = $program->name;
        $validated['program_id'] = $program->id;

        $rawMetode = strtolower($validated['metode'] ?? 'offline');
        if (str_contains($rawMetode, 'online')) {
            $validated['learning_method'] = 'online';
        } elseif (str_contains($rawMetode, 'hybrid')) {
            $validated['learning_method'] = 'hybrid';
        } else {
            $validated['learning_method'] = 'offline';
        }

        session(['pre_registration' => $validated]);

        return redirect()->route('register')
            ->with('info', "Anda memilih program: {$program->name}. Silakan lengkapi kata sandi untuk membuat akun.");
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

        $tahfidzProgram = Program::where('name', 'like', '%Tahfidz%')->first();
        if ($tahfidzProgram) {
            $validated['program_id'] = $tahfidzProgram->id;
            $validated['program'] = $tahfidzProgram->name;
            $validated['program_name'] = $tahfidzProgram->name;
        } else {
            $validated['program'] = "Tahfidz Al-Qur'an";
        }
        $validated['program_slug'] = 'tahfidz';
        $validated['is_tahfidz'] = true;

        $rawMetode = strtolower($validated['metode'] ?? 'offline');
        if (str_contains($rawMetode, 'online')) {
            $validated['learning_method'] = 'online';
        } elseif (str_contains($rawMetode, 'hybrid')) {
            $validated['learning_method'] = 'hybrid';
        } else {
            $validated['learning_method'] = 'offline';
        }

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

                $createdStudent = null;

                if (! empty($preRegData)) {
                    $childName = $preRegData['nama_anak'] ?? ('Anak dari '.$user->name);

                    $age = 10;
                    if (! empty($preRegData['usia'])) {
                        $rawUsia = strtolower(trim($preRegData['usia']));
                        if (is_numeric($rawUsia)) {
                            $age = (int) $rawUsia;
                        } elseif (str_contains($rawUsia, 'bawah 10') || str_contains($rawUsia, '< 10') || str_contains($rawUsia, '<10') || str_contains($rawUsia, 'balita') || str_contains($rawUsia, 'paud') || str_contains($rawUsia, 'tk')) {
                            $age = 7; // Di bawah 10 tahun (Wajib Ustazah jika laki-laki)
                        } elseif (str_contains($rawUsia, '10-15') || str_contains($rawUsia, '10 - 15')) {
                            $age = 12; // 10 tahun ke atas
                        } elseif (str_contains($rawUsia, '16-30') || str_contains($rawUsia, '16 - 30')) {
                            $age = 22;
                        } elseif (str_contains($rawUsia, '31-50') || str_contains($rawUsia, '31 - 50')) {
                            $age = 35;
                        } elseif (str_contains($rawUsia, '50+')) {
                            $age = 55;
                        } elseif (preg_match('/(\d+)/', $rawUsia, $matches)) {
                            $age = (int) $matches[1];
                        }
                    }

                    $targetInfo = ! empty($preRegData['target_tahfidz']) ? ' | Target: '.$preRegData['target_tahfidz'] : '';
                    $levelInfo = ! empty($preRegData['level_tahfidz']) ? ' | Level: '.$preRegData['level_tahfidz'] : '';
                    $notes = 'Program Pilihan: '.($preRegData['program'] ?? '-')."{$targetInfo}{$levelInfo} | Metode: ".($preRegData['metode'] ?? '-');

                    // Clean Student Email & Standard Default Password
                    $studentAccountService = app(StudentAccountService::class);
                    $studentEmail = $studentAccountService->generateEmail($childName);
                    $defaultPassword = StudentAccountService::DEFAULT_PASSWORD;

                    $studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => RoleEnum::STUDENT->label()]);
                    $studentUser = User::create([
                        'name' => $childName,
                        'email' => $studentEmail,
                        'password' => Hash::make($defaultPassword),
                        'role_id' => $studentRole->id,
                    ]);

                    $createdStudent = Student::create([
                        'user_id' => $studentUser->id,
                        'parent_id' => $parentProfile->id,
                        'full_name' => $childName,
                        'age' => $age,
                        'gender' => $preRegData['gender'] ?? 'L',
                        'location' => $preRegData['lokasi'] ?? null,
                        'notes' => $notes,
                    ]);
                }

                // Cek apakah ada program yang dipilih di modal pra-registrasi
                $targetProgramId = $preRegData['program_id'] ?? null;
                if (! $targetProgramId && ! empty($preRegData['is_tahfidz'])) {
                    $tahfidzProgram = Program::where('name', 'like', '%Tahfidz%')->first();
                    $targetProgramId = $tahfidzProgram?->id;
                }
                if (! $targetProgramId && ! empty($preRegData['program'])) {
                    $matchedProgram = Program::where('name', 'like', "%{$preRegData['program']}%")->first();
                    $targetProgramId = $matchedProgram?->id;
                }

                $learningMethod = $preRegData['learning_method'] ?? null;
                if (! $learningMethod && ! empty($preRegData['metode'])) {
                    $raw = strtolower($preRegData['metode']);
                    if (str_contains($raw, 'online')) {
                        $learningMethod = 'online';
                    } elseif (str_contains($raw, 'hybrid')) {
                        $learningMethod = 'hybrid';
                    } else {
                        $learningMethod = 'offline';
                    }
                }
                $learningMethod = $learningMethod ?? 'offline';

                session()->forget('pre_registration');

                event(new Registered($user));
                Auth::login($user);

                // Jika sudah memilih program & anak berhasil didaftarkan, lanjutkan ke Langkah 2 (Pilih Hari & Jam)
                if ($targetProgramId && $createdStudent) {
                    return redirect()->route('parent.enrollments.create', [
                        'program_id' => $targetProgramId,
                        'student_id' => $createdStudent->id,
                        'method' => $learningMethod,
                    ])->with('success', "Pendaftaran akun berhasil! Data ananda {$createdStudent->full_name} telah tersimpan. Silakan tentukan preferensi hari dan jam bimbingan untuk menyelesaikan Langkah 2.");
                }

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
