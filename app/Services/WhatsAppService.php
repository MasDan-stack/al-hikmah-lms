<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.api_key', env('WHATSAPP_API_KEY'));
    }

    /**
     * Kirim Pesan WhatsApp ke Nomor Wali Santri
     */
    public function sendMessage(string $targetPhone, string $message): bool
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $targetPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        if (empty($this->apiKey)) {
            Log::info("[WhatsApp Service Mock] Mengirim pesan ke {$cleanPhone}:\n{$message}");

            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post('https://api.fonnte.com/send', [
                'target' => $cleanPhone,
                'message' => $message,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('[WhatsApp Service Error] '.$e->getMessage());

            return false;
        }
    }
}
