<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function downloadProgress(Request $request, $studentId)
    {
        $studentUser = User::find($studentId) ?? auth()->user();

        $progressList = Progress::with(['student.user', 'mentor.user'])
            ->latest()
            ->take(15)
            ->get();

        return view('reports.progress-pdf', [
            'studentUser' => $studentUser,
            'progressList' => $progressList,
            'generatedAt' => now()->format('d F Y, H:i'),
        ]);
    }
}
