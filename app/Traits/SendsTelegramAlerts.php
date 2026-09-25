<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait SendsTelegramAlerts
{
    protected function sendTelegramAlert(string $message, string $level = 'info'): void
    {
        $token = config('alhikmah.alert.telegram_bot_token');
        $chatId = config('alhikmah.alert.telegram_chat_id');

        if (! $token || ! $chatId) {
            Log::warning('Notifikasi Telegram dilewati: token atau chat_id belum terkonfigurasi di .env');

            return;
        }

        $emoji = match ($level) {
            'critical' => '🔴',
            'warning' => '🟡',
            'success' => '🟢',
            default => 'ℹ️',
        };

        try {
            Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "{$emoji} *AL-HIKMAH LMS Alert*\n\n{$message}\n\n_Waktu: ".now()->toDateTimeString().' WIB_',
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim Telegram alert: '.$e->getMessage());
        }
    }
}
