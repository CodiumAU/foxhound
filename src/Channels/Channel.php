<?php

namespace Foxhound\Channels;

use Foxhound\Data;
use Foxhound\Manifest;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Foxhound\Contracts\Storage;
use Illuminate\Notifications\Events\NotificationSending;

abstract class Channel
{
    /**
     * Create a new channel instance.
     */
    public function __construct(
        protected Storage $storage
    ) {
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
    abstract public function data(): Data\Response\ChannelData;

    /**
     * Build the message data from a manifest.
     */
    abstract public function buildMessageData(Manifest $manifest): Data\Response\MessageData;

    /**
     * Get the unique key for the channel.
     */
    public function key(): string
    {
        return Str::snake(class_basename($this));
    }
}
