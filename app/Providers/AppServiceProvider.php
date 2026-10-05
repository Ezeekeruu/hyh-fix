<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Behind Render's TLS proxy the app sees plain HTTP, so generated
        // asset/form URLs would come out as http:// and browsers block them
        // as mixed content. Force HTTPS in production regardless of APP_URL.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
