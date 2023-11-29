<?php

namespace App\Listeners\NotificationSending;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use App\Interceptor\Manifest;
use InvalidArgumentException;
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
    public function handle(NotificationSending $event): bool
    {
        try {
            $driver = $this->manager->driver($event->channel);

            // Create manifest.
            $manifest = $driver->make(new Manifest(
                uuid: Str::orderedUuid(),
                channel: $event->channel,
                sentAt: CarbonImmutable::now(),
                event: $event,
            ));

            $driver->intercept($event, $manifest);

            $driver->save($manifest);

            return false;
        } catch (InvalidArgumentException) {
            return true;
        }
    }
}
