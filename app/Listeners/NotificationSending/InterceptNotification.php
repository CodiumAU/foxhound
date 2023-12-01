<?php

namespace App\Listeners\NotificationSending;

use App\Foxhound\Manifest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use App\Foxhound\ChannelManager;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class InterceptNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected ChannelManager $manager,
        protected ConfigRepository $config,
        protected Filesystem $filesystem
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSending $event): bool
    {
        if (!in_array($event->channel, $this->config->get('foxhound.channels', []))) {
            return true;
        }

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
