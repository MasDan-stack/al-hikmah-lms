<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CriticalRiskAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $criticalStudents
    ) {}

    public function build(): self
    {
        return $this->subject('🚨 [AL-HIKMAH LMS] Laporan Harian: Santri Kritis Berisiko Dropout')
            ->view('emails.analytics.critical-risk-alert');
    }
}
