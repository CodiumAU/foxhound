<?php

namespace Foxhound\Listeners\MessageSending;

use Foxhound\Manifest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Foxhound\ChannelManager;
use InvalidArgumentException;
use Foxhound\Contracts\Storage;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class InterceptMessage
{
    /**
     * The mail channel name.
     */
    protected static $channel = 'mail';

    /**
     * Create the event listener.
     */
    public function __construct(
        protected ChannelManager $manager,
        protected ConfigRepository $config,
        protected Storage $storage
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSending $event): bool
    {
        // Do not intercept a mail sending if the "mail" channel is not configured.
        if (!in_array(static::$channel, $this->config->get('foxhound.channels', []))) {
            return true;
        }

        try {
            $channel = $this->manager->channel(static::$channel);
            $manifest = new Manifest(
                channel: $channel->key(),
                uuid: Str::orderedUuid(),
                sentAt: CarbonImmutable::now()
            );

            // Intercept the message sending.
            $channel->intercept($event, $manifest);

            // Save the manifest after the driver has run any additional logic for the interception.
            $this->storage->saveManifest($manifest);

            return false;
        } catch (InvalidArgumentException) {
            return true;
        }
    }
}
