<?php

namespace Blubear\LaravelOtp;

use Illuminate\Support\ServiceProvider;

class LaravelOptProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-otp.php', 'laravel-otp');
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'otp');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/otp')
            ], 'views');
            $this->publishes([
                __DIR__ . '/../config/laravel-otp.php' => config_path('laravel-otp.php'),
            ], 'config');
        }
    }
}
