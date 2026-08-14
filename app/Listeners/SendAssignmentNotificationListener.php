<?php

namespace App\Listeners;

use App\Events\StudentAssignedToMentor;
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

        // In-App Notification untuk Mentor
        if ($mentor->user_id) {
            Notification::create([
                'user_id' => $mentor->user_id,
                'type' => 'student_assignment',
                'title' => 'Santri Baru Dialokasikan',
                'message' => "Santri {$student->getDisplayName()} telah dialokasikan ke jadwal mengajar Anda (Hari {$day}).",
                'is_read' => false,
            ]);
        }

        // In-App Notification untuk Wali Santri
        if ($student->parent?->user_id) {
            Notification::create([
                'user_id' => $student->parent->user_id,
                'type' => 'student_assignment',
                'title' => 'Pengampu Belajar Ananda',
                'message' => "Ananda {$student->getDisplayName()} telah dialokasikan ke Pengajar {$mentor->getDisplayName()} pada hari {$day}.",
                'is_read' => false,
            ]);
        }

        // Opsional: Integrasi WhatsApp Gateway Webhook
        $parentPhone = $student->parent?->user?->phone ?? $student->parent?->emergency_phone;
        if ($parentPhone) {
            Log::info("WhatsApp Queue: Mengirim template konfirmasi ke {$parentPhone} untuk santri {$student->full_name}");
        }
    }
}
