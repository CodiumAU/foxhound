<?php

namespace Foxhound\Listeners\NotificationSending;

use Foxhound\Manifest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Foxhound\ChannelManager;
use InvalidArgumentException;
use Foxhound\Contracts\Storage;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationSent;
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
        protected Dispatcher $events,
        protected Storage $storage
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
                channel: $channel->key(),
                uuid: Str::orderedUuid(),
                sentAt: CarbonImmutable::now()
            );

            // Intercept the notification.
            $channel->intercept($event, $manifest);

            // Save the manifest after the driver has run any additional logic for the interception.
            $this->storage->saveManifest($manifest);

            // Continue to dispatch the notification sent event after we intercepted the notification. We can then
            // return false to stop the notification from actually sending as Foxhound has intercepted it.
            $this->events->dispatch(
                new NotificationSent($event->notifiable, $event->notification, $event->channel)
            );

            return false;
        } catch (InvalidArgumentException) {
            return true;
        }
    }
}
