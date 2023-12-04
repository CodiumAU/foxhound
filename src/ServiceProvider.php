<?php

namespace Foxhound;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Notifications\Events\NotificationSending;
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
            path: FOXHOUND_PATH.'/config/foxhound.php',
            key: 'foxhound'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(FOXHOUND_PATH.'/resources/views', 'foxhound');

        $this->offerPublishing();
        $this->bootRoutes();
        $this->bootEventListeners();
        $this->bootCommands();
    }

    /**
     * Boot the Foxhound event listeners.
     */
    protected function bootEventListeners(): void
    {
        Event::listen(NotificationSending::class, Listeners\NotificationSending\InterceptNotification::class);
    }

    /**
     * Boot the Foxhound routes.
     */
    protected function bootRoutes(): void
    {
        // Group and load the API routes.
        Route::prefix('foxhound/api')->group(fn () => $this->loadRoutesFrom(FOXHOUND_PATH.'/routes/api.php'));

        // Define the global Foxhound route for the SPA.
        Route::view('foxhound/{path?}', 'foxhound::index')->where('path', '.*');
    }

    /**
     * Boot the Foxhound commands.
     */
    protected function bootCommands(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }

    /**
     * Offers publishing for assets and configuration when running in console.
     */
    protected function offerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes(
            paths: [FOXHOUND_PATH.'/config/foxhound.php' => config_path('foxhound.php')],
            groups: 'foxhound-config'
        );

        $this->publishes(
            paths: [FOXHOUND_PATH.'/public' => public_path('vendor/foxhound')],
            groups: 'foxhound-assets'
        );
    }
}
