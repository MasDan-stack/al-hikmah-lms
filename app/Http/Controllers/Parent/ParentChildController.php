<?php

namespace App\Http\Controllers\Parent;

use App\Enums\Role as RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Progress;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ParentChildController extends Controller
{
    public function index(): View
    {
        $parent = auth()->user()->parentProfile;
        $children = $parent
            ? $parent->students()->with(['user', 'mentors.user'])->get()
            : collect();

        return view('parent.children.index', compact('children'));
    }

    public function show(int $id): View
    {
        $parent = auth()->user()->parentProfile;
        $child = Student::with(['user', 'mentors.user'])->findOrFail($id);

        if (! $parent || $child->parent_id !== $parent->id) {
            abort(403, 'Akses data anak ditolak.');
        }

        $progresses = Progress::with(['mentor.user', 'session'])
            ->where('student_id', $child->id)
            ->latest()
            ->get();

        // Data Grafik Bulanan untuk anak ini (6 bulan terakhir)
        $chartLabels = [];
        $chartProgressCounts = [];
        $chartAvgTajwid = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $chartLabels[] = $monthDate->translatedFormat('M Y');

            $monthProgress = Progress::where('student_id', $child->id)
                ->whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month);

            $chartProgressCounts[] = (clone $monthProgress)->count();
            $chartAvgTajwid[] = round((clone $monthProgress)->avg('nilai_tajwid') ?? 0, 1);
        }

        return view('parent.children.show', compact(
            'child',
            'progresses',
            'chartLabels',
            'chartProgressCounts',
            'chartAvgTajwid'
        ));
    }

    public function exportReport(int $id)
    {
        $parent = auth()->user()->parentProfile;
        $child = Student::with(['user', 'mentors.user'])->findOrFail($id);

        if (! $parent || $child->parent_id !== $parent->id) {
            abort(403, 'Akses data anak ditolak.');
        }

        $progresses = Progress::with(['mentor.user'])
            ->where('student_id', $child->id)
            ->latest()
            ->get();

        $avgTajwid = round($progresses->avg('nilai_tajwid') ?? 0, 1);
        $avgFluent = round($progresses->avg('nilai_fluent') ?? 0, 1);

        return view('parent.children.report', compact('child', 'progresses', 'avgTajwid', 'avgFluent'));
    }

    public function enrollTahfidz(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|string',
            'new_nama_anak' => 'required_if:student_id,new|nullable|string|max:255',
            'new_usia' => 'nullable|integer|min:3|max:80',
            'new_gender' => 'nullable|string|in:L,P',
            'target_tahfidz' => 'required|string|max:100',
            'level_tahfidz' => 'nullable|string|max:100',
            'metode' => 'nullable|string|max:100',
        ]);

        $parent = auth()->user()->parentProfile;
        if (! $parent) {
            return back()->with('error', 'Profil orang tua tidak ditemukan.');
        }

        $notes = "Program Pilihan: Tahfidz Al-Qur'an | Target: {$validated['target_tahfidz']} | Level: ".($validated['level_tahfidz'] ?? '-').' | Metode: '.($validated['metode'] ?? '-');

        if ($validated['student_id'] === 'new') {
            $childName = $validated['new_nama_anak'];
            $baseSlug = Str::slug($childName);
            $studentEmail = $baseSlug.'.'.Str::random(5).'@alhikmah.com';
            while (User::where('email', $studentEmail)->exists()) {
                $studentEmail = $baseSlug.'.'.Str::random(5).'@alhikmah.com';
            }

            $studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => RoleEnum::STUDENT->label()]);
            $studentUser = User::create([
                'name' => $childName,
                'email' => $studentEmail,
                'password' => Hash::make(Str::random(10)),
                'role_id' => $studentRole->id,
            ]);

            $student = Student::create([
                'user_id' => $studentUser->id,
                'parent_id' => $parent->id,
                'full_name' => $childName,
                'age' => $validated['new_usia'] ?? 10,
                'gender' => $validated['new_gender'] ?? 'L',
                'location' => $parent->address ?? 'Indonesia',
                'notes' => $notes,
            ]);
        } else {
            $student = Student::where('parent_id', $parent->id)->findOrFail($validated['student_id']);
            $existingNotes = $student->notes ? $student->notes.' | ' : '';
            $student->update(['notes' => $existingNotes.$notes]);
        }

        // Attach to Program Tahfidz if available
        $tahfidzProgram = Program::where('name', 'like', '%Tahfidz%')->first();
        if ($tahfidzProgram) {
            $student->programs()->syncWithoutDetaching([$tahfidzProgram->id => ['status' => 'active', 'enrolled_at' => now()]]);
        }

        return redirect()->route('parent.children.index')
            ->with('success', "Pendaftaran Program Tahfidz untuk ananda {$student->getDisplayName()} berhasil! Admin akan segera mengalokasikan pengajar.");
    }
}
