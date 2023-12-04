<?php

namespace Foxhound\Channels;

use Foxhound\Manifest;
use Illuminate\Http\Response;
use Foxhound\Data\ChannelData;
use Foxhound\Data\MessageData;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Notifications\Events\NotificationSending;

abstract class Channel
{
    /**
     * Create a new channel instance.
     */
    public function __construct(
        protected Filesystem $filesystem,
        protected string $key,
        protected string $rootStorageDirectory = 'foxhound'
    ) {
    }

    /**
     * Create a new directory.
     */
    public function directory(string $name): self
    {
        $this->filesystem->makeDirectory(
            $this->path($name)
        );

        return $this;
    }

    /**
     * Store a new file.
     */
    public function store(string $name, string $contents): self
    {
        $this->filesystem->put(
            $this->path($name),
            $contents,
        );

        return $this;
    }

    /**
     * Get the contents of a file.
     */
    public function file(string $name): string
    {
        return $this->filesystem->get($this->path($name));
    }

    /**
     * Resolve a manifest by its UUID.
     */
    public function buildManifest(string $uuid): ?Manifest
    {
        $manifest = $this->filesystem->get($this->path("{$uuid}/manifest.json"));

        if ($manifest) {
            return Manifest::parse($this, $manifest);
        } else if ($this->filesystem->exists($directory = $this->path($uuid))) {
            // If the manifest file does not exist, but the directory does, delete the directory.
            $this->filesystem->deleteDirectory($directory);
        }

        return null;
    }

    /**
     * Get a relative path for the channel.
     */
    public function path(string $path = null): string
    {
        return "{$this->rootStorageDirectory}/{$this->key}/{$path}";
    }

    /**
     * Get all messages for a channel.
     */
    public function messages(): array
    {
        $messages = [];

        foreach ($this->filesystem->directories($this->path()) as $path) {
            if ($manifest = $this->buildManifest(basename($path))) {
                $messages[$manifest->uuid] = $this->buildMessageData($manifest);
            }
        }

        krsort($messages, SORT_STRING);

        return array_values($messages);
    }

    /**
     * Delete the messages for a channel.
     */
    public function deleteMessages(): void
    {
        $this->filesystem->deleteDirectory($this->path());
    }

    /**
     * Get a count of unread messages for the channel.
     */
    protected function unreadMessagesCount(): int
    {
        return array_reduce($this->messages(), fn ($count, MessageData $message) => $count + ($message->unread ? 1 : 0), 0);
    }

    /**
     * Intercept a notification event and build the manifest.
     */
    abstract public function intercept(NotificationSending $event, Manifest $manifest): void;

    /**
     * Return the response of the stored notification from its manifest.
     */
    abstract public function response(Manifest $manifest): Response;

    /**
     * Get the channel data.
     */
    abstract public function data(): ChannelData;

    /**
     * Build the message data from a manifest.
     */
    abstract public function buildMessageData(Manifest $manifest): MessageData;
}
