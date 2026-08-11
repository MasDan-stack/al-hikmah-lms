<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\RegisterMentorController;
use App\Http\Controllers\ReportController;
use App\Models\Mentor;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

// Halaman Home
Route::get('/', function () {
    return view('home');
})->name('home');

// Halaman Tentang Kami (dengan statistik real-time)
Route::get('/tentang-kami', function () {
    $totalStudents = Student::count();
    $totalMentors = Mentor::where('is_active', true)->count();
    $totalPrograms = Program::count();

    return view('tentang-kami', compact('totalStudents', 'totalMentors', 'totalPrograms'));
})->name('tentang-kami');

// Halaman Program Belajar (dengan data program dari DB)
Route::get('/program', function () {
    $programs = Program::all();

    return view('program', compact('programs'));
})->name('program');

Route::get('/metode', function () {
    return view('metode');
})->name('metode');

Route::get('/tahfidz', function () {
    return view('tahfidz');
})->name('tahfidz');

// Halaman Biaya (dengan data paket dari DB)
Route::get('/biaya', function () {
    $programs = Program::orderBy('price', 'asc')->get();

    return view('biaya', compact('programs'));
})->name('biaya');

// Halaman Bergabung (Pendaftaran Pendamping / Guru Al-Qur'an)
Route::get('/bergabung', [RegisterMentorController::class, 'create'])->name('bergabung');
Route::post('/bergabung', [RegisterMentorController::class, 'store'])->middleware('guest');

// ==========================================
// 📌 ROUTE ADMIN (HARUS ADA)
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/programs', function () {
            return view('admin.programs.index');
        })->name('programs.index');

        Route::get('/mentors', function () {
            return view('admin.mentors.index');
        })->name('mentors.index');

        Route::get('/students', function () {
            return view('admin.students.index');
        })->name('students.index');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

// ==========================================
// 📌 ROUTE MENTOR / GURU
// ==========================================
Route::middleware(['auth', 'role:mentor'])
    ->prefix('mentor')
    ->name('mentor.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

// ==========================================
// 📌 ROUTE PARENT / ORANG TUA
// ==========================================
Route::middleware(['auth', 'role:parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

// ==========================================
// 📌 ROUTE STUDENT / SANTRI
// ==========================================
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

// Dashboard Route (Role Based Redirect)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->isMentor()) {
        return redirect()->route('mentor.dashboard');
    }

    if ($user->isParent()) {
        return redirect()->route('parent.dashboard');
    }

    if ($user->isStudent()) {
        return redirect()->route('student.dashboard');
    }

    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/report/download/{student?}', [ReportController::class, 'downloadProgress'])
    ->middleware(['auth'])
    ->name('report.download');

require __DIR__.'/auth.php';
