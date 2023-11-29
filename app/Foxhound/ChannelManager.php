<?php

namespace App\Foxhound;

use Illuminate\Support\Manager;
use App\Foxhound\Channels\Channel;

/**
 * @method \App\Foxhound\Channels\Channel driver(string $driver = null)
 */
class ChannelManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'mail';
    }

    public function createMailDriver(): Channels\Mail
    {
        return $this->createChannelDriver(Channels\Mail::class);
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
