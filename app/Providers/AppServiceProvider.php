<?php

namespace App\Providers;

use App\servecs\sms\adapter\Kavenegar\Sms;
use App\servecs\sms\SmsInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SmsInterface::class ,Sms::class );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191); //Solved by increasing StringLength
    }
}
