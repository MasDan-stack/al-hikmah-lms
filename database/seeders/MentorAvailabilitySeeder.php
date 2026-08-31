<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\MentorAvailability;
use Illuminate\Database\Seeder;

class MentorAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $mentors = Mentor::all();
        $days = MentorAvailability::DAYS_ORDER;

        foreach ($mentors as $mentor) {
            foreach ($days as $day) {
                MentorAvailability::firstOrCreate(
                    [
                        'mentor_id' => $mentor->id,
                        'day' => $day,
                    ],
                    [
                        'start_time' => '16:00:00',
                        'end_time' => '20:00:00',
                        'max_students' => $mentor->default_max_students_per_day ?? 5,
                        'is_available' => true,
                        'is_holiday' => false,
                    ]
                );
            }

            // Assign days for attached students if pivot day_assigned is null
            foreach ($mentor->students as $index => $student) {
                $assignedDay = $days[$index % count($days)];
                $mentor->students()->updateExistingPivot($student->id, [
                    'day_assigned' => $assignedDay,
                    'time_assigned' => '16:30:00',
                    'is_active' => true,
                ]);
            }
        }
    }
}
