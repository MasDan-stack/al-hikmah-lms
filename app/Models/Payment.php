<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'program_id',
        'enrollment_id',
        'amount',
        'registration_fee',
        'program_fee',
        'payment_purpose',
        'invoice_number',
        'status',
        'payment_date',
        'due_date',
        'payment_method',
        'gateway_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'program_fee' => 'decimal:2',
            'payment_date' => 'datetime',
            'due_date' => 'date',
            'gateway_response' => 'array',
        ];
    }

    public function hasRegistrationFee(): bool
    {
        return (float) $this->registration_fee > 0;
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
