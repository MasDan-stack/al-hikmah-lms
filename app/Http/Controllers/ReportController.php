<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function downloadProgress(Request $request, mixed $student = null)
    {
        $studentUser = is_numeric($student) ? User::find($student) : null;
        $studentUser = $studentUser ?? auth()->user();

        $progressList = Progress::with(['student.user', 'mentor.user'])
            ->latest()
            ->take(15)
            ->get();

        return view('reports.progress-pdf', [
            'studentUser' => $studentUser,
            'progressList' => $progressList,
            'generatedAt' => now()->locale('id')->isoFormat('dddd, D MMMM Y, H:i').' WIB',
        ]);
    }
}
