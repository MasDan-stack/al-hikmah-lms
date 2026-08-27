<?php

namespace Database\Seeders;

use App\Models\HifzMilestone;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DefaultHifzMilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::take(5)->get();

        foreach ($students as $student) {
            HifzMilestone::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'name' => 'Target Khatam Juz 30',
                ],
                [
                    'target_type' => 'juz_completion',
                    'target_date' => now()->addDays(30),
                    'progress_current' => 150,
                    'progress_goal' => 564,
                    'status' => 'active',
                ]
            );
        }
    }
}
