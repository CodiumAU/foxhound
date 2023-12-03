<?php

namespace Foxhound;

use Illuminate\Support\Str;
use Foxhound\Channels\Channel;
use Illuminate\Support\Manager;

/**
 * @method \Foxhound\Channels\Channel driver(string $driver = null)
 */
class ChannelManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'mail';
    }

    public function channel(?string $channel): Channel
    {
        return $this->driver($channel);
    }

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

    public function createVonageDriver(): Channels\Vonage
    {
        return $this->createChannelDriver(Channels\Vonage::class);
    }

    protected function createChannelDriver(string $class): Channel
    {
        return new $class(
            filesystem: $this->container['filesystem']->disk(),
            rootStorageDirectory: 'foxhound',
        );
    }
}
