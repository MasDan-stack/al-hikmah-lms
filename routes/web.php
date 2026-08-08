<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// Halaman Home
Route::get('/', function () {
    return view('home');
})->name('home');

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