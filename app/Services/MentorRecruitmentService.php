<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\Mentor;
use App\Models\MentorApplication;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MentorRecruitmentService
{
    public function __construct(
        protected WhatsAppService $whatsAppService,
        protected MentorAccountService $mentorAccountService
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
            if (! $mentor) {
                $mentor = Mentor::create([
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                    'full_name' => $application->full_name,
                    'specialization' => $application->specialization ?? 'Tahfidz',
                    'bio' => $application->experience_description ?? 'Calon Guru Pembimbing Al-Qur\'an',
                    'rating' => 5.00,
                    'is_active' => false,
                    'status' => 'inactive',
                    'sanad_chain' => $application->sanad_chain,
                ]);
            } else {
                $mentor->update([
                    'application_id' => $application->id,
                    'specialization' => $application->specialization ?? $mentor->specialization,
                ]);
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

    public function scheduleInterview(MentorApplication $application, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($application, $notes) {
            $oldStatus = $application->status;
            $application->update([
                'status' => 'interview_scheduled',
                'current_stage' => 4,
                'admin_notes' => $notes ?? $application->admin_notes,
            ]);

            FinancialAuditLog::log(
                userId: auth()->id(),
                action: 'mentor_interview_scheduled',
                entityType: 'mentor_application',
                entityId: $application->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => 'interview_scheduled', 'notes' => $notes]
            );

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
