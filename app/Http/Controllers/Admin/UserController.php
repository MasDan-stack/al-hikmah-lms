<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna dengan filter pencarian & role
     */
    public function index(Request $request): View
    {
        $query = User::with('role')->latest();

        // 1. Filter Pencarian (Nama / Email / No. Telp)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // 2. Filter Role
        if ($request->filled('role')) {
            $roleName = $request->role;
            $query->whereHas('role', function ($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Simpan pengguna baru dan inisialisasi profil peran otomatis
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', Password::defaults()],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role_id' => $validated['role_id'],
                'password' => Hash::make($validated['password']),
            ]);

            // Sinkronisasi profil relasi domain
            $this->syncUserProfile($user);
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru dan profil perannya berhasil dibuat.');
    }

    /**
     * Update data pengguna, ubah role, dan sinkronkan dependensi profil
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', Password::defaults()],
        ]);

        // Anti-Demotion Guard: Admin tidak boleh mencabut role admin dari dirinya sendiri
        if (auth()->id() === $user->id && (int) $user->role_id !== (int) $validated['role_id']) {
            return back()->with('error', 'Anda tidak dapat mencabut hak akses Administrator dari akun Anda sendiri.');
        }

        DB::transaction(function () use ($request, $user, $validated) {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role_id' => $validated['role_id'],
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Sinkronkan profil domain sesuai role baru
            $this->syncUserProfile($user->fresh());
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Data profil dan hak akses pengguna berhasil diperbarui.');
    }

    /**
     * Hapus akun pengguna dengan proteksi Anti-Lockout & Foreign Key Integrity Check
     */
    public function destroy(User $user): RedirectResponse
    {
        // 1. Anti-Self Deletion Guard
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // 2. Proteksi Mentor yang Masih Membina Santri Aktif
        if ($user->mentor && $user->mentor->students()->wherePivot('is_active', true)->exists()) {
            return back()->with('error', 'Mentor tidak dapat dihapus karena masih memiliki santri binaan aktif. Alihkan santri terlebih dahulu.');
        }

        // 3. Proteksi Santri dengan Riwayat Belajar / Transaksi
        if ($user->student && ($user->student->sessions()->exists() || $user->student->payments()->exists() || $user->student->progress()->exists())) {
            return back()->with('error', 'Santri tidak dapat dihapus karena memiliki riwayat sesi bimbingan, pembayaran, atau progres hafalan.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dihapus.');
    }

    /**
     * Helper sinkronisasi profil peran agar selalu tersedia di database
     */
    private function syncUserProfile(User $user): void
    {
        $roleName = strtolower($user->role?->name ?? '');

        if ($roleName === 'mentor') {
            Mentor::updateOrCreate(
                ['user_id' => $user->id],
                ['full_name' => $user->name, 'is_active' => true]
            );
        } elseif ($roleName === 'parent') {
            ParentProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['emergency_phone' => $user->phone]
            );
        } elseif ($roleName === 'student') {
            Student::updateOrCreate(
                ['user_id' => $user->id],
                ['full_name' => $user->name, 'age' => 12]
            );
        }
    }
}
