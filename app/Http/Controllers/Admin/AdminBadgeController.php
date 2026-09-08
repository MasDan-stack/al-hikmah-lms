<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBadgeController extends Controller
{
    public function index(): View
    {
        $badges = Badge::withCount('students')->get();

        return view('admin.gamification.badges.index', compact('badges'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:badges,code',
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'icon' => 'required|string|max:50',
            'category' => 'required|in:milestone,streak,achievement,leaderboard,adab',
            'points_reward' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = true;
        Badge::create($validated);

        return back()->with('success', 'Lencana baru berhasil ditambahkan ke katalog!');
    }

    public function update(Request $request, Badge $badge): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'icon' => 'required|string|max:50',
            'category' => 'required|in:milestone,streak,achievement,leaderboard,adab',
            'points_reward' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $badge->update($validated);

        return back()->with('success', 'Data lencana berhasil diperbarui!');
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        $badge->delete();

        return back()->with('success', 'Lencana berhasil dihapus dari katalog.');
    }
}
