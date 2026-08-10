<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Tahsin Al-Qur\'an', 'Tahfidz Cilik', 'Tajwid & Ghorib', 'Iqra Dasar', 'Kajian Adab']),
            'description' => fake()->sentence(),
            'duration_weeks' => fake()->numberBetween(8, 24),
            'price' => fake()->randomElement([150000, 250000, 350000, 500000]),
            'level' => fake()->randomElement(['Pemula', 'Menengah', 'Lanjutan']),
        ];
    }
}
