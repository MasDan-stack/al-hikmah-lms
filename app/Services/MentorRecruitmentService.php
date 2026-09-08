<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\Mentor;
use App\Models\MentorApplication;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MentorRecruitmentService
{
    public function __construct(
        protected WhatsAppService $whatsAppService,
        protected MentorAccountService $mentorAccountService,
        protected MentorTestService $mentorTestService
    ) {}

    public function submitApplication(array $data): MentorApplication
    {
        return DB::transaction(function () use ($data) {
            $applicationCode = 'APP-'.date('Ym').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

            // 1. Dapatkan atau Buat User Akun Calon Guru
            $mentorRole = Role::firstOrCreate(
                ['name' => 'mentor'],
                ['label' => 'Mentor / Guru']
            );

            $user = User::where('email', $data['email'])->first();
            $plainPassword = $data['password'] ?? null;

            if (! $user) {
                $user = User::create([
                    'name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => $plainPassword ? Hash::make($plainPassword) : Hash::make('password123'),
                    'role_id' => $mentorRole->id,
                ]);
            } else {
                if ($plainPassword) {
                    $user->update([
                        'password' => Hash::make($plainPassword),
                        'role_id' => $mentorRole->id,
                        'name' => $data['full_name'],
                        'phone' => $data['phone'] ?? $user->phone,
                    ]);
                }
            }

            // 2. Buat Data Lamaran Calon Guru
            $applicationData = collect($data)
                ->except(['password', 'password_confirmation', 'cv', 'certificate', 'documents'])
                ->toArray();

            $application = MentorApplication::create(array_merge($applicationData, [
                'user_id' => $user->id,
                'application_code' => $applicationCode,
                'status' => 'submitted',
                'current_stage' => 1,
                'submitted_at' => now(),
            ]));

            // 3. Buat Profil Mentor Awal (Mode Seleksi / Belum Aktif)
            $mentor = Mentor::where('user_id', $user->id)->first();
            $mentorData = [
                'application_id' => $application->id,
                'full_name' => $application->full_name,
                'birth_date' => $application->birth_date,
                'gender' => $application->gender === 'female' ? 'P' : 'L',
                'address' => $application->address,
                'city' => $application->city,
                'education' => $application->education,
                'institution' => $application->institution,
                'experience_years' => $application->experience_years,
                'hifz_total_juz' => $application->hifz_total_juz,
                'specialization' => $application->specialization ?? 'Tahfidz',
                'sanad_chain' => $application->sanad_chain,
                'bio' => $application->experience_description ?? 'Calon Guru Pembimbing Al-Qur\'an',
            ];

            if (! $mentor) {
                $mentor = Mentor::create(array_merge($mentorData, [
                    'user_id' => $user->id,
                    'rating' => 5.00,
                    'is_active' => false,
                    'status' => 'inactive',
                ]));
            } else {
                $mentor->update($mentorData);
            }

            FinancialAuditLog::log(
                userId: $user->id,
                action: 'mentor_application_submitted',
                entityType: 'mentor_application',
                entityId: $application->id,
                oldValues: null,
                newValues: ['status' => 'submitted', 'code' => $applicationCode]
            );

            // 4. Login otomatis calon guru ke Dashboard
            if (! Auth::check()) {
                Auth::login($user);
            }

            return $application;
        });
    }

    public function processDocumentReview(MentorApplication $application, bool $isApproved = true, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($application, $isApproved, $notes) {
            $oldStatus = $application->status;
            $newStatus = $isApproved ? 'document_review' : 'rejected';
            $stage = $isApproved ? 2 : $application->current_stage;

            $application->update([
                'status' => $newStatus,
                'current_stage' => $stage,
                'admin_notes' => $notes ?? $application->admin_notes,
            ]);

            FinancialAuditLog::log(
                userId: auth()->id(),
                action: 'mentor_document_review',
                entityType: 'mentor_application',
                entityId: $application->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => $newStatus, 'notes' => $notes]
            );

            return true;
        });
    }

    public function approveDocumentAndScheduleTest(MentorApplication $application, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($application, $notes) {
            $oldStatus = $application->status;

            // Generate test session with 15 questions
            // MentorTestService::generateTest automatically sets status to 'test_scheduled' and current_stage to 3
            $this->mentorTestService->generateTest($application, [
                'count' => 15,
            ]);

            if ($notes) {
                $application->update(['admin_notes' => $notes]);
            }

            FinancialAuditLog::log(
                userId: auth()->id() ?? $application->user_id,
                action: 'mentor_document_approved_test_scheduled',
                entityType: 'mentor_application',
                entityId: $application->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => 'test_scheduled', 'stage' => 3, 'notes' => $notes]
            );

            // Fail-safe WhatsApp Notification ke Calon Guru
            if ($application->phone) {
                try {
                    $waMessage = "Assalamu'alaikum Wr. Wb. Ustadz/Ustadzah *{$application->full_name}*,\n\n"
                        ."Alhamdulillah berkas administrasi dan portofolio Anda telah kami verifikasi dan *Disetujui*.\n\n"
                        ."Sesi tes kompetensi Al-Qur'an dan Pedagogi (15 Soal) telah siap di portal seleksi Anda.\n"
                        ."Silakan login ke dashboard Anda untuk mulai mengerjakan dalam kurun waktu *2x24 jam*.\n\n"
                        .'🌐 Link Portal: '.url('/login')."\n\n"
                        ."Barakallahu fiikum,\n*Panitia Seleksi Guru AL-HIKMAH LMS*";

                    $this->whatsAppService->sendMessage($application->phone, $waMessage);
                } catch (\Throwable $e) {
                    Log::warning("WhatsApp test scheduled notification failed: {$e->getMessage()}");
                }
            }

            return true;
        });
    }

    public function scheduleInterview(MentorApplication $application, array $data): bool
    {
        return DB::transaction(function () use ($application, $data) {
            $oldStatus = $application->status;
            $application->update([
                'status' => 'interview_scheduled',
                'current_stage' => 4,
                'admin_notes' => $data['interview_notes'] ?? $application->admin_notes,
                'interview_scheduled_at' => $data['interview_scheduled_at'] ?? null,
                'interview_meeting_link' => $data['interview_meeting_link'] ?? null,
                'interview_type' => $data['interview_type'] ?? 'online',
                'interview_notes' => $data['interview_notes'] ?? null,
            ]);

            FinancialAuditLog::log(
                userId: auth()->id(),
                action: 'mentor_interview_scheduled',
                entityType: 'mentor_application',
                entityId: $application->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => 'interview_scheduled', 'data' => collect($data)->except('_token')->toArray()]
            );

            // Fail-safe WhatsApp Notification Undangan Wawancara
            if ($application->phone && ! empty($data['interview_scheduled_at'])) {
                try {
                    $formattedDate = Carbon::parse($data['interview_scheduled_at'])->locale('id')->isoFormat('dddd, D MMMM Y - HH:mm WIB');
                    $meetingInfo = ($data['interview_type'] ?? 'online') === 'online'
                        ? 'Link Pertemuan: '.($data['interview_meeting_link'] ?? 'Akan diinformasikan kembali')
                        : 'Lokasi: Kantor AL-HIKMAH LMS (Tatap Muka)';

                    $waMessage = "Assalamu'alaikum Wr. Wb. Ustadz/Ustadzah *{$application->full_name}*,\n\n"
                        ."Alhamdulillah, hasil tes kompetensi Anda memenuhi kualifikasi. Kami mengundang Anda untuk mengikuti sesi *Wawancara & Simulasi Mengajar (Microteaching)*:\n\n"
                        ."📅 Waktu: {$formattedDate}\n"
                        .'📍 Tipe: '.strtoupper($data['interview_type'] ?? 'ONLINE')."\n"
                        ."🔗 {$meetingInfo}\n\n"
                        ."Mohon konfirmasi kehadiran Anda. Jazakumullah khairan.\n\n"
                        .'*Tim Rekrutmen AL-HIKMAH LMS*';

                    $this->whatsAppService->sendMessage($application->phone, $waMessage);
                } catch (\Throwable $e) {
                    Log::warning("WhatsApp interview notification failed: {$e->getMessage()}");
                }
            }

            return true;
        });
    }

    public function updateStage(MentorApplication $application, string $newStatus, int $newStage, ?string $notes = null): bool
    {
        $oldStatus = $application->status;
        $application->status = $newStatus;
        $application->current_stage = $newStage;

        if ($notes) {
            $application->admin_notes = $notes;
        }

        $saved = $application->save();

        if ($saved) {
            FinancialAuditLog::log(
                userId: auth()->id(),
                action: 'mentor_application_stage_updated',
                entityType: 'mentor_application',
                entityId: $application->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => $newStatus, 'stage' => $newStage]
            );
        }

        return $saved;
    }

    public function rejectApplication(MentorApplication $application, string $reason): bool
    {
        return DB::transaction(function () use ($application, $reason) {
            $oldStatus = $application->status;
            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            FinancialAuditLog::log(
                userId: auth()->id(),
                action: 'mentor_application_rejected',
                entityType: 'mentor_application',
                entityId: $application->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => 'rejected', 'reason' => $reason]
            );

            return true;
        });
    }

    public function acceptApplication(MentorApplication $application, ?string $notes = null): array
    {
        return $this->approveApplication($application);
    }

    public function approveApplication(MentorApplication $application): array
    {
        $oldStatus = $application->status;
        $result = $this->mentorAccountService->createMentorAccount($application);

        FinancialAuditLog::log(
            userId: auth()->id(),
            action: 'mentor_application_approved',
            entityType: 'mentor_application',
            entityId: $application->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => 'approved', 'mentor_id' => $result['mentor']->id ?? null]
        );

        return $result;
    }
}
