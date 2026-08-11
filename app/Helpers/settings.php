<?php

if (! function_exists('wa_url')) {
    /**
     * Generate WhatsApp link secara dinamis.
     *
     * @param  string|null  $message  Pesan default yang akan dikirim via WA
     * @return string URL WhatsApp lengkap
     */
    function wa_url(?string $message = null): string
    {
        $phone = config('settings.whatsapp_number', '6285786689008');

        // Clean any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $url = "https://wa.me/{$phone}";

        if (! empty($message)) {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }
}
