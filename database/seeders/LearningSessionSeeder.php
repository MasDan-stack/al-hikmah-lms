<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Database\Seeder;

class LearningSessionSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $mentors = Mentor::all();

        if ($students->isEmpty() || $mentors->isEmpty()) {
            return;
        }

        $sessions = [
            [
                'student_id' => $students->first()->id,
                'mentor_id' => $mentors->first()->id,
                'date' => today()->format('Y-m-d'),
                'time' => '16:00',
                'method' => 'online',
                'status' => 'completed',
                'notes' => 'Sesi bimbingan Al-Fatihah dan Al-Baqarah 1-10.',
            ],
            [
                'student_id' => $students->skip(1)->first()?->id ?? $students->first()->id,
                'mentor_id' => $mentors->skip(1)->first()?->id ?? $mentors->first()->id,
                'date' => today()->format('Y-m-d'),
                'time' => '19:30',
                'method' => 'online',
                'status' => 'pending',
                'notes' => 'Setoran surah An-Naba dan tajwid nun mati.',
            ],
            [
                'student_id' => $students->last()->id,
                'mentor_id' => $mentors->last()->id,
                'date' => today()->addDays(1)->format('Y-m-d'),
                'time' => '16:30',
                'method' => 'offline',
                'status' => 'pending',
                'notes' => 'Bimbingan tatap muka hafalan Juz 29.',
            ],
            [
                'student_id' => $students->first()->id,
                'mentor_id' => $mentors->first()->id,
                'date' => today()->subDays(2)->format('Y-m-d'),
                'time' => '16:00',
                'method' => 'online',
                'status' => 'completed',
                'notes' => 'Setoran kelancaran Juz 30 surah An-Naziat.',
            ],
        ];

        foreach ($sessions as $sessionData) {
            Session::create($sessionData);
        }
    }
}
