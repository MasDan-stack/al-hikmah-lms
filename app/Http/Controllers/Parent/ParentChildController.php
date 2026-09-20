<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parent\EnrollTahfidzRequest;
use App\Models\PasswordResetLog;
use App\Models\Program;
use App\Models\Progress;
use App\Models\Student;
use App\Services\EmailService;
use App\Services\StudentAccountService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ParentChildController extends Controller
{
    public function __construct(
        protected StudentAccountService $studentAccountService,
        protected WhatsAppService $whatsAppService,
        protected EmailService $emailService
    ) {}

    public function index(): View
    {
        $parent = auth()->user()->parentProfile;
        $children = $parent
            ? $parent->students()->with(['user', 'mentors.user', 'earnedBadges'])->get()
            : collect();

        return view('parent.children.index', compact('children'));
    }

    public function show(Student $student): View
    {
        $this->authorize('view', $student);

        $child = $student->loadMissing(['user', 'mentors.user', 'earnedBadges', 'juzProgress']);

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

    public function exportReport(Student $student)
    {
        $this->authorize('exportReport', $student);

        $child = $student->loadMissing(['user', 'mentors.user']);

        $progresses = Progress::with(['mentor.user'])
            ->where('student_id', $child->id)
            ->latest()
            ->get();

        $avgTajwid = round($progresses->avg('nilai_tajwid') ?? 0, 1);
        $avgFluent = round($progresses->avg('nilai_fluent') ?? 0, 1);

        return view('parent.children.report', compact('child', 'progresses', 'avgTajwid', 'avgFluent'));
    }

    public function enrollTahfidz(EnrollTahfidzRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $parent = auth()->user()->parentProfile;
        if (! $parent) {
            return back()->with('error', 'Profil orang tua tidak ditemukan.');
        }

        $notes = "Program Pilihan: Tahfidz Al-Qur'an | Target: {$validated['target_tahfidz']} | Level: ".($validated['level_tahfidz'] ?? '-').' | Metode: '.($validated['metode'] ?? '-');

        if ($validated['student_id'] === 'new') {
            $result = $this->studentAccountService->createStudentAccount(
                $parent,
                $validated['new_nama_anak'],
                $validated['new_usia'] ?? 10,
                $validated['new_gender'] ?? 'L',
                $parent->address ?? 'Indonesia',
                $notes
            );
            $student = $result['student'];
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

    /**
     * Fitur Reset & Kirim Password Akun Santri oleh Orang Tua
     */
    public function requestPasswordReset(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('resetPassword', $student);

        $child = $student->loadMissing('user');

        $newPassword = $this->studentAccountService->generatePassword(8);
        $child->user->update([
            'password' => Hash::make($newPassword),
        ]);

        $parentUser = auth()->user();
        $parentPhone = $parent->emergency_phone ?: $parentUser->phone;
        $parentEmail = $parentUser->email;

        // Pesan kredensial baru
        $message = "Assalamu'alaikum Wr. Wb. Bapak/Ibu {$parentUser->name},\n\n"
            ."Permintaan reset password akun santri untuk ananda *{$child->getDisplayName()}* telah berhasil diproses.\n\n"
            ."Detail Akun Terbaru:\n"
            ."📧 Email: {$child->user->email}\n"
            ."🔑 Password Baru: {$newPassword}\n"
            .'🌐 URL Login: '.url('/login')."\n\n"
            .'Silakan dampingi ananda saat login kembali.';

        $channel = 'whatsapp';
        $status = 'sent';

        $waSuccess = false;
        if ($parentPhone) {
            $waSuccess = $this->whatsAppService->sendMessage($parentPhone, $message);
        }

        if (! $waSuccess) {
            if ($parentEmail) {
                $channel = 'email';
                $emailSuccess = $this->emailService->sendRawEmail(
                    $parentEmail,
                    'Password Baru Santri AL-HIKMAH LMS - '.$child->getDisplayName(),
                    nl2br($message)
                );
                $status = $emailSuccess ? 'fallback' : 'failed';
            } else {
                $channel = 'inapp';
                $status = 'failed';
            }
        }

        // Audit log
        PasswordResetLog::create([
            'user_id' => $child->user_id,
            'changed_by' => $parentUser->id,
            'reset_method' => 'parent',
            'ip_address' => $request->ip() ?: '127.0.0.1',
            'user_agent' => $request->userAgent(),
            'notification_channel' => $channel,
            'notification_status' => $status,
        ]);

        if ($status === 'failed') {
            return back()->with('warning', 'Password anak berhasil direset, namun pengiriman via WhatsApp/Email gagal. Silakan hubungi admin lembaga.');
        }

        $channelName = $channel === 'whatsapp' ? 'WhatsApp' : 'Email';

        return back()->with('success', "Password baru untuk ananda {$child->getDisplayName()} telah berhasil dibuat dan dikirimkan ke {$channelName} Anda.");
    }
}
