<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentorAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        $availabilities = $mentor?->availabilities()
            ->get()
            ->keyBy('day') ?? collect();

        $days = MentorAvailability::DAYS;
        $daysOrder = MentorAvailability::DAYS_ORDER;

        return view('mentor.availability.index', compact('mentor', 'availabilities', 'days', 'daysOrder'));
    }

    public function updateBulk(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (! $mentor) {
            return back()->with('error', 'Profil mentor tidak ditemukan.');
        }

        $validated = $request->validate([
            'days' => 'required|array',
            'days.*.day' => 'required|string|in:'.implode(',', MentorAvailability::DAYS_ORDER),
            'days.*.start_time' => 'nullable',
            'days.*.end_time' => 'nullable',
            'days.*.max_students' => 'nullable|integer|min:1|max:20',
            'days.*.is_available' => 'nullable',
            'days.*.is_holiday' => 'nullable',
        ]);

        foreach ($validated['days'] as $dayData) {
            $isAvailable = isset($dayData['is_available']) && ($dayData['is_available'] == '1' || $dayData['is_available'] === true);
            $isHoliday = isset($dayData['is_holiday']) && ($dayData['is_holiday'] == '1' || $dayData['is_holiday'] === true);

            MentorAvailability::updateOrCreate(
                [
                    'mentor_id' => $mentor->id,
                    'day' => $dayData['day'],
                ],
                [
                    'start_time' => ! empty($dayData['start_time']) ? $dayData['start_time'] : '08:00',
                    'end_time' => ! empty($dayData['end_time']) ? $dayData['end_time'] : '16:00',
                    'max_students' => $dayData['max_students'] ?? $mentor->default_max_students_per_day ?? 5,
                    'is_available' => $isAvailable,
                    'is_holiday' => $isHoliday,
                ]
            );
        }

        return redirect()->route('mentor.availability.index')
            ->with('success', 'Jadwal ketersediaan mengajar berhasil diperbarui.');
    }
}
