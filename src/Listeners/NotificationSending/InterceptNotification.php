<?php

namespace Foxhound\Listeners\NotificationSending;

use Foxhound\Manifest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Foxhound\ChannelManager;
use InvalidArgumentException;
use Foxhound\Contracts\Storage;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Notifications\ChannelManager as NotificationChannelManager;

class InterceptNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected ChannelManager $manager,
        protected NotificationChannelManager $notifications,
        protected ConfigRepository $config,
        protected Dispatcher $events,
        protected Storage $storage
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSending $event): void
    {
        // Do not intercept a notification for a channel that has not been configured.
        if (!in_array($event->channel, $this->config->get('foxhound.channels', []))) {
            return;
        }

        try {
            $channel = $this->manager->channel($event->channel);
            $manifest = new Manifest(
                channel: $channel->key(),
                uuid: Str::orderedUuid(),
                sentAt: CarbonImmutable::now()
            );

            // Intercept the notification.
            $channel->intercept($event, $manifest);

            // Save the manifest after the driver has run any additional logic for the interception.
            $this->storage->saveManifest($manifest);

            // Extend the channel with a class that can be used to "fake" the sending of the notification. This allows
            // us to not break out of the usual notification flow and have other event listeners run as expected.
            $this->notifications->extend($event->channel, fn () => new class {
                public function send()
                {
                    return null;
                }
            });

            $this->notifications->forgetDrivers();
        } catch (InvalidArgumentException) {
            // Silently ignore invalid argument exceptions.
        }
    }
}
