<?php

namespace Foxhound;

use Foxhound\Contracts\Storage;
use Foxhound\Storage\DatabaseStorage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Foxhound\Storage\FilesystemStorage;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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

        if (!Foxhound::enabled()) {
            return;
        }

        $this->app->bind(Storage::class, fn () => match ($this->app->make('config')->get('foxhound.storage.driver')) {
            'filesystem' => $this->createFilesystemStorage(),
            'database' => $this->createDatabaseStorage()
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->bootCommands();
        $this->bootPublishing();

        if (!Foxhound::enabled()) {
            return;
        }

        $this->bootAuthorization();
        $this->bootViews();
        $this->bootRoutes();
        $this->bootEventListeners();
    }

    /**
     * Boot the Foxhound authorization.
     */
    protected function bootAuthorization(): void
    {
        $this->callAfterResolving(Gate::class, function (Gate $gate, Application $app) {
            $gate->define(
                ability: 'viewFoxhound',
                callback: fn ($user = null) => in_array($app->environment(), $app->make('config')->get('foxhound.environments'))
            );
        });
    }

    /**
     * Boot the Foxhound views.
     */
    protected function bootViews(): void
    {
        $this->loadViewsFrom(FOXHOUND_PATH.'/resources/views', 'foxhound');
    }

    /**
     * Boot the Foxhound event listeners.
     */
    protected function bootEventListeners(): void
    {
        Event::listen(NotificationSending::class, Listeners\NotificationSending\InterceptNotification::class);
        Event::listen(MessageSending::class, Listeners\MessageSending\InterceptMessage::class);
    }

    /**
     * Boot the Foxhound routes.
     */
    protected function bootRoutes(): void
    {
        $config = $this->app->make('config');

        Route::withoutMiddleware($config->get('foxhound.without_csrf_verification', false) ? [VerifyCsrfToken::class] : [])
            ->middleware($config->get('foxhound.middleware'))
            ->group(function () {
                // Group and load the API routes.
                Route::prefix('foxhound/api')->group(fn () => $this->loadRoutesFrom(FOXHOUND_PATH.'/routes/api.php'));

                // Define the global Foxhound route for the SPA.
                Route::view('foxhound/{path?}', 'foxhound::index')->where('path', '.*');
            });
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
     * Boot publishing for assets and configuration when running in console.
     */
    protected function bootPublishing(): void
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

        $this->publishes(
            paths: [FOXHOUND_PATH.'/database/migrations' => database_path('migrations')],
            groups: 'foxhound-migrations'
        );
    }

    /**
     * Create a filesystems torage instance.
     */
    protected function createFilesystemStorage(): FilesystemStorage
    {
        return new FilesystemStorage(
            filesystem: $this->app->make('filesystem')->disk($this->app->make('config')->get('foxhound.storage.filesystem.disk')),
            rootStorageDirectory: $this->app->make('config')->get('foxhound.storage.filesystem.root')
        );
    }

    /**
     * Create a database storage instance.
     */
    protected function createDatabaseStorage(): DatabaseStorage
    {
        return new DatabaseStorage(
            database: $this->app->make('db'),
            connection: $this->app->make('config')->get('foxhound.storage.database.connection')
        );
    }
}
