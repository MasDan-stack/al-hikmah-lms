<?php

namespace App\Providers;

use App\Events\StudentAssignedToMentor;
use App\Listeners\LogMentorActivityListener;
use App\Listeners\SendAssignmentNotificationListener;
use App\Models\Gallery;
use App\Models\Progress;
use App\Observers\GalleryObserver;
use App\Observers\ProgressObserver;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('Helpers/settings.php'))) {
            require_once app_path('Helpers/settings.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'id', 'ind');

        // Enforce HTTPS in production environments
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Configure named rate limiters for security hardening
        $this->configureRateLimiting();

        Gallery::observe(GalleryObserver::class);
        Progress::observe(ProgressObserver::class);

        Event::listen(
            StudentAssignedToMentor::class,
            SendAssignmentNotificationListener::class
        );

        Event::listen(
            StudentAssignedToMentor::class,
            LogMentorActivityListener::class
        );
    }

    /**
     * Configure rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Global API rate limit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Authentication rate limit (5 attempts per minute per email + IP)
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = strtolower((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)->by($throttleKey);
        });

        // Public Contact form submissions
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Free placement test booking
        RateLimiter::for('trial_booking', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Mentor recruitment registration
        RateLimiter::for('mentor_apply', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Public applicant status checker (prevent enumeration)
        RateLimiter::for('status_tracker', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // AI Question generation (protect API token budget)
        RateLimiter::for('ai_generation', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
