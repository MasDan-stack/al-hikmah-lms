<?php

namespace App\Providers;

use App\Events\StudentAssignedToMentor;
use App\Listeners\LogMentorActivityListener;
use App\Listeners\SendAssignmentNotificationListener;
use App\Models\Gallery;
use App\Observers\GalleryObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
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

        Gallery::observe(GalleryObserver::class);

        Event::listen(
            StudentAssignedToMentor::class,
            SendAssignmentNotificationListener::class
        );

        Event::listen(
            StudentAssignedToMentor::class,
            LogMentorActivityListener::class
        );
    }
}
