<?php

namespace App\Events;

use App\Models\Mentor;
use App\Models\Student;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentAssignedToMentor
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Mentor $mentor,
        public Student $student,
        public string $day
    ) {}
}
