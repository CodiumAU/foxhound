<?php

namespace Foxhound;

use Illuminate\Support\Str;
use Foxhound\Channels\Channel;
use Foxhound\Contracts\Storage;
use Illuminate\Support\Manager;

/**
 * @method \Foxhound\Channels\Channel driver(string $driver = null)
 */
class ChannelManager extends Manager
{
    /**
     * Get the default driver.
     */
    public function getDefaultDriver(): string
    {
        return 'mail';
    }

    /**
     * Alias for getting a driver.
     */
    public function channel(?string $channel): Channel
    {
        return $this->driver($channel);
    }

    /**
     * Create the mail driver.
     */
    public function createMailDriver(): Channels\Mail
    {
        $channel = $this->createChannelDriver(Channels\Mail::class);

        foreach (['from', 'to', 'reply_to'] as $key) {
            $address = $this->container['config']["mail.{$key}"] ?? null;

            if (isset($address['address'])) {
                $channel->{'always'.Str::studly($key)}($address['address'], $address['name'] ?? null);
            }
        }

        return $channel;
    }

    /**
     * Create the Vonage driver.
     */
    public function createVonageDriver(): Channels\Vonage
    {
        return $this->createChannelDriver(Channels\Vonage::class);
    }

    /**
     * Create a new channel driver.
     */
    protected function createChannelDriver(string $class): Channel
    {
        return new $class(
            storage: $this->container[Storage::class],
        );
    }
}
