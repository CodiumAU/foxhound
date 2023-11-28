<?php

namespace App\Interceptor;

use Illuminate\Support\Manager;

/**
 * @method \App\Interceptor\Interceptor driver(string $driver = null)
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

    protected function createInterceptorDriver(string $class): Interceptor
    {
        return new $class(
            filesystem: $this->container['filesystem']->disk(),
            rootStorageDirectory: 'interceptor',
        );
    }
}
