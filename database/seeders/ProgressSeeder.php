<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\Progress;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Database\Seeder;

class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $mentors = Mentor::all();
        $sessions = Session::all();

        if ($students->isEmpty() || $mentors->isEmpty()) {
            return;
        }

        $progressData = [
            [
                'session_id' => $sessions->first()?->id,
                'student_id' => $students->first()->id,
                'mentor_id' => $mentors->first()->id,
                'kategori' => 'Tahfidz',
                'surah_start' => 'Al-Baqarah',
                'surah_end' => 'Al-Baqarah',
                'ayat_start' => 1,
                'ayat_end' => 10,
                'juz' => 1,
                'nilai_fluent' => 88,
                'nilai_tajwid' => 85,
                'nilai_adab' => 90,
                'catatan_evaluasi' => 'Alhamdulillah bacaan lancar, pertahankan kelancaran makhraj kho dan '.'ain.',
                'homework' => 'Muroja\'ah surah Al-Baqarah ayat 1-15 3x sehari.',
            ],
            [
                'session_id' => $sessions->skip(1)->first()?->id,
                'student_id' => $students->skip(1)->first()?->id ?? $students->first()->id,
                'mentor_id' => $mentors->skip(1)->first()?->id ?? $mentors->first()->id,
                'kategori' => 'Tahsin',
                'surah_start' => 'An-Naba',
                'surah_end' => 'An-Naba',
                'ayat_start' => 1,
                'ayat_end' => 20,
                'juz' => 30,
                'nilai_fluent' => 82,
                'nilai_tajwid' => 80,
                'nilai_adab' => 85,
                'catatan_evaluasi' => 'Perhatikan dengung (ghunnah) pada ikhfa dan idgham bighunnah.',
                'homework' => 'Latihan dengung 2 harakat di rumah.',
            ],
            [
                'session_id' => $sessions->last()?->id,
                'student_id' => $students->last()->id,
                'mentor_id' => $mentors->last()->id,
                'kategori' => 'Murojaah',
                'surah_start' => 'Al-Mulk',
                'surah_end' => 'Al-Mulk',
                'ayat_start' => 1,
                'ayat_end' => 30,
                'juz' => 29,
                'nilai_fluent' => 92,
                'nilai_tajwid' => 90,
                'nilai_adab' => 95,
                'catatan_evaluasi' => 'Masya Allah hafalan Juz 29 sangat mumtaz.',
                'homework' => 'Persiapan setoran Juz 28 surah Al-Jumu\'ah.',
            ],
        ];

        foreach ($progressData as $item) {
            Progress::create($item);
        }
    }
}
