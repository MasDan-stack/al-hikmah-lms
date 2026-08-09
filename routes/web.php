<?php

use App\Http\Controllers\Admin\DashboardController;
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
        
        // Nanti tambahkan CRUD routes:
        // Route::resource('students', StudentController::class);
        // Route::resource('mentors', MentorController::class);
        // Route::resource('sessions', SessionController::class);
    });

// Redirect setelah login
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';