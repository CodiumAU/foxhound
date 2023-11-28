<?php

namespace App\Listeners\NotificationSending;

use Illuminate\Support\Str;
use App\Interceptor\Manifest;
use App\Interceptor\InterceptorManager;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Notifications\Events\NotificationSending;

class InterceptNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected InterceptorManager $manager,
        protected Filesystem $filesystem
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSending $event): void
    {
        $driver = $this->manager->driver($event->channel);

        // Create manifest.
        $manifest = new Manifest(
            uuid: Str::orderedUuid(),
            channel: $event->channel,
            event: $event,
        );

        // Create root directory.
        $rootNotificationDirectory = "interceptor/{$manifest->uuid}";
        $this->filesystem->makeDirectory($rootNotificationDirectory);

        $driver->intercept($event, $manifest);

        // Store manifest file.
        $this->filesystem->put(
            "{$rootNotificationDirectory}/manifest.json",
            json_encode($manifest),
        );
    }
}
