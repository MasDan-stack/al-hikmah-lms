<?php

namespace Database\Factories;

use App\Models\MentorApplication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MentorApplicationFactory extends Factory
{
    protected $model = MentorApplication::class;

    public function definition()
    {
        return [
            'application_code' => 'REG-'.date('Ym').'-'.strtoupper(Str::random(6)),
            'full_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'birth_date' => $this->faker->date('Y-m-d', '-20 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'education' => 'S1 Pendidikan Agama Islam',
            'institution' => 'UIN',
            'experience_years' => $this->faker->numberBetween(1, 10),
            'experience_description' => $this->faker->paragraph,
            'specialization' => $this->faker->randomElement(['Tahfidz', 'Tahsin', 'Iqra']),
            'hifz_total_juz' => $this->faker->numberBetween(0, 30),
            'status' => 'submitted',
            'current_stage' => 1,
            'submitted_at' => now(),
        ];
    }
}
