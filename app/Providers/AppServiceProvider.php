<?php

namespace App\Providers;

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
        // Disable phiki syntax highlighting to prevent FailedToInitializePatternSearchException
        if (class_exists('Phiki\Phiki')) {
            try {
                // This will help prevent the syntax highlighting error
                ini_set('mbstring.func_overload', '0');
            } catch (\Exception $e) {
                // Ignore if mbstring is not available
            }
        }
    }
}
