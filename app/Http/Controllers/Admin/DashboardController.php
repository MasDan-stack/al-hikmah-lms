<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Session;
use App\Models\Student; // Model sudah di-update

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalMentors = Mentor::count();
        $todaySessions = Session::whereDate('date', today())->count();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalMentors',
            'todaySessions'
        ));
    }
}
