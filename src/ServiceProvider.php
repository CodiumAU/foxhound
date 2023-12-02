<?php

namespace Foxhound;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (! defined('FOXHOUND_PATH')) {
            define('FOXHOUND_PATH', realpath(__DIR__.'/../'));
        }

        $this->mergeConfigFrom(
            FOXHOUND_PATH.'/config/foxhound.php', 'foxhound'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            FOXHOUND_PATH.'/public' => public_path('vendor/foxhound'),
        ], ['foxhound-assets']);

        $this->loadViewsFrom(FOXHOUND_PATH.'/resources/views', 'foxhound');

        Route::prefix('foxhound/api')->group(fn () => $this->loadRoutesFrom(FOXHOUND_PATH.'/routes/api.php'));
        Route::view('foxhound/{any?}', 'foxhound::index')->where('any', '.*')->name('foxhound');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                FOXHOUND_PATH.'/config/foxhound.php' => config_path('foxhound.php'),
            ], 'foxhound-config');
        }
    }
}
