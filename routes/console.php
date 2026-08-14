<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Enrollment::where('status', EnrollmentStatus::WAITING_ADMIN->value)
        ->where('created_at', '<', now()->subDays(7))
        ->update([
            'status' => EnrollmentStatus::CANCELLED->value,
            'admin_notes' => 'Otomatis dibatalkan oleh sistem karena tidak diproses lebih dari 7 hari.',
        ]);

    Enrollment::where('status', EnrollmentStatus::WAITING_PARENT->value)
        ->where('updated_at', '<', now()->subDays(7))
        ->update([
            'status' => EnrollmentStatus::CANCELLED->value,
            'admin_notes' => 'Otomatis dibatalkan oleh sistem karena wali santri tidak merespon tawaran jadwal lebih dari 7 hari.',
        ]);
})->daily()->name('expire-stale-enrollments');
