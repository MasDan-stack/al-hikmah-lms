<?php

namespace App\Services;

use App\Models\Mentor;
use App\Models\MentorApplication;
use App\Models\MentorProbationTracking;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MentorAccountService
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    public function generateEmail(string $name): string
    {
        $domain = Setting::where('key', 'institution_domain')->value('value') ?: 'alhikmah.com';
        $cleaned = trim(preg_replace('/[^a-zA-Z\s]/', '', preg_replace('/\b(ustadz|ustadzah|guru|pengajar)\b/i', '', $name)));
        $parts = preg_split('/\s+/', $cleaned);
        $first = Str::lower(substr($parts[0] ?? 'men', 0, 3));
        $last = Str::lower(count($parts) > 1 ? end($parts) : ($parts[0] ?? 'tor'));

        $base = "{$first}.{$last}";
        $email = "{$base}@{$domain}";
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = "{$base}{$counter}@{$domain}";
            $counter++;
        }

        return $email;
    }

    public function generatePassword(int $length = 10): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';

        return substr(str_shuffle(str_repeat($chars, 3)), 0, $length);
    }

    public function createMentorAccount(MentorApplication $application): array
    {
        return DB::transaction(function () use ($application) {
            $mentorRole = Role::firstOrCreate(
                ['name' => 'mentor'],
                ['label' => 'Mentor / Guru']
            );

            // Cek apakah user sudah dibuat saat proses registrasi awal
            $user = $application->user ?? User::where('email', $application->email)->first();
            $plainPassword = null;

            if (! $user) {
                $email = $application->email ?: $this->generateEmail($application->full_name);
                $plainPassword = $this->generatePassword(10);

                $user = User::create([
                    'name' => $application->full_name,
                    'email' => $email,
                    'password' => Hash::make($plainPassword),
                    'role_id' => $mentorRole->id,
                    'phone' => $application->phone,
                ]);

                $application->update(['user_id' => $user->id]);
            } else {
                $user->update([
                    'role_id' => $mentorRole->id,
                    'phone' => $application->phone ?: $user->phone,
                ]);
            }

            // 2. Buat atau Update Profil Mentor menjadi status probation aktif
            $mentor = $application->mentor ?? Mentor::where('user_id', $user->id)->first();

            if (! $mentor) {
                $mentor = Mentor::create([
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                    'full_name' => $application->full_name,
                    'specialization' => $application->specialization,
                    'bio' => $application->experience_description ?? 'Guru Pembimbing Al-Qur\'an',
                    'rating' => 5.00,
                    'is_active' => true,
                    'join_date' => today(),
                    'probation_end_date' => today()->addMonths(3),
                    'status' => 'probation',
                    'sanad_chain' => $application->sanad_chain,
                ]);
            } else {
                $mentor->update([
                    'application_id' => $application->id,
                    'is_active' => true,
                    'status' => 'probation',
                    'join_date' => today(),
                    'probation_end_date' => today()->addMonths(3),
                    'specialization' => $application->specialization ?: $mentor->specialization,
                ]);
            }

            // 3. Inisialisasi Probation Tracking jika belum ada
            $probation = MentorProbationTracking::firstOrCreate(
                ['mentor_id' => $mentor->id],
                [
                    'start_date' => today(),
                    'end_date' => today()->addMonths(3),
                    'duration_months' => 3,
                    'status' => 'active',
                ]
            );

            // 4. Update Status Lamaran
            $application->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return [
                'user' => $user,
                'mentor' => $mentor,
                'plain_password' => $plainPassword,
            ];
        });
    }

    protected function sendCredentialsNotification(MentorApplication $app, string $email, string $password): void
    {
        $message = "Assalamu'alaikum Wr. Wb. Ustadz/Ustadzah *{$app->full_name}*,\n\n"
            ."Ahlan wa Sahlan! Selamat, lamaran Anda telah *DITERIMA* sebagai Guru Pembimbing di *AL-HIKMAH LMS*.\n\n"
            ."Berikut informasi akun login Anda:\n"
            ."📧 Email: {$email}\n"
            ."🔑 Password: {$password}\n"
            .'🌐 URL Login: '.url('/login')."\n\n"
            ."Status Anda saat ini adalah *Masa Percobaan (Probation 3 Bulan)*. Silakan login untuk memulai orientasi dan melengkapi jadwal bimbingan Anda.\n\n"
            ."Barakallahu fiikum,\n*Manajemen AL-HIKMAH LMS*";

        try {
            $this->whatsAppService->sendMessage($app->phone, $message);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim kredensial WA: '.$e->getMessage());
        }
    }
}
