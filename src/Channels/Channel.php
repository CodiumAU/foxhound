<?php

namespace Foxhound\Channels;

use RuntimeException;
use Foxhound\Manifest;
use Illuminate\Http\Response;
use Foxhound\Data\ChannelData;
use Illuminate\Support\Number;
use Foxhound\Data\AttachmentData;
use Foxhound\Data\MessageSummaryData;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Notifications\Events\NotificationSending;

abstract class Channel
{
    public function __construct(
        protected Filesystem $filesystem,
        protected string $rootStorageDirectory = 'foxhound'
    ) {
    }

    public function manifest(string $uuid): ?Manifest
    {
        $path = $this->relativePath("{$uuid}/manifest.json");

        if ($this->filesystem->exists($path)) {
            return Manifest::parse($this->filesystem->get($path));
        }

        $this->filesystem->deleteDirectory($this->relativePath($uuid));

        return null;
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

    public function path(string $path = null): string
    {
        return $this->filesystem->path(
            $this->relativePath($path)
        );
    }

    public function relativePath(string $path = null): string
    {
        return "{$this->rootStorageDirectory}/{$this->data()->key}/{$path}";
    }

    public function attachments(Manifest $manifest): array
    {
        return collect($manifest->data['attachments'])
            ->map(fn (array $data, $uuid) => AttachmentData::from([
                'name' => $data['name'],
                'size' => Number::fileSize(
                    bytes: $this->filesystem->size($this->relativePath("{$manifest->uuid}/attachments/{$uuid}")),
                    precision: 2
                ),
                'url' => ''
            ]))
            ->values()
            ->toArray();
    }

    abstract public function intercept(NotificationSending $event, Manifest $manifest): void;

    abstract public function newMessageSummaryData(Manifest $manifest): MessageSummaryData;

    abstract public function response(Manifest $manifest): Response;

    abstract public function data(): ChannelData;
}
