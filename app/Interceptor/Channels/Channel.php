<?php

namespace App\Interceptor\Channels;

use App\Interceptor\Manifest;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Notifications\Events\NotificationSending;

abstract class Channel
{
    public function __construct(
        protected Filesystem $filesystem,
        protected string $rootStorageDirectory = 'interceptor'
    ) {
    }

    abstract public function intercept(NotificationSending $event, Manifest $manifest): void;

    abstract public function path(string $path = null): string;

    abstract public function relativePath(string $path = null): string;
}
