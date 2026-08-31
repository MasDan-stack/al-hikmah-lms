<?php

namespace Database\Factories;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MentorFactory extends Factory
{
    protected $model = Mentor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->mentor(),
            'full_name' => fake()->name(),
            'specialization' => 'Tahfidz',
            'bio' => fake()->sentence(),
            'rating' => 5.00,
            'is_active' => true,
            'status' => 'active',
            'join_date' => today(),
        ];
    }
}
