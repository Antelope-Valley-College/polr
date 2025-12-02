<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // If the protocol is set to HTTPS, force HTTPS scheme for URLs
        if (env('APP_PROTOCOL') === 'https://') {
            \URL::forceScheme('https');
        }
    }
}
