<?php

namespace App\Providers;

use App\Helpers\TimezoneHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Format a datetime in the current user's timezone for Blade views.
        // Usage: @userTz($datetime, 'd/m/Y h:i A') or @userTz($date . ' ' . $time, 'd/m/Y')
        Blade::directive('userTz', function ($expression) {
            return "<?php echo e(\App\Helpers\TimezoneHelper::formatInUserTz($expression)); ?>";
        });
    }
}
