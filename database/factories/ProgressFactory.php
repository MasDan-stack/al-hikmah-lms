<?php

namespace Database\Factories;

use App\Models\Mentor;
use App\Models\Progress;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Progress>
 */
class ProgressFactory extends Factory
{
    protected $model = Progress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'mentor_id' => Mentor::factory(),
            'kategori' => fake()->randomElement(['Tahsin', 'Tahfidz', 'Tajwid']),
            'surah_start' => 'Al-Fatihah',
            'surah_end' => 'An-Nas',
            'ayat_start' => 1,
            'ayat_end' => 7,
            'juz' => fake()->numberBetween(1, 30),
            'nilai_fluent' => fake()->numberBetween(70, 100),
            'nilai_tajwid' => fake()->numberBetween(70, 100),
            'nilai_adab' => fake()->numberBetween(80, 100),
            'catatan_evaluasi' => fake()->paragraph(),
            'homework' => fake()->sentence(),
        ];
    }
}
