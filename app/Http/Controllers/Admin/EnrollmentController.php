<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorActivityLog;
use App\Models\Payment;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnrollmentController extends Controller
{
    /**
     * Menampilkan antrean pendaftaran dengan filter status, pencarian, dan rentang tanggal
     */
    public function index(Request $request): View
    {
        $query = Enrollment::with(['student.parent.user', 'program', 'mentor.user', 'payment'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', fn ($s) => $s->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('program', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $enrollments = $query->paginate(15)->withQueryString();

        $stats = [
            'waiting_admin' => Enrollment::where('status', EnrollmentStatus::WAITING_ADMIN->value)->count(),
            'waiting_parent' => Enrollment::where('status', EnrollmentStatus::WAITING_PARENT->value)->count(),
            'confirmed' => Enrollment::where('status', EnrollmentStatus::CONFIRMED->value)->count(),
            'active' => Enrollment::where('status', EnrollmentStatus::ACTIVE->value)->count(),
        ];

        return view('admin.enrollments.index', compact('enrollments', 'stats'));
    }

    /**
     * Form review dan negosiasi jadwal permohonan
     */
    public function edit(int $id): View
    {
        $enrollment = Enrollment::with(['student.parent.user', 'program', 'mentor'])->findOrFail($id);

        $mentors = Mentor::where('is_active', true)
            ->with(['availabilities', 'students'])
            ->get();

        $days = Enrollment::DAYS;

        return view('admin.enrollments.edit', compact('enrollment', 'mentors', 'days'));
    }

    /**
     * Admin menyetujui jadwal yang diminta oleh orang tua
     */
    public function accept(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'mentor_id' => ['required', 'exists:mentors,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $enrollment = Enrollment::with(['student.parent.user', 'program'])->findOrFail($id);
        $mentor = Mentor::findOrFail($validated['mentor_id']);

        // Verifikasi kuota mentor pada hari yang diminta
        $requestedDays = $enrollment->requested_days ?? ['monday'];
        foreach ($requestedDays as $day) {
            if (! $mentor->hasQuotaOnDay($day)) {
                return back()->withInput()->with('error', "Mentor {$mentor->getDisplayName()} tidak memiliki kuota kosong pada hari ".(Enrollment::DAYS[$day] ?? $day).'. Silakan pilih mentor lain atau tawarkan jadwal alternatif.');
            }
        }

        DB::transaction(function () use ($enrollment, $validated, $mentor) {
            $enrollment->update([
                'mentor_id' => $mentor->id,
                'start_date' => $validated['start_date'],
                'admin_notes' => $validated['admin_notes'] ?? null,
                'status' => EnrollmentStatus::CONFIRMED,
                'confirmed_at' => now(),
            ]);

            // Kunci kuota mentor di mentor_student
            $enrollment->syncToMentorStudent(true);

            // Menerbitkan invoice pembayaran jika belum ada
            if (! $enrollment->payment()->exists()) {
                $programFee = (float) ($enrollment->program_price ?? $enrollment->program?->price ?? 400000);
                $hasPaidReg = $enrollment->student?->hasPaidRegistrationFee() ?? false;
                $registrationFee = $hasPaidReg ? 0.00 : (float) site_setting('registration_fee', 150000);
                $totalAmount = $programFee + $registrationFee;

                Payment::create([
                    'student_id' => $enrollment->student_id,
                    'program_id' => $enrollment->program_id,
                    'enrollment_id' => $enrollment->id,
                    'program_fee' => $programFee,
                    'registration_fee' => $registrationFee,
                    'amount' => $totalAmount,
                    'payment_purpose' => 'registration',
                    'due_date' => Carbon::parse($validated['start_date'])->subDay()->toDateString(),
                    'status' => 'pending',
                    'invoice_number' => 'INV-'.date('Ymd').'-'.str_pad((string) $enrollment->id, 4, '0', STR_PAD_LEFT),
                ]);
            }

            // Catat log aktivitas mentor
            MentorActivityLog::log(
                $mentor->id,
                'enrollment_accepted',
                "Admin mengkonfirmasi jadwal pendaftaran #{$enrollment->id} santri {$enrollment->student->getDisplayName()} untuk mentor {$mentor->getDisplayName()}."
            );

            // Notifikasi ke Orang Tua via NotificationService
            if ($enrollment->student?->parent?->user_id) {
                NotificationService::send(
                    $enrollment->student->parent->user_id,
                    'Jadwal Pendaftaran Disetujui!',
                    "Jadwal belajar program {$enrollment->program->name} untuk {$enrollment->student->getDisplayName()} telah disetujui bersama {$mentor->getDisplayName()}. Silakan lakukan pembayaran tagihan.",
                    NotificationType::SUCCESS,
                    route('parent.enrollments.show', $enrollment->id),
                    'enrollment',
                    true
                );
            }
        });

        return redirect()->route('admin.enrollments.index')
            ->with('success', "Permohonan pendaftaran #{$enrollment->id} berhasil disetujui dan invoice telah diterbitkan.");
    }

    /**
     * Admin memberikan penawaran alternatif jadwal (Counter-Offer)
     */
    public function offerAlternative(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'mentor_id' => ['nullable', 'exists:mentors,id'],
            'offered_days' => ['required', 'array', 'min:1'],
            'offered_days.*' => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'offered_time' => ['nullable', 'date_format:H:i'],
            'admin_notes' => ['required', 'string', 'max:500'],
        ]);

        $enrollment = Enrollment::with(['student.parent.user', 'program'])->findOrFail($id);

        $enrollment->update([
            'mentor_id' => $validated['mentor_id'] ?? null,
            'offered_days' => $validated['offered_days'],
            'offered_time' => $validated['offered_time'] ?? null,
            'admin_notes' => $validated['admin_notes'],
            'status' => EnrollmentStatus::WAITING_PARENT,
        ]);

        // Notifikasi ke Orang Tua via NotificationService
        if ($enrollment->student?->parent?->user_id) {
            NotificationService::send(
                $enrollment->student->parent->user_id,
                'Tawaran Alternatif Jadwal Belajar',
                "Lembaga memberikan alternatif jadwal untuk program {$enrollment->program->name}. Silakan tinjau dan konfirmasi di portal Anda.",
                NotificationType::WARNING,
                route('parent.enrollments.show', $enrollment->id),
                'enrollment',
                true
            );
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', "Alternatif jadwal untuk permohonan #{$enrollment->id} berhasil dikirim ke Orang Tua.");
    }

    /**
     * Konfirmasi masal beberapa pendaftaran (Batch Confirm)
     */
    public function bulkAccept(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enrollment_ids' => ['required', 'array', 'min:1'],
            'enrollment_ids.*' => ['exists:enrollments,id'],
        ]);

        $count = 0;
        DB::transaction(function () use ($validated, &$count) {
            $enrollments = Enrollment::whereIn('id', $validated['enrollment_ids'])
                ->where('status', EnrollmentStatus::WAITING_ADMIN->value)
                ->whereNotNull('mentor_id')
                ->get();

            foreach ($enrollments as $enrollment) {
                $enrollment->update([
                    'status' => EnrollmentStatus::CONFIRMED,
                    'confirmed_at' => now(),
                    'start_date' => $enrollment->start_date ?? now()->addDays(7)->toDateString(),
                ]);

                $enrollment->syncToMentorStudent(true);

                if (! $enrollment->payment()->exists()) {
                    $programFee = (float) ($enrollment->program_price ?? $enrollment->program?->price ?? 400000);
                    $hasPaidReg = $enrollment->student?->hasPaidRegistrationFee() ?? false;
                    $registrationFee = $hasPaidReg ? 0.00 : (float) site_setting('registration_fee', 150000);
                    $totalAmount = $programFee + $registrationFee;

                    Payment::create([
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
                }
                $count++;
            }
        });

        return back()->with('success', "Sebanyak {$count} permohonan pendaftaran berhasil disetujui secara masal.");
    }

    /**
     * Admin membatalkan permohonan pendaftaran
     */
    public function cancel(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ]);

        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update([
            'status' => EnrollmentStatus::CANCELLED,
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.enrollments.index')
            ->with('warning', "Permohonan pendaftaran #{$enrollment->id} telah dibatalkan.");
    }

    /**
     * Export data pendaftaran ke format CSV / Excel
     */
    public function export(Request $request): StreamedResponse
    {
        $fileName = 'data-pendaftaran-alhikmah-'.date('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM untuk Excel

            fputcsv($handle, [
                'ID Permohonan',
                'Nama Santri',
                'Nama Orang Tua / Wali',
                'Nomor WhatsApp',
                'Program Belajar',
                'Guru Pembimbing / Mentor',
                'Hari Belajar',
                'Jam Belajar',
                'Status Pendaftaran',
                'Status Pembayaran',
                'Tanggal Pengajuan',
                'Tanggal Mulai Belajar',
            ]);

            $enrollments = Enrollment::with(['student.parent.user', 'program', 'mentor.user', 'payment'])
                ->latest()
                ->get();

            foreach ($enrollments as $row) {
                fputcsv($handle, [
                    '#ENR-'.str_pad($row->id, 5, '0', STR_PAD_LEFT),
                    $row->student?->getDisplayName() ?? '-',
                    $row->student?->parent_name ?? '-',
                    $row->student?->getParentPhone() ?? '-',
                    $row->program?->name ?? '-',
                    $row->mentor?->getDisplayName() ?? 'Belum ditentukan',
                    $row->effective_days_label,
                    $row->effective_time_label,
                    $row->status->label(),
                    $row->payment?->status ? strtoupper($row->payment->status) : 'BELUM TERBIT',
                    $row->created_at->format('d/m/Y H:i'),
                    $row->start_date_label,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
