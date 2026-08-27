<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Kirim email dengan konten HTML sederhana
     */
    public function sendRawEmail(string $to, string $subject, string $htmlContent): bool
    {
        try {
            Mail::html($htmlContent, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });

            return true;
        } catch (\Throwable $e) {
            Log::warning("[EmailService Error] Gagal mengirim email ke {$to}: ".$e->getMessage());

            return false;
        }
    }
}
