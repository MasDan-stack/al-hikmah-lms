<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $programs = Program::all();

        if ($students->isEmpty() || $programs->isEmpty()) {
            return;
        }

        $payments = [
            [
                'student_id' => $students->first()->id,
                'program_id' => $programs->first()->id,
                'amount' => $programs->first()->price,
                'invoice_number' => 'INV-AH-'.date('Ym').'-001',
                'status' => 'paid',
                'payment_date' => now()->subDays(5),
                'payment_method' => 'Transfer Bank BCA',
            ],
            [
                'student_id' => $students->skip(1)->first()?->id ?? $students->first()->id,
                'program_id' => $programs->skip(2)->first()?->id ?? $programs->first()->id,
                'amount' => $programs->skip(2)->first()?->price ?? 250000,
                'invoice_number' => 'INV-AH-'.date('Ym').'-002',
                'status' => 'paid',
                'payment_date' => now()->subDays(2),
                'payment_method' => 'QRIS',
            ],
            [
                'student_id' => $students->last()->id,
                'program_id' => $programs->skip(1)->first()?->id ?? $programs->first()->id,
                'amount' => $programs->skip(1)->first()?->price ?? 450000,
                'invoice_number' => 'INV-AH-'.date('Ym').'-003',
                'status' => 'pending',
                'payment_date' => null,
                'payment_method' => 'Midtrans Payment Gateway',
            ],
        ];

        foreach ($payments as $payment) {
            Payment::firstOrCreate(
                ['invoice_number' => $payment['invoice_number']],
                $payment
            );
        }
    }
}
