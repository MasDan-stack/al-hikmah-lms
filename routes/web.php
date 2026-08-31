<?php

use App\Http\Controllers\Admin\ActiveEnrollmentController;
use App\Http\Controllers\Admin\AdminAlertController;
use App\Http\Controllers\Admin\AdminBadgeController;
use App\Http\Controllers\Admin\AdminBlogCategoryController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminBlogTagController;
use App\Http\Controllers\Admin\AdminBroadcastController;
use App\Http\Controllers\Admin\AdminGamificationController;
use App\Http\Controllers\Admin\AdminMentorLeaveController;
use App\Http\Controllers\Admin\AdminMentorPerformanceController;
use App\Http\Controllers\Admin\AdminProbationController;
use App\Http\Controllers\Admin\AdminRecruitmentController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminRevenueController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\GalleryCategoryController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\MentorAvailabilityController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\RecruitmentApiController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AnalyticsApiController;
use App\Http\Controllers\Api\PakasirWebhookController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Mentor\AvailabilityController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\MentorLeaveController;
use App\Http\Controllers\Mentor\MentorMessageController;
use App\Http\Controllers\Mentor\MentorQuestionController;
use App\Http\Controllers\Mentor\MentorRecruitmentTestController;
use App\Http\Controllers\Mentor\MentorSelfServiceController;
use App\Http\Controllers\Mentor\MentorTargetController;
use App\Http\Controllers\Mentor\ProgressController as MentorProgressController;
use App\Http\Controllers\Mentor\ReportController as MentorReportController;
use App\Http\Controllers\Mentor\SessionController as MentorSessionController;
use App\Http\Controllers\Mentor\StudentController as MentorStudentController;
use App\Http\Controllers\Parent\EnrollmentController as ParentEnrollmentController;
use App\Http\Controllers\Parent\ParentChildController;
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentFeedbackController;
use App\Http\Controllers\Parent\ParentMessageController;
use App\Http\Controllers\Parent\ParentPaymentController;
use App\Http\Controllers\Parent\ParentProfileController;
use App\Http\Controllers\Parent\ParentScheduleController;
use App\Http\Controllers\Public\MentorApplicationController;
use App\Http\Controllers\PublicBlogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentGamificationController;
use App\Http\Controllers\Student\StudentPasswordController;
use App\Http\Controllers\Student\StudentTargetController;
use App\Models\Article;
use App\Models\Mentor;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

// Halaman Home
Route::get('/', function () {
    $latestArticles = Article::published()
        ->with(['category', 'user'])
        ->latest('published_at')
        ->take(3)
        ->get();

    return view('home', compact('latestArticles'));
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

// Halaman Bergabung (Pendaftaran Pendamping / Guru Al-Qur'an) - V8.3
Route::get('/bergabung', [MentorApplicationController::class, 'create'])->name('bergabung');
Route::post('/bergabung', [MentorApplicationController::class, 'store'])->name('mentor.recruitment.store');
Route::get('/cek-status-lamaran', [MentorApplicationController::class, 'status'])->name('mentor.recruitment.status');
Route::post('/cek-status-lamaran', [MentorApplicationController::class, 'checkStatus'])->name('mentor.recruitment.check-status');

// Halaman Roadmap / Peta Alur Belajar
Route::get('/roadmap', [LandingController::class, 'roadmap'])->name('roadmap');

// Halaman FAQ / Tanya Jawab Interaktif
Route::get('/faq', function () {
    return view('faq');
})->name('faq');

// Halaman Hubungi Kami (Contact Form)
Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
Route::post('/kontak', [ContactController::class, 'store'])->name('contact.store');

// Halaman Galeri Interaktif Publik
Route::get('/galeri', [LandingController::class, 'galeri'])->name('galeri');
Route::post('/galeri/{id}/view', [LandingController::class, 'incrementView'])->name('galeri.view');

// ==========================================
// 📖 PUBLIC BLOG & SEO SITEMAP ROUTES
// ==========================================
Route::get('/sitemap.xml', [PublicBlogController::class, 'sitemap'])->name('sitemap');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [PublicBlogController::class, 'index'])->name('index');
    Route::get('/kategori/{slug}', [PublicBlogController::class, 'category'])->name('category');
    Route::get('/tag/{slug}', [PublicBlogController::class, 'tag'])->name('tag');
    Route::get('/{slug}', [PublicBlogController::class, 'show'])->name('show');
    Route::post('/{slug}/share', [PublicBlogController::class, 'trackShare'])->name('share');
});

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

        // ==========================================
        // MANAJEMEN REKRUTMEN MENTOR (V8.3)
        // ==========================================
        // Recruitment APIs for ApexCharts
        Route::get('/api/recruitment/funnel', [RecruitmentApiController::class, 'funnel'])->name('api.recruitment.funnel');
        Route::get('/api/recruitment/daily-trend', [RecruitmentApiController::class, 'dailyTrend'])->name('api.recruitment.daily-trend');

        Route::prefix('mentors/recruitment')->name('recruitment.')->group(function () {
            // Applications
            Route::get('/applications', [AdminRecruitmentController::class, 'applications'])->name('applications.index');
            Route::get('/applications/export', [AdminRecruitmentController::class, 'exportCsv'])->name('applications.export');
            Route::get('/applications/{id}', [AdminRecruitmentController::class, 'showApplication'])->name('applications.show');
            Route::get('/applications/{id}/documents/{documentId}', [AdminRecruitmentController::class, 'downloadDocument'])->name('applications.document');
            Route::post('/applications/{id}/approve-document', [AdminRecruitmentController::class, 'approveDocument'])->name('applications.approveDocument');
            Route::post('/applications/{id}/reject', [AdminRecruitmentController::class, 'rejectApplication'])->name('applications.reject');
            Route::post('/applications/{id}/schedule-interview', [AdminRecruitmentController::class, 'scheduleInterview'])->name('applications.scheduleInterview');
            Route::post('/applications/{id}/accept', [AdminRecruitmentController::class, 'acceptApplication'])->name('applications.accept');

            // Test Sessions
            Route::get('/test-sessions', [AdminRecruitmentController::class, 'testSessions'])->name('tests.index');
            Route::get('/test-sessions/{id}', [AdminRecruitmentController::class, 'showTest'])->name('tests.show');
            Route::post('/applications/{id}/generate-test', [AdminRecruitmentController::class, 'generateTest'])->name('tests.generate');
            Route::post('/test-sessions/{id}/evaluate', [AdminRecruitmentController::class, 'evaluateTest'])->name('tests.evaluate');
        });

        // ==========================================
        // MANAJEMEN MENTOR & PROBATION
        // ==========================================
        Route::get('/mentors', function () {
            return view('admin.mentors.index');
        })->name('mentors.index');

        Route::prefix('mentors/probation')->name('mentors.probation.')->group(function () {
            Route::get('/', [AdminProbationController::class, 'index'])->name('index');
            Route::get('/{id}', [AdminProbationController::class, 'show'])->name('show');
            Route::post('/{id}/scores', [AdminProbationController::class, 'updateScores'])->name('updateScores');
            Route::post('/{id}/complete', [AdminProbationController::class, 'completeProbation'])->name('complete');
        });

        // Mentor Leave & Substitute Management
        Route::prefix('mentors/leaves')->name('mentors.leaves.')->group(function () {
            Route::get('/', [AdminMentorLeaveController::class, 'index'])->name('index');
            Route::post('/{leave}/approve', [AdminMentorLeaveController::class, 'approve'])->name('approve');
            Route::post('/{leave}/reject', [AdminMentorLeaveController::class, 'reject'])->name('reject');
            Route::delete('/{leave}', [AdminMentorLeaveController::class, 'destroy'])->name('destroy');
        });

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
        Route::post('/enrollments/{id}/assign-recommended', [AdminEnrollmentController::class, 'assignRecommended'])->name('enrollments.assign-recommended');
        Route::post('/enrollments/bulk-assign', [AdminEnrollmentController::class, 'bulkAssign'])->name('enrollments.bulk-assign');
        Route::post('/enrollments/{id}/offer', [AdminEnrollmentController::class, 'offerAlternative'])->name('enrollments.offer');
        Route::post('/enrollments/bulk-accept', [AdminEnrollmentController::class, 'bulkAccept'])->name('enrollments.bulk-accept');
        Route::post('/enrollments/{id}/cancel', [AdminEnrollmentController::class, 'cancel'])->name('enrollments.cancel');

        // Manajemen Galeri Kegiatan & Kategori
        Route::post('/gallery-categories/reorder', [GalleryCategoryController::class, 'reorder'])->name('gallery-categories.reorder');
        Route::post('/gallery-categories/{id}/toggle', [GalleryCategoryController::class, 'toggle'])->name('gallery-categories.toggle');
        Route::post('/gallery-categories/{id}/restore', [GalleryCategoryController::class, 'restore'])->name('gallery-categories.restore');
        Route::delete('/gallery-categories/{id}/force-delete', [GalleryCategoryController::class, 'forceDelete'])->name('gallery-categories.force-delete');
        Route::resource('gallery-categories', GalleryCategoryController::class)->except(['show', 'create', 'edit']);

        Route::post('/galleries/reorder', [GalleryController::class, 'reorder'])->name('galleries.reorder');
        Route::post('/galleries/{id}/toggle', [GalleryController::class, 'toggle'])->name('galleries.toggle');
        Route::post('/galleries/{id}/restore', [GalleryController::class, 'restore'])->name('galleries.restore');
        Route::delete('/galleries/{id}/force-delete', [GalleryController::class, 'forceDelete'])->name('galleries.force-delete');
        Route::resource('galleries', GalleryController::class)->except(['show']);

        // Contact Messages Management
        Route::get('/contacts', [AdminContactMessageController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{id}', [AdminContactMessageController::class, 'show'])->name('contacts.show');
        Route::put('/contacts/{id}', [AdminContactMessageController::class, 'updateStatus'])->name('contacts.update-status');
        Route::delete('/contacts/{id}', [AdminContactMessageController::class, 'destroy'])->name('contacts.destroy');

        // Blog CMS Management
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::post('/upload-image', [AdminBlogController::class, 'uploadImage'])->name('upload-image');
            Route::get('/trash', [AdminBlogController::class, 'trash'])->name('trash');
            Route::post('/{id}/restore', [AdminBlogController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [AdminBlogController::class, 'forceDelete'])->name('force-delete');
            Route::post('/{id}/toggle-status', [AdminBlogController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{id}/toggle-featured', [AdminBlogController::class, 'toggleFeatured'])->name('toggle-featured');

            Route::get('/', [AdminBlogController::class, 'index'])->name('index');
            Route::get('/create', [AdminBlogController::class, 'create'])->name('create');
            Route::post('/', [AdminBlogController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminBlogController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminBlogController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminBlogController::class, 'destroy'])->name('destroy');

            Route::resource('categories', AdminBlogCategoryController::class)->except(['show']);
            Route::resource('tags', AdminBlogTagController::class)->except(['show']);
        });

        // Gamifikasi & Badges Management
        Route::resource('badges', AdminBadgeController::class)->except(['show', 'create', 'edit']);
        Route::get('/gamification/settings', [AdminGamificationController::class, 'settings'])->name('gamification.settings');
        Route::post('/gamification/settings', [AdminGamificationController::class, 'updateSettings'])->name('gamification.settings.update');
        Route::get('/gamification/leaderboard', [AdminGamificationController::class, 'leaderboard'])->name('gamification.leaderboard');
        Route::post('/gamification/refresh-leaderboard', [AdminGamificationController::class, 'refreshLeaderboard'])->name('gamification.refresh');

        // ==========================================
        // 📊 ANALYTICS & OPERATIONAL INTELLIGENCE (v8.2)
        // ==========================================
        // Revenue Analytics & Breakdown
        Route::get('/revenue', [AdminRevenueController::class, 'index'])->name('revenue.index');

        // Staff & HR Workload
        Route::get('/staff', [AdminStaffController::class, 'index'])->name('staff.index');

        // Operational Alerts Center
        Route::get('/alerts', [AdminAlertController::class, 'index'])->name('alerts.index');

        // Reports Generator & Dual Export (Excel & PDF)
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-excel', [AdminReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/export-pdf', [AdminReportController::class, 'exportPdf'])->name('reports.export-pdf');

        // WhatsApp Mass Broadcast System
        Route::get('/broadcast', [AdminBroadcastController::class, 'index'])->name('broadcast.index');
        Route::post('/broadcast/send', [AdminBroadcastController::class, 'send'])->name('broadcast.send');
        Route::post('/broadcast/preview', [AdminBroadcastController::class, 'preview'])->name('broadcast.preview');

        // Mentor Performance Dashboard, Scorecard 360, & AI Coaching (v2.0)
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/mentors', [AdminMentorPerformanceController::class, 'index'])->name('mentors.index');
            Route::get('/mentors/export-excel', [AdminMentorPerformanceController::class, 'exportExcel'])->name('mentors.export-excel');
            Route::get('/mentors/{id}', [AdminMentorPerformanceController::class, 'show'])->name('mentors.show');
            Route::post('/mentors/{id}/recalculate', [AdminMentorPerformanceController::class, 'recalculate'])->name('mentors.recalculate');
            Route::post('/mentors/{id}/send-wa', [AdminMentorPerformanceController::class, 'sendWhatsAppReport'])->name('mentors.send-wa');
        });
    });

// ==========================================
// 📊 ANALYTICS AJAX JSON API (APEXCHARTS)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('api/analytics')->name('api.analytics.')->group(function () {
    Route::get('/revenue-trend', [AnalyticsApiController::class, 'revenueTrend'])->name('revenue-trend');
    Route::get('/program-breakdown', [AnalyticsApiController::class, 'programBreakdown'])->name('program-breakdown');
    Route::get('/payment-status', [AnalyticsApiController::class, 'paymentStatus'])->name('payment-status');
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

        // Target Hafalan Management
        Route::get('/targets', [MentorTargetController::class, 'index'])->name('targets.index');
        Route::get('/targets/create', [MentorTargetController::class, 'create'])->name('targets.create');
        Route::post('/targets', [MentorTargetController::class, 'store'])->name('targets.store');
        Route::post('/targets/bulk', [MentorTargetController::class, 'bulkAssign'])->name('targets.bulk-assign');
        Route::patch('/targets/{target}/evaluate', [MentorTargetController::class, 'evaluate'])->name('targets.evaluate');

        // Mentor Messages & Chat with Parents
        Route::get('/messages', [MentorMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/create', [MentorMessageController::class, 'create'])->name('messages.create');
        Route::get('/messages/chat/{parent_user_id}', [MentorMessageController::class, 'chat'])->name('messages.chat');
        Route::post('/messages', [MentorMessageController::class, 'store'])->name('messages.store');

        // Bank Soal & AI Generator Routes
        Route::get('/questions', [MentorQuestionController::class, 'index'])->name('questions.index');
        Route::get('/questions/generate', [MentorQuestionController::class, 'create'])->name('questions.generate');
        Route::match(['get', 'post'], '/questions/print', [MentorQuestionController::class, 'print'])->name('questions.print');
        Route::post('/questions/generate-preview', [MentorQuestionController::class, 'preview'])
            ->middleware('throttle:10,1')
            ->name('questions.preview');
        Route::post('/questions/store-batch', [MentorQuestionController::class, 'storeBatch'])->name('questions.store-batch');
        Route::get('/questions/trash', [MentorQuestionController::class, 'trash'])->name('questions.trash');
        Route::post('/questions/{id}/restore', [MentorQuestionController::class, 'restore'])->name('questions.restore');
        Route::delete('/questions/{id}/force-delete', [MentorQuestionController::class, 'forceDelete'])->name('questions.force-delete');
        Route::delete('/questions/{question}', [MentorQuestionController::class, 'destroy'])->name('questions.destroy');

        // Mentor Leave & Substitute Requests
        Route::get('/leaves', [MentorLeaveController::class, 'index'])->name('leaves.index');
        Route::post('/leaves', [MentorLeaveController::class, 'store'])->name('leaves.store');
        Route::delete('/leaves/{leave}', [MentorLeaveController::class, 'destroy'])->name('leaves.destroy');

        // Mentor Self-Service Performance Portal, Goals & Reflection (v2.0)
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/', [MentorSelfServiceController::class, 'myPerformance'])->name('index');
            Route::get('/goals', [MentorSelfServiceController::class, 'goals'])->name('goals');
            Route::post('/goals', [MentorSelfServiceController::class, 'storeGoal'])->name('goals.store');
            Route::get('/self-assessment', [MentorSelfServiceController::class, 'selfAssessment'])->name('self-assessment');
            Route::post('/self-assessment', [MentorSelfServiceController::class, 'storeSelfAssessment'])->name('self-assessment.store');
        });

        // Portal Rekrutmen & Ujian Tes Calon Guru
        Route::get('/recruitment/test/{sessionId}', [MentorRecruitmentTestController::class, 'showTest'])->name('recruitment.take-test');
        Route::post('/recruitment/test/{sessionId}/submit', [MentorRecruitmentTestController::class, 'submitTest'])->name('recruitment.submit-test');
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

        // Post-Session Feedback & Quick Chips
        Route::post('/feedbacks', [ParentFeedbackController::class, 'store'])->name('feedbacks.store');

        // D. Modul Pembayaran
        Route::get('/payments', [ParentPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/history', [ParentPaymentController::class, 'history'])->name('payments.history');
        Route::get('/payments/{id}', [ParentPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{id}/pay', [ParentPaymentController::class, 'payOnline'])->name('payments.pay');
        Route::get('/payments/{id}/status', [ParentPaymentController::class, 'checkStatus'])->name('payments.status');
        Route::post('/payments/{id}/cancel', [ParentPaymentController::class, 'cancelPayment'])->name('payments.cancel');
        Route::get('/payments/{id}/download', [ParentPaymentController::class, 'downloadInvoice'])->name('payments.download');

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

        // =================================================================
        // 🔒 SUB-ROUTE KHUSUS YANG SUDAH LUNAS / AKTIF (PROTECTED BY GUARD)
        // =================================================================
        Route::middleware(['parent.paid'])->group(function () {
            // Modul Anak & Capaian Belajar
            Route::get('/children', [ParentChildController::class, 'index'])->name('children.index');
            Route::get('/children/{id}', [ParentChildController::class, 'show'])->name('children.show');
            Route::get('/children/{id}/report', [ParentChildController::class, 'exportReport'])->name('children.report');
            Route::post('/children/{id}/reset-password', [ParentChildController::class, 'requestPasswordReset'])->name('children.reset-password');
            Route::post('/enroll-tahfidz', [ParentChildController::class, 'enrollTahfidz'])->name('enroll-tahfidz');

            // Modul Jadwal Belajar
            Route::get('/schedules', [ParentScheduleController::class, 'index'])->name('schedules.index');
            Route::get('/schedules/list', [ParentScheduleController::class, 'list'])->name('schedules.list');
            Route::get('/schedules/{id}', [ParentScheduleController::class, 'show'])->name('schedules.show');
            Route::post('/schedules/{id}/confirm', [ParentScheduleController::class, 'confirm'])->name('schedules.confirm');

            // Modul Komunikasi
            Route::get('/messages', [ParentMessageController::class, 'index'])->name('messages.index');
            Route::get('/messages/create', [ParentMessageController::class, 'create'])->name('messages.create');
            Route::get('/messages/{mentor_id}', [ParentMessageController::class, 'chat'])->name('messages.chat');
            Route::post('/messages', [ParentMessageController::class, 'store'])->name('messages.store');
        });
    });

// ==========================================
// 📌 ROUTE STUDENT / SANTRI
// ==========================================
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Target Hafalan
        Route::get('/targets/today', [StudentDashboardController::class, 'targetHariIni'])->name('targets.today');
        Route::post('/targets', [StudentTargetController::class, 'store'])->name('targets.store');
        Route::put('/targets/{target}', [StudentTargetController::class, 'update'])->name('targets.update');
        Route::patch('/targets/{target}/complete', [StudentTargetController::class, 'markComplete'])->name('targets.complete');

        // Milestone Targets
        Route::get('/milestones', [StudentDashboardController::class, 'milestones'])->name('milestones');
        Route::post('/milestones', [StudentTargetController::class, 'storeMilestone'])->name('milestones.store');

        // Progress Hafalan
        Route::get('/progress/juz', [StudentDashboardController::class, 'progressPerJuz'])->name('progress.juz');

        // Leaderboard
        Route::get('/leaderboard', [StudentDashboardController::class, 'leaderboard'])->name('leaderboard');

        // Badges & Gamifikasi
        Route::get('/badges', [StudentDashboardController::class, 'badges'])->name('badges');
        Route::get('/badges/hall-of-fame/{badgeCode}', [StudentGamificationController::class, 'hallOfFame'])->name('badges.hall-of-fame');
        Route::get('/stats', [StudentGamificationController::class, 'myStats'])->name('stats');
        Route::post('/privacy/toggle', [StudentGamificationController::class, 'togglePrivacy'])->name('privacy.toggle');

        // Password Management
        Route::get('/password', [StudentPasswordController::class, 'show'])->name('password.index');
        Route::post('/password/reset', [StudentPasswordController::class, 'reset'])->name('password.reset');
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

// ==========================================
// 💳 PAKASIR PAYMENT GATEWAY WEBHOOK
// ==========================================
Route::post('/api/webhook/pakasir', [PakasirWebhookController::class, 'handle'])->name('api.webhook.pakasir');
Route::post('/webhook/pakasir', [PakasirWebhookController::class, 'handle']);

require __DIR__.'/auth.php';
