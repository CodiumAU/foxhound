<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class InterceptorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (! defined('INTERCEPTOR_PATH')) {
            define('INTERCEPTOR_PATH', realpath(__DIR__.'/../'));
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            INTERCEPTOR_PATH.'/public' => public_path('vendor/interceptor'),
        ], ['interceptor-assets']);
    }
}
