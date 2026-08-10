<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'program_id' => Program::factory(),
            'amount' => fake()->randomElement([150000, 250000, 350000]),
            'invoice_number' => 'INV-'.strtoupper(Str::random(8)),
            'status' => fake()->randomElement(['pending', 'paid', 'failed']),
            'payment_date' => now(),
            'payment_method' => fake()->randomElement(['Transfer Bank', 'QRIS', 'Virtual Account']),
            'gateway_response' => null,
        ];
    }
}
