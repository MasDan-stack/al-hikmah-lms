<?php

namespace App\Listeners;

use App\Events\StudentAssignedToMentor;
use App\Models\MentorAvailability;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendAssignmentNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(StudentAssignedToMentor $event): void
    {
        $mentor = $event->mentor;
        $student = $event->student;
        $day = $event->day;
        $dayLabel = MentorAvailability::DAYS[$day] ?? $day;
        $timeStr = $event->time ? ' Jam '.substr($event->time, 0, 5).' WIB' : '';

        // In-App Notification untuk Mentor
        if ($mentor->user_id) {
            Notification::create([
                'user_id' => $mentor->user_id,
                'type' => 'student_assignment',
                'title' => 'Santri Baru Dialokasikan',
                'message' => "Santri {$student->getDisplayName()} telah dialokasikan ke jadwal mengajar Anda (Hari {$dayLabel}{$timeStr}).",
                'is_read' => false,
            ]);
        }

        // In-App Notification untuk Wali Santri
        if ($student->parent?->user_id) {
            Notification::create([
                'user_id' => $student->parent->user_id,
                'type' => 'student_assignment',
                'title' => 'Pengampu Belajar Ananda',
                'message' => "Ananda {$student->getDisplayName()} telah dialokasikan ke Pengajar {$mentor->getDisplayName()} pada hari {$dayLabel}{$timeStr}.",
                'is_read' => false,
            ]);
        }

        // Logging
        $parentPhone = $student->parent?->user?->phone ?? $student->parent?->emergency_phone;
        if ($parentPhone) {
            Log::info("WhatsApp Assignment Notification: Mengirim pesan ke {$parentPhone} untuk santri {$student->getDisplayName()} ({$dayLabel}{$timeStr})");
        }
    }
}
