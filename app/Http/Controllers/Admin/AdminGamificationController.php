<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetLog;
use App\Models\Setting;
use App\Services\LeaderboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGamificationController extends Controller
{
    public function __construct(
        protected LeaderboardService $leaderboardService
    ) {}

    public function settings(): View
    {
        $domainSetting = Setting::where('key', 'institution_domain')->value('value') ?: 'alhikmah.com';
        $auditLogs = PasswordResetLog::with(['user', 'changer'])
            ->latest('created_at')
            ->paginate(15);

        return view('admin.gamification.settings', compact('domainSetting', 'auditLogs'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'institution_domain' => 'required|string|max:100',
        ]);

        Setting::updateOrCreate(
            ['key' => 'institution_domain'],
            ['value' => $request->institution_domain]
        );

        return back()->with('success', 'Pengaturan domain institusi berhasil diperbarui!');
    }

    public function leaderboard(Request $request): View
    {
        $category = $request->query('category', 'overall');
        $leaderboard = $this->leaderboardService->getLeaderboard($category, 100);

        return view('admin.gamification.leaderboard', compact('category', 'leaderboard'));
    }

    public function refreshLeaderboard(): RedirectResponse
    {
        $this->leaderboardService->invalidateCache();

        return back()->with('success', 'Cache leaderboard berhasil disegarkan!');
    }
}
