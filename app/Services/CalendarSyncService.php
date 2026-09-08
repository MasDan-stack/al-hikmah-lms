<?php

namespace App\Services;

use App\Models\Mentor;
use App\Models\MentorCalendarSync;
use App\Models\Session; // Assuming LearningSession is actually Session based on Student.php relationship
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CalendarSyncService
{
    /**
     * Membuat URL otorisasi OAuth2 resmi.
     */
    public function getAuthUrl(string $provider, Mentor $mentor): string
    {
        if ($provider === 'google') {
            $clientId = config('services.google.calendar_client_id');
            $redirectUri = route('mentor.calendar.callback', ['provider' => 'google']);

            $url = 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'https://www.googleapis.com/auth/calendar.events.readonly https://www.googleapis.com/auth/calendar.events',
                'access_type' => 'offline',
                'prompt' => 'consent',
                'state' => encrypt($mentor->id),
            ]);

            return $url;
        }

        // Outlook/Live/Office365 or others can be added here
        return '';
    }

    /**
     * Menukar kode otorisasi dengan access token dan refresh token yang disimpan terenkripsi.
     */
    public function handleCallback(string $provider, string $code, Mentor $mentor): bool
    {
        if ($provider === 'google') {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.calendar_client_id'),
                'client_secret' => config('services.google.calendar_client_secret'),
                'code' => $code,
                'redirect_uri' => route('mentor.calendar.callback', ['provider' => 'google']),
                'grant_type' => 'authorization_code',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $mentor->calendarSync()->updateOrCreate(
                    ['provider' => 'google'],
                    [
                        'access_token' => $data['access_token'],
                        'refresh_token' => $data['refresh_token'] ?? null,
                        'token_expires_at' => now()->addSeconds($data['expires_in']),
                        'is_active' => true,
                    ]
                );

                return true;
            }

            Log::error('Google Calendar OAuth failed: '.$response->body());

            return false;
        }

        return false;
    }

    /**
     * Mengambil daftar agenda 14 hari ke depan dan memetakan rentang jam yang beririsan dengan angka slot 0 sampai 6 Al-Hikmah.
     */
    public function syncBusySlots(Mentor $mentor): array
    {
        $sync = $mentor->calendarSync()->where('is_active', true)->first();

        if (! $sync || ! $sync->access_token) {
            return [];
        }

        if ($sync->token_expires_at && $sync->token_expires_at->isPast()) {
            $this->refreshAccessToken($sync);
        }

        // Mock implementation for now, in a real app this calls the Google Calendar API
        // https://www.googleapis.com/calendar/v3/calendars/primary/events
        $busySlots = [];

        // Simulating the API response and logic
        $sync->update([
            'last_synced_at' => now(),
            'busy_slots_cache' => $busySlots,
        ]);

        return $busySlots;
    }

    /**
     * Mengirim agenda bimbingan Al-Hikmah yang telah dikonfirmasi ke kalender Google guru beserta link meeting/alamat.
     */
    public function pushSessionToExternalCalendar(Session $session): bool
    {
        $mentor = $session->mentor;
        $sync = $mentor->calendarSync()->where('is_active', true)->first();

        if (! $sync || ! $sync->access_token) {
            return false;
        }

        if ($sync->token_expires_at && $sync->token_expires_at->isPast()) {
            $this->refreshAccessToken($sync);
        }

        // Mock implementation to push to Google Calendar
        // In real app: POST https://www.googleapis.com/calendar/v3/calendars/primary/events

        return true;
    }

    /**
     * Cek apakah ada konflik dengan kalender eksternal pada hari dan jam tertentu.
     */
    public function hasExternalConflict(Mentor $mentor, string $day, string $time): bool
    {
        $sync = $mentor->calendarSync()->where('is_active', true)->first();
        if (! $sync || empty($sync->busy_slots_cache)) {
            return false;
        }

        // Simple mock logic:
        // In reality, this would check if the requested $day and $time intersects with any datetime in busy_slots_cache.
        $cache = $sync->busy_slots_cache;

        foreach ($cache as $busySlot) {
            if ($busySlot['day'] === $day && $busySlot['time'] === $time) {
                return true;
            }
        }

        return false;
    }

    protected function refreshAccessToken(MentorCalendarSync $sync): void
    {
        if ($sync->provider === 'google' && $sync->refresh_token) {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.calendar_client_id'),
                'client_secret' => config('services.google.calendar_client_secret'),
                'refresh_token' => $sync->refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $sync->update([
                    'access_token' => $data['access_token'],
                    'token_expires_at' => now()->addSeconds($data['expires_in']),
                ]);
            }
        }
    }
}
