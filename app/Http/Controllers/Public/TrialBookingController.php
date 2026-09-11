<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\TrialBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrialBookingController extends Controller
{
    /**
     * Simpan pengajuan sesi uji coba gratis 15 menit (Placement Test)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'parent_name' => ['required', 'string', 'max:255'],
            'child_name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'min:9', 'max:30'],
            'child_age' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'in:L,P'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'trial_focus' => ['required', 'string', 'in:iqra_placement,tahsin_tajwid,tahfidz_hafalan,bahasa_arab'],
            'preferred_date' => ['nullable', 'date'],
            'preferred_time_slot' => ['required', 'string', 'in:pagi,siang,sore,malam'],
            'learning_method' => ['required', 'in:online,offline'],
            'city' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Standardize WhatsApp number format
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['whatsapp']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        } elseif (! str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62'.$cleanPhone;
        }
        $validated['whatsapp'] = $cleanPhone;

        // Automatically associate program if empty but matching trial_focus
        if (empty($validated['program_id'])) {
            $matchedProgram = match ($validated['trial_focus']) {
                'iqra_placement' => Program::where('name', 'like', '%iqra%')->first(),
                'tahsin_tajwid' => Program::where('name', 'like', '%tahsin%')->first(),
                'tahfidz_hafalan' => Program::where('name', 'like', '%tahfidz%')->first(),
                'bahasa_arab' => Program::where('name', 'like', '%arab%')->first(),
                default => null,
            };
            if ($matchedProgram) {
                $validated['program_id'] = $matchedProgram->id;
            }
        }

        $booking = TrialBooking::create($validated);

        // Generate WA coordination link for the parent
        $adminWa = function_exists('site_setting') ? site_setting('whatsapp_number', '6285786689008') : '6285786689008';
        $adminWa = preg_replace('/[^0-9]/', '', $adminWa);
        if (str_starts_with($adminWa, '0')) {
            $adminWa = '62'.substr($adminWa, 1);
        }

        $timeSlotText = TrialBooking::TIME_SLOTS[$booking->preferred_time_slot] ?? $booking->preferred_time_slot;
        $focusText = TrialBooking::FOCUS_OPTIONS[$booking->trial_focus] ?? $booking->trial_focus;

        $waMessage = "Assalamu'alaikum Admin AL-HIKMAH,\n\nSaya telah mendaftarkan Sesi Uji Coba Gratis 15 Menit (Placement Test) untuk ananda:\n"
            ."• Nama Wali: {$booking->parent_name}\n"
            ."• Nama Santri: {$booking->child_name} ({$booking->child_age})\n"
            ."• Fokus Evaluasi: {$focusText}\n"
            ."• Pilihan Waktu: {$timeSlotText}\n"
            .'• Metode: '.strtoupper($booking->learning_method)."\n\n"
            .'Mohon bantuan konfirmasi jadwal dan tautan temu online dengan ustadz/ustadzah. Terima kasih.';

        $waUrl = 'https://api.whatsapp.com/send?phone='.$adminWa.'&text='.rawurlencode($waMessage);

        $successMessage = "Alhamdulillah, pendaftaran sesi uji coba gratis untuk ananda {$booking->child_name} berhasil dicatat. Tim Al-Hikmah akan segera mengonfirmasi jadwal melalui WhatsApp.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'booking_id' => $booking->id,
                'wa_url' => $waUrl,
            ]);
        }

        return redirect()->back()->with([
            'success' => $successMessage,
            'trial_booked' => true,
            'trial_wa_url' => $waUrl,
        ]);
    }
}
