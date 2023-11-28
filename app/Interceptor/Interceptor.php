<?php

namespace App\Interceptor;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Notifications\Events\NotificationSending;

abstract class Interceptor
{
    public function __construct(
        protected Filesystem $filesystem,
        protected string $rootStorageDirectory = 'interceptor'
    ) {
    }

    abstract public function intercept(NotificationSending $event, Manifest $manifest): void;
}
