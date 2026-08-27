<?php

namespace App\Services;

use App\Enums\Role as RoleEnum;
use App\Models\HifzTarget;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentAccountService
{
    public function __construct(
        protected HifzProgressService $hifzProgressService,
        protected WhatsAppService $whatsAppService,
        protected EmailService $emailService
    ) {}

    /**
     * Menghasilkan email unik dari nama santri
     * Format: {3hurufdepan}.{namabelakang}@{domain}
     */
    public function generateEmail(string $name, ?string $domain = null): string
    {
        $domain = $domain ?: (Setting::where('key', 'institution_domain')->value('value') ?: 'alhikmah.com');
        $cleanName = $this->sanitizeName($name);

        $parts = preg_split('/\s+/', trim($cleanName));
        $firstPart = $parts[0] ?? 'san';
        $lastPart = count($parts) > 1 ? end($parts) : $firstPart;

        $prefix = Str::lower(substr($firstPart, 0, 3));
        $suffix = Str::lower($lastPart);
        $baseUsername = "{$prefix}.{$suffix}";

        $email = "{$baseUsername}@{$domain}";
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = "{$baseUsername}{$counter}@{$domain}";
            $counter++;
        }

        return $email;
    }

    public function generateStudentEmail(string $name, ?string $domain = null): string
    {
        return $this->generateEmail($name, $domain);
    }

    /**
     * Membersihkan nama santri dari gelar dan relasi bin/binti
     */
    public function sanitizeName(string $name): string
    {
        $cleaned = preg_replace('/\b(bin|binti|ananda|kak|dek)\b/i', '', $name);
        $cleaned = preg_replace('/[^a-zA-Z\s]/', '', $cleaned);

        return trim(preg_replace('/\s+/', ' ', $cleaned));
    }

    /**
     * Menghasilkan password acak aman minimal 8 karakter dengan kombinasi lengkap
     */
    public function generatePassword(int $length = 8): string
    {
        $length = max(8, $length);
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';

        // Pastikan minimal ada 1 dari setiap kategori karakter
        $passwordArr = [
            $uppercase[random_int(0, strlen($uppercase) - 1)],
            $lowercase[random_int(0, strlen($lowercase) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
            $symbols[random_int(0, strlen($symbols) - 1)],
        ];

        $allChars = $uppercase.$lowercase.$numbers.$symbols;
        for ($i = 4; $i < $length; $i++) {
            $passwordArr[] = $allChars[random_int(0, strlen($allChars) - 1)];
        }

        shuffle($passwordArr);

        return implode('', $passwordArr);
    }

    /**
     * Membuat akun User dan Student baru secara otomatis
     *
     * @return array{student: Student, user: User, plain_password: string}
     */
    public function createStudentAccount(
        ParentProfile $parent,
        string $fullName,
        int $age = 10,
        string $gender = 'L',
        ?string $location = null,
        ?string $notes = null
    ): array {
        $email = $this->generateEmail($fullName);
        $plainPassword = $this->generatePassword(8);

        $studentRole = Role::firstOrCreate(
            ['name' => RoleEnum::STUDENT->value],
            ['label' => RoleEnum::STUDENT->label()]
        );

        $user = User::create([
            'name' => $fullName,
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'role_id' => $studentRole->id,
            'phone' => $parent->emergency_phone ?? $parent->user?->phone,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'parent_id' => $parent->id,
            'full_name' => $fullName,
            'age' => $age,
            'gender' => $gender,
            'location' => $location ?: ($parent->address ?: 'Indonesia'),
            'notes' => $notes,
            'total_points' => 0,
            'current_streak' => 0,
            'longest_streak' => 0,
            'privacy_leaderboard' => false,
        ]);

        // Inisialisasi 30 Juz Progress
        $this->hifzProgressService->initializeJuzProgress($student);

        // Buat Target Hari Pertama Otomatis
        HifzTarget::create([
            'student_id' => $student->id,
            'mentor_id' => $parent->user_id, // Default assignor
            'target_date' => now()->toDateString(),
            'surah_name' => 'QS. An-Naas',
            'start_ayat' => 1,
            'end_ayat' => 6,
            'total_ayat' => 6,
            'notes' => 'Target hari pertama santri baru: Hafal 1 Surat/Ayat pembuka!',
            'status' => 'pending',
        ]);

        // Kirim Notifikasi Kredensial ke Orang Tua
        $this->sendWelcomeCredentials($parent, $student, $email, $plainPassword);

        return [
            'student' => $student,
            'user' => $user,
            'plain_password' => $plainPassword,
        ];
    }

    /**
     * Mengirimkan kredensial akun baru kepada orang tua
     */
    protected function sendWelcomeCredentials(
        ParentProfile $parent,
        Student $student,
        string $email,
        string $plainPassword
    ): void {
        $parentUser = $parent->user;
        $parentPhone = $parent->emergency_phone ?: $parentUser?->phone;
        $parentEmail = $parentUser?->email;

        $message = "Assalamu'alaikum Wr. Wb. Bapak/Ibu {$parentUser?->name},\n\n"
            ."Alhamdulillah, akun belajar santri untuk ananda *{$student->getDisplayName()}* telah berhasil dibuat di AL-HIKMAH LMS.\n\n"
            ."Berikut detail login ananda:\n"
            ."📧 Email: {$email}\n"
            ."🔑 Password: {$plainPassword}\n"
            .'🌐 URL Login: '.url('/login')."\n\n"
            .'Mohon simpan informasi login ini dengan baik.';

        $waSent = false;
        if ($parentPhone) {
            $waSent = $this->whatsAppService->sendMessage($parentPhone, $message);
        }

        if (! $waSent && $parentEmail) {
            $this->emailService->sendRawEmail(
                $parentEmail,
                'Kredensial Akun Santri AL-HIKMAH LMS - '.$student->getDisplayName(),
                nl2br($message)
            );
        }
    }
}
