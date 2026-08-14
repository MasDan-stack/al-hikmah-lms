<?php

namespace App\Listeners;

use App\Events\StudentAssignedToMentor;
use App\Models\MentorActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogMentorActivityListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(StudentAssignedToMentor $event): void
    {
        MentorActivityLog::log(
            $event->mentor->id,
            'Alokasi Santri Baru',
            "Santri {$event->student->getDisplayName()} dialokasikan pada hari {$event->day}."
        );
    }
}
