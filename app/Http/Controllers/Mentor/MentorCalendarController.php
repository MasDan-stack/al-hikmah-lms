<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Services\CalendarSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorCalendarController extends Controller
{
    public function __construct(
        protected CalendarSyncService $calendarSyncService
    ) {}

    public function connect(Request $request): RedirectResponse
    {
        $mentor = Auth::user()->mentor;
        if (! $mentor) {
            return redirect()->route('mentor.availability.index')->with('error', 'Profil mentor tidak ditemukan.');
        }

        $authUrl = $this->calendarSyncService->getAuthUrl('google', $mentor);
        if (empty($authUrl) || ! config('services.google.calendar_client_id')) {
            return redirect()->route('mentor.availability.index')->with('warning', 'Integrasi Google Calendar sedang dalam mode simulasi atau kredensial API belum diatur.');
        }

        return redirect()->away($authUrl);
    }

    public function callback(Request $request): RedirectResponse
    {
        $mentor = Auth::user()->mentor;
        if (! $mentor) {
            return redirect()->route('mentor.availability.index')->with('error', 'Profil mentor tidak ditemukan.');
        }

        $code = $request->query('code');
        if (! $code) {
            return redirect()->route('mentor.availability.index')->with('error', 'Otorisasi Google Calendar dibatalkan.');
        }

        $success = $this->calendarSyncService->handleCallback('google', $code, $mentor);
        if ($success) {
            return redirect()->route('mentor.availability.index')->with('success', 'Google Calendar berhasil terhubung! Jadwal Anda akan otomatis disinkronkan.');
        }

        return redirect()->route('mentor.availability.index')->with('error', 'Gagal menghubungkan Google Calendar.');
    }

    public function disconnect(): RedirectResponse
    {
        $mentor = Auth::user()->mentor;
        if ($mentor) {
            $mentor->calendarSync()->where('provider', 'google')->delete();
        }

        return redirect()->route('mentor.availability.index')->with('success', 'Koneksi Google Calendar berhasil diputuskan.');
    }

    public function sync(): RedirectResponse
    {
        $mentor = Auth::user()->mentor;
        if (! $mentor) {
            return redirect()->route('mentor.availability.index')->with('error', 'Profil mentor tidak ditemukan.');
        }

        $this->calendarSyncService->syncBusySlots($mentor);

        return redirect()->route('mentor.availability.index')->with('success', 'Jadwal Google Calendar berhasil disinkronkan.');
    }
}
