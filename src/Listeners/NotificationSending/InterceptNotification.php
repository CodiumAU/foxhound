<?php

namespace Foxhound\Listeners\NotificationSending;

use Foxhound\Manifest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Foxhound\ChannelManager;
use InvalidArgumentException;
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
        // Do not intercept a notification for a channel that has not been configured.
        if (!in_array($event->channel, $this->config->get('foxhound.channels', []))) {
            return true;
        }

        try {
            $channel = $this->manager->channel($event->channel);
            $manifest = new Manifest(
                channel: $channel,
                uuid: Str::orderedUuid(),
                sentAt: CarbonImmutable::now(),
                event: $event,
            );

            // Intercept the notification.
            $channel->intercept($event, $manifest);

            // Save the manifest after the driver has run any additional logic for the interception.
            $manifest->save();

            return false;
        } catch (InvalidArgumentException) {
            return true;
        }
    }
}
