<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Selamat Datang di AL-HIKMAH LMS',
                'message' => 'Akun Anda berhasil dikonfigurasi. Silakan pantau jadwal sesi & catatan progres bimbingan Al-Qur\'an.',
                'type' => 'info',
                'is_read' => false,
            ]);
        }
    }
}
