<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Halaman Home
Route::get('/', function () {
    return view('home');
})->name('home');

// Halaman Tentang Kami
Route::get('/tentang-kami', function () {
    return view('tentang-kami');
})->name('tentang-kami');

Route::get('/program', function () {
    return view('program');
})->name('program');

Route::get('/metode', function () {
    return view('metode');
})->name('metode');

Route::get('/tahfidz', function () {
    return view('tahfidz');
})->name('tahfidz');

Route::get('/biaya', function () {
    return view('biaya');
})->name('biaya');
// ==========================================
// 📌 ROUTE ADMIN (HARUS ADA)
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
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
