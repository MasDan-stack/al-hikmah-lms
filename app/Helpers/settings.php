<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

if (! function_exists('site_setting')) {
    /**
     * Ambil nilai pengaturan website dari database (tabel settings), fallback ke config/env.
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        try {
            if (Schema::hasTable('settings')) {
                $val = Setting::get($key);
                if ($val !== null && $val !== '') {
                    return $val;
                }
            }
        } catch (Throwable $e) {
            // Fallback jika database belum di-migrate
        }

        return config("settings.{$key}", $default);
    }
}

if (! function_exists('wa_url')) {
    /**
     * Generate WhatsApp link secara dinamis.
     *
     * @param  string|null  $message  Pesan default yang akan dikirim via WA
     * @return string URL WhatsApp lengkap
     */
    function wa_url(?string $message = null): string
    {
        $phone = site_setting('whatsapp_number', '6285786689008');

        // Clean any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $url = "https://wa.me/{$phone}";

        if (! empty($message)) {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }
}
