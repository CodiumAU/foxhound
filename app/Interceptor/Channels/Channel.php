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

    public function manifest(string $uuid): Manifest
    {
        return Manifest::parse($this->filesystem->get("{$this->relativePath($uuid)}/manifest.json"));
    }

    public function make(Manifest $manifest): Manifest
    {
        $this->filesystem->makeDirectory(
            $this->relativePath($manifest->uuid)
        );

        return $manifest;
    }

    public function save(Manifest $manifest): void
    {
        $this->filesystem->put(
            "{$this->relativePath($manifest->uuid)}/manifest.json",
            json_encode($manifest),
        );
    }

    abstract public function intercept(NotificationSending $event, Manifest $manifest): void;

    abstract public function path(string $path = null): string;

    abstract public function relativePath(string $path = null): string;
}
