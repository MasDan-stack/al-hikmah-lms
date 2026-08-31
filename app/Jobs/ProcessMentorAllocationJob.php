<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Models\MatchingLog;
use App\Models\Mentor;
use App\Models\MentorActivityLog;
use App\Notifications\NotificationType;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMentorAllocationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int|Enrollment $enrollment,
        public int $mentorId,
        public float $score = 100.0,
        public string $selectionType = 'recommended',
        public ?array $breakdown = null,
        public ?int $selectedBy = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $enrollment = $this->enrollment instanceof Enrollment
            ? $this->enrollment
            : Enrollment::with(['student.parent.user', 'program'])->find($this->enrollment);

        if (! $enrollment) {
            return;
        }

        $mentor = Mentor::with('user')->find($this->mentorId);
        if (! $mentor) {
            return;
        }

        // 1. Simpan Audit Log Matchmaking
        MatchingLog::create([
            'enrollment_id' => $enrollment->id,
            'mentor_id' => $mentor->id,
            'score' => $this->score,
            'breakdown' => $this->breakdown,
            'selection_type' => $this->selectionType,
            'selected_by' => $this->selectedBy,
        ]);

        // 2. Catat Log Aktivitas Mentor
        MentorActivityLog::log(
            $mentor->id,
            'enrollment_allocated',
            "Santri {$enrollment->student?->getDisplayName()} berhasil dialokasikan via Smart Matchmaking AI ({$this->selectionType}, Skor: {$this->score}%)."
        );

        // 3. Notifikasi ke Mentor via WhatsApp / Notification System
        if ($mentor->user_id) {
            NotificationService::send(
                $mentor->user_id,
                'Penugasan Santri Baru!',
                "Alhamdulillah, Anda mendapatkan penugasan santri baru ({$enrollment->student?->getDisplayName()}) untuk program {$enrollment->program?->name}.",
                NotificationType::INFO,
                route('mentor.sessions.index'),
                'enrollment',
                true
            );
        }

        Log::info("ProcessMentorAllocationJob completed for Enrollment #{$enrollment->id} -> Mentor #{$mentor->id}");
    }
}
