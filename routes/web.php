<?php

use App\Http\Controllers\Admin\ActiveEnrollmentController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\MentorAvailabilityController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\RegisterMentorController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Mentor\AvailabilityController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\MentorMessageController;
use App\Http\Controllers\Mentor\ProgressController as MentorProgressController;
use App\Http\Controllers\Mentor\ReportController as MentorReportController;
use App\Http\Controllers\Mentor\SessionController as MentorSessionController;
use App\Http\Controllers\Mentor\StudentController as MentorStudentController;
use App\Http\Controllers\Parent\EnrollmentController as ParentEnrollmentController;
use App\Http\Controllers\Parent\ParentChildController;
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentMessageController;
use App\Http\Controllers\Parent\ParentPaymentController;
use App\Http\Controllers\Parent\ParentProfileController;
use App\Http\Controllers\Parent\ParentScheduleController;
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

// Halaman Program Belajar (dengan data program dari DB via LandingController)
Route::get('/program', [LandingController::class, 'program'])->name('program');

Route::get('/metode', function () {
    return view('metode');
})->name('metode');

Route::get('/tahfidz', function () {
    return view('tahfidz');
})->name('tahfidz');
Route::post('/tahfidz/pre-register', [RegisteredUserController::class, 'preRegisterTahfidz'])->name('tahfidz.pre-register');

// Halaman Biaya (dengan data paket dari DB via LandingController)
Route::get('/biaya', [LandingController::class, 'biaya'])->name('biaya');

// Pre-Register Khusus Program
Route::post('/program/pre-register', [RegisteredUserController::class, 'preRegisterProgram'])->name('program.pre-register');

// Halaman Bergabung (Pendaftaran Pendamping / Guru Al-Qur'an)
Route::get('/bergabung', [RegisterMentorController::class, 'create'])->name('bergabung');
Route::post('/bergabung', [RegisterMentorController::class, 'store'])->middleware('guest');

// Halaman Roadmap / Peta Alur Belajar
Route::get('/roadmap', [LandingController::class, 'roadmap'])->name('roadmap');

// Halaman FAQ / Tanya Jawab Interaktif
Route::get('/faq', function () {
    return view('faq');
})->name('faq');

// Halaman Hubungi Kami (Contact Form)
Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
Route::post('/kontak', [ContactController::class, 'store'])->name('contact.store');

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

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments', [AdminPaymentController::class, 'store'])->name('payments.store');
        Route::put('/payments/{id}', [AdminPaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{id}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('/payments/send-reminder', [AdminPaymentController::class, 'sendReminder'])->name('payments.send-reminder');

        // Mentor Availability & Allocation Management
        Route::get('/mentors/availability', [MentorAvailabilityController::class, 'index'])->name('mentors.availability');
        Route::get('/mentors/available-api', [MentorAvailabilityController::class, 'getAvailableMentors'])->name('mentors.available-api');
        Route::post('/mentors/assign-student', [MentorAvailabilityController::class, 'assignStudent'])->name('mentors.assign-student');
        Route::post('/mentors/unassign-student', [MentorAvailabilityController::class, 'unassignStudent'])->name('mentors.unassign-student');

        // User Management & Role Access Control
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Flexible Enrollment & Schedule Negotiation
        Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/enrollments/export', [AdminEnrollmentController::class, 'export'])->name('enrollments.export');
        Route::get('/active-enrollments', [ActiveEnrollmentController::class, 'index'])->name('active-enrollments.index');
        Route::get('/enrollments/{id}/edit', [AdminEnrollmentController::class, 'edit'])->name('enrollments.edit');
        Route::post('/enrollments/{id}/accept', [AdminEnrollmentController::class, 'accept'])->name('enrollments.accept');
        Route::post('/enrollments/{id}/offer', [AdminEnrollmentController::class, 'offerAlternative'])->name('enrollments.offer');
        Route::post('/enrollments/bulk-accept', [AdminEnrollmentController::class, 'bulkAccept'])->name('enrollments.bulk-accept');
        Route::post('/enrollments/{id}/cancel', [AdminEnrollmentController::class, 'cancel'])->name('enrollments.cancel');

        // Contact Messages Management
        Route::get('/contacts', [AdminContactMessageController::class, 'index'])->name('contacts.index');
        Route::put('/contacts/{id}', [AdminContactMessageController::class, 'updateStatus'])->name('contacts.update-status');
        Route::delete('/contacts/{id}', [AdminContactMessageController::class, 'destroy'])->name('contacts.destroy');
    });

// ==========================================
// 📌 ROUTE MENTOR / GURU
// ==========================================
Route::middleware(['auth', 'role:mentor'])
    ->prefix('mentor')
    ->name('mentor.')
    ->group(function () {
        Route::get('/dashboard', [MentorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/sessions', [MentorSessionController::class, 'index'])->name('sessions.index');
        Route::post('/sessions/{id}/status', [MentorSessionController::class, 'updateStatus'])->name('sessions.update-status');
        Route::get('/students', [MentorStudentController::class, 'index'])->name('students.index');
        Route::get('/students/parents', [MentorStudentController::class, 'parents'])->name('students.parents');
        Route::get('/students/{id}', [MentorStudentController::class, 'show'])->name('students.show');
        Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability/update-bulk', [AvailabilityController::class, 'updateBulk'])->name('availability.update-bulk');
        Route::get('/progress/create', [MentorProgressController::class, 'create'])->name('progress.create');
        Route::post('/progress', [MentorProgressController::class, 'store'])->name('progress.store');
        Route::get('/progress/bulk', [MentorProgressController::class, 'createBulk'])->name('progress.bulk-create');
        Route::post('/progress/bulk', [MentorProgressController::class, 'storeBulk'])->name('progress.bulk-store');
        Route::get('/reports/export', [MentorReportController::class, 'export'])->name('reports.export');
        Route::get('/profile', [MentorDashboardController::class, 'profile'])->name('profile');

        // Mentor Messages & Chat with Parents
        Route::get('/messages', [MentorMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/create', [MentorMessageController::class, 'create'])->name('messages.create');
        Route::get('/messages/chat/{parent_user_id}', [MentorMessageController::class, 'chat'])->name('messages.chat');
        Route::post('/messages', [MentorMessageController::class, 'store'])->name('messages.store');
    });

// ==========================================
// 📌 ROUTE PARENT / ORANG TUA (MODUL A - F)
// ==========================================
Route::middleware(['auth', 'role:parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        // A. Dashboard Utama
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        // B. Modul Anak & Progress
        Route::get('/children', [ParentChildController::class, 'index'])->name('children.index');
        Route::get('/children/{id}', [ParentChildController::class, 'show'])->name('children.show');
        Route::get('/children/{id}/report', [ParentChildController::class, 'exportReport'])->name('children.report');
        Route::post('/enroll-tahfidz', [ParentChildController::class, 'enrollTahfidz'])->name('enroll-tahfidz');

        // C. Modul Jadwal Belajar
        Route::get('/schedules', [ParentScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/list', [ParentScheduleController::class, 'list'])->name('schedules.list');
        Route::get('/schedules/{id}', [ParentScheduleController::class, 'show'])->name('schedules.show');
        Route::post('/schedules/{id}/confirm', [ParentScheduleController::class, 'confirm'])->name('schedules.confirm');

        // D. Modul Pembayaran
        Route::get('/payments', [ParentPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/history', [ParentPaymentController::class, 'history'])->name('payments.history');
        Route::get('/payments/{id}', [ParentPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{id}/pay', [ParentPaymentController::class, 'payOnline'])->name('payments.pay');
        Route::get('/payments/{id}/download', [ParentPaymentController::class, 'downloadInvoice'])->name('payments.download');

        // E. Modul Komunikasi
        Route::get('/messages', [ParentMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/create', [ParentMessageController::class, 'create'])->name('messages.create');
        Route::get('/messages/{mentor_id}', [ParentMessageController::class, 'chat'])->name('messages.chat');
        Route::post('/messages', [ParentMessageController::class, 'store'])->name('messages.store');

        // F. Modul Profil & Pengaturan
        Route::get('/profile', [ParentProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ParentProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/notifications', [ParentProfileController::class, 'notifications'])->name('profile.notifications');
        Route::post('/profile/notifications', [ParentProfileController::class, 'updateNotifications'])->name('profile.update-notifications');
        Route::post('/profile/password', [ParentProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/profile/children', [ParentProfileController::class, 'children'])->name('profile.children');
        Route::post('/profile/children', [ParentProfileController::class, 'storeChild'])->name('profile.store-child');

        // G. Modul Pendaftaran & Negosiasi Jadwal
        Route::get('/enrollments', [ParentEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/enrollments/create', [ParentEnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/enrollments', [ParentEnrollmentController::class, 'store'])->name('enrollments.store');
        Route::get('/enrollments/{id}', [ParentEnrollmentController::class, 'show'])->name('enrollments.show');
        Route::post('/enrollments/{id}/accept-offer', [ParentEnrollmentController::class, 'acceptOffer'])->name('enrollments.accept-offer');
        Route::post('/enrollments/{id}/reject-offer', [ParentEnrollmentController::class, 'rejectOffer'])->name('enrollments.reject-offer');
    });

// ==========================================
// 📌 ROUTE STUDENT / SANTRI
// ==========================================
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('student.dashboard');
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
