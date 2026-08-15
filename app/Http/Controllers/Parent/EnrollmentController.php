<?php

namespace App\Http\Controllers\Parent;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Program;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Tampilkan daftar seluruh permohonan pendaftaran milik orang tua
     */
    public function index(Request $request): View
    {
        $parentProfile = auth()->user()->parentProfile;
        $studentIds = $parentProfile ? $parentProfile->students()->pluck('id') : collect();

        $query = Enrollment::whereIn('student_id', $studentIds)
            ->with(['student', 'program', 'mentor.user', 'payment'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->paginate(10)->withQueryString();

        return view('parent.enrollments.index', compact('enrollments'));
    }

    /**
     * Form pengajuan pendaftaran program + pemilihan hari & jam
     */
    public function create(Request $request): View|RedirectResponse
    {
        $programId = $request->query('program_id');
        $program = Program::where('is_active', true)->findOrFail($programId);

        $parentProfile = auth()->user()->parentProfile;
        $children = $parentProfile ? $parentProfile->students()->with('programs')->get() : collect();

        if ($children->isEmpty()) {
            return redirect()->route('parent.profile.children')
                ->with('warning', 'Silakan daftarkan data anak binaan Anda terlebih dahulu sebelum memilih jadwal program.');
        }

        $availableDays = Enrollment::DAYS;

        return view('parent.enrollments.create', compact('program', 'children', 'availableDays'));
    }

    /**
     * Simpan permohonan pendaftaran baru dengan snapshot harga
     */
    public function store(Request $request): RedirectResponse
    {
        $parentProfile = auth()->user()->parentProfile;
        if (! $parentProfile) {
            return redirect()->route('parent.dashboard')->with('error', 'Profil orang tua tidak ditemukan.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'learning_method' => ['nullable', 'in:offline,online,hybrid'],
            'requested_days' => ['required', 'array', 'min:1'],
            'requested_days.*' => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'requested_time' => ['nullable', 'date_format:H:i'],
            'parent_notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Verifikasi kepemilikan data anak
        $student = $parentProfile->students()->where('id', $validated['student_id'])->firstOrFail();
        $program = Program::findOrFail($validated['program_id']);

        // Cek duplikasi pendaftaran aktif
        $duplicate = Enrollment::where('student_id', $student->id)
            ->where('program_id', $program->id)
            ->whereIn('status', [
                EnrollmentStatus::WAITING_ADMIN->value,
                EnrollmentStatus::WAITING_PARENT->value,
                EnrollmentStatus::CONFIRMED->value,
                EnrollmentStatus::ACTIVE->value,
            ])
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'Santri ini sudah memiliki pendaftaran yang sedang berjalan untuk program yang sama.');
        }

        $sessionMethod = session('pre_registration.metode', 'offline');
        if (stripos($sessionMethod, 'online') !== false) {
            $sessionMethod = 'online';
        } elseif (stripos($sessionMethod, 'hybrid') !== false) {
            $sessionMethod = 'hybrid';
        } else {
            $sessionMethod = 'offline';
        }

        $learningMethod = $validated['learning_method'] ?? $sessionMethod;

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'program_price' => $program->price, // Snapshot harga terkunci
            'learning_method' => $learningMethod,
            'requested_days' => $validated['requested_days'],
            'requested_time' => $validated['requested_time'] ?? null,
            'parent_notes' => $validated['parent_notes'] ?? null,
            'status' => EnrollmentStatus::WAITING_ADMIN,
        ]);

        // Notifikasi ke seluruh Admin via NotificationService terpusat
        NotificationService::notifyAdmins(
            'Permohonan Jadwal Belajar Baru',
            "Permohonan pendaftaran program {$program->name} untuk santri {$student->getDisplayName()} telah masuk.",
            NotificationType::INFO,
            route('admin.enrollments.edit', $enrollment->id),
            'enrollment'
        );

        return redirect()->route('parent.enrollments.show', $enrollment->id)
            ->with('success', 'Permohonan pendaftaran & pilihan jadwal berhasil diajukan. Pengelola lembaga akan mereview jadwal Anda.');
    }

    /**
     * Tampilkan detail permohonan pendaftaran & aksi negosiasi
     */
    public function show(int $id): View
    {
        $parentProfile = auth()->user()->parentProfile;
        $studentIds = $parentProfile ? $parentProfile->students()->pluck('id') : collect();

        $enrollment = Enrollment::whereIn('student_id', $studentIds)
            ->with(['student', 'program', 'mentor.user', 'payment'])
            ->findOrFail($id);

        return view('parent.enrollments.show', compact('enrollment'));
    }

    /**
     * Wali Santri menerima penawaran jadwal alternatif dari Admin
     */
    public function acceptOffer(int $id): RedirectResponse
    {
        $parentProfile = auth()->user()->parentProfile;
        $studentIds = $parentProfile ? $parentProfile->students()->pluck('id') : collect();

        $enrollment = Enrollment::whereIn('student_id', $studentIds)
            ->where('status', EnrollmentStatus::WAITING_PARENT->value)
            ->findOrFail($id);

        DB::transaction(function () use ($enrollment) {
            $enrollment->update([
                'status' => EnrollmentStatus::CONFIRMED,
                'confirmed_at' => now(),
            ]);

            // Menerbitkan invoice pembayaran otomatis
            $this->createEnrollmentInvoice($enrollment);
        });

        return redirect()->route('parent.enrollments.show', $enrollment->id)
            ->with('success', 'Jadwal alternatif berhasil disepakati! Invoice tagihan pendaftaran telah diterbitkan.');
    }

    /**
     * Wali Santri menolak penawaran jadwal alternatif dan meminta jadwal lain
     */
    public function rejectOffer(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $parentProfile = auth()->user()->parentProfile;
        $studentIds = $parentProfile ? $parentProfile->students()->pluck('id') : collect();

        $enrollment = Enrollment::whereIn('student_id', $studentIds)
            ->where('status', EnrollmentStatus::WAITING_PARENT->value)
            ->findOrFail($id);

        $reason = $validated['rejection_reason'] ? " (Catatan Wali: {$validated['rejection_reason']})" : '';

        $enrollment->update([
            'status' => EnrollmentStatus::WAITING_ADMIN,
            'parent_notes' => ($enrollment->parent_notes ? $enrollment->parent_notes."\n" : '')."[Wali Santri meminta alternatif jadwal lain{$reason}]",
        ]);

        return redirect()->route('parent.enrollments.show', $enrollment->id)
            ->with('warning', 'Penolakan jadwal telah dikirimkan ke Admin untuk dicarikan alternatif jadwal lain.');
    }

    /**
     * Helper privat membuat record tagihan pendaftaran dengan rincian biaya
     */
    private function createEnrollmentInvoice(Enrollment $enrollment): void
    {
        if ($enrollment->payment()->exists()) {
            return;
        }

        $programFee = (float) ($enrollment->program_price ?? $enrollment->program?->price ?? 400000);
        $hasPaidReg = $enrollment->student?->hasPaidRegistrationFee() ?? false;
        $registrationFee = $hasPaidReg ? 0.00 : (float) site_setting('registration_fee', 150000);
        $totalAmount = $programFee + $registrationFee;

        $payment = Payment::create([
            'student_id' => $enrollment->student_id,
            'program_id' => $enrollment->program_id,
            'enrollment_id' => $enrollment->id,
            'program_fee' => $programFee,
            'registration_fee' => $registrationFee,
            'amount' => $totalAmount,
            'payment_purpose' => 'registration',
            'due_date' => now()->addDays(3)->toDateString(),
            'status' => 'pending',
            'invoice_number' => 'INV-'.date('Ymd').'-'.str_pad((string) $enrollment->id, 4, '0', STR_PAD_LEFT),
        ]);

        NotificationService::send(
            auth()->id(),
            "Tagihan Pendaftaran: {$enrollment->program->name}",
            'Jadwal belajar telah disepakati. Tagihan pendaftaran sebesar Rp '.number_format($payment->amount, 0, ',', '.').' telah siap untuk dibayarkan.',
            NotificationType::WARNING,
            route('parent.enrollments.show', $enrollment->id),
            'payment'
        );
    }
}
