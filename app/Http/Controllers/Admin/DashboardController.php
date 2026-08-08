<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Student;
use App\Models\Session; // Model sudah di-update
use Illuminate\Http\Request;

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