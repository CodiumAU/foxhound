<?php

namespace App\Interceptor;

use Illuminate\Support\Manager;
use App\Interceptor\Channels\Channel;

/**
 * @method \App\Interceptor\Channels\Channel driver(string $driver = null)
 */
class InterceptorManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'mail';
    }

    public function createMailDriver(): Channels\Mail
    {
        return $this->createInterceptorDriver(Channels\Mail::class);
    }

    public function createVonageDriver(): Channels\Vonage
    {
        return $this->createInterceptorDriver(Channels\Vonage::class);
    }

    protected function createInterceptorDriver(string $class): Channel
    {
        return new $class(
            filesystem: $this->container['filesystem']->disk(),
            rootStorageDirectory: 'interceptor',
        );
    }
}
