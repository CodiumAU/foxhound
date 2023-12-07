<?php

namespace Foxhound\Storage;

use Foxhound\Manifest;
use Foxhound\Channels\Channel;
use Foxhound\Contracts\Storage;
use Foxhound\Data\Response\MessageData;
use Illuminate\Contracts\Filesystem\Filesystem;

class FilesystemStorage implements Storage
{
    /**
     * Create filesystem storage instance.
     */
    public function __construct(
        protected Filesystem $filesystem,
        protected string $rootStorageDirectory = 'foxhound'
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getManifest(Channel $channel, string $uuid): ?Manifest
    {
        $manifest = $this->filesystem->get($this->path("{$channel->key()}/{$uuid}/manifest.json"));

        if ($manifest) {
            try {
                return Manifest::fromJson($manifest);
            } catch (Exception) {
                // If the manifest file exists, but is invalid, we'll do nothing and just delete the directory instead. This can
                // happen if the manifest file contains a notification that is no longer valid or contains models that no longer
                // exist in the database.
            }
        }

        if ($this->filesystem->exists($directory = $this->path("{$channel->key()}/{$uuid}"))) {
            // If the manifest file does not exist, but the directory does, delete the directory.
            $this->filesystem->deleteDirectory($directory);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function saveManifest(Manifest $manifest): self
    {
        $this->filesystem->put(
            $this->path("{$manifest->channel}/{$manifest->uuid}/manifest.json"),
            json_encode($manifest)
        );

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessages(Channel $channel): array
    {
        $messages = [];

        foreach ($this->filesystem->directories($this->path($channel->key())) as $path) {
            if ($manifest = $this->getManifest($channel, basename($path))) {
                $messages[] = $channel->buildMessageData($manifest);
            }
        }

        usort($messages, fn ($a, $b) => $b->sentAt <=> $a->sentAt);

        return array_values($messages);
    }

    /**
     * {@inheritDoc}
     */
    public function getUnreadMessagesCount(Channel $channel): int
    {
        return array_reduce($this->getMessages($channel), fn ($count, MessageData $message) => $count + ($message->unread ? 1 : 0), 0);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMessages(Channel $channel): void
    {
        $this->filesystem->deleteDirectory($this->path($channel->key()));
    }

    /**
     * Get a relative path within the storage directory.
     */
    protected function path(string $path = null): string
    {
        return "{$this->rootStorageDirectory}/{$path}";
    }
}
