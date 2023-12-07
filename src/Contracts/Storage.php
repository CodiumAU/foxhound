<?php

namespace Foxhound\Contracts;

use Foxhound\Manifest;
use Foxhound\Channels\Channel;

interface Storage
{
    /**
     * Get a manifest.
     */
    public function getManifest(Channel $channel, string $uuid): ?Manifest;

    /**
     * Save a manifest.
     */
    public function saveManifest(Manifest $manifest): self;

    /**
     * Get all messages for a given channel.
     */
    public function getMessages(Channel $channel): array;

    /**
     * Delete all messages for a given channel.
     */
    public function deleteMessages(Channel $channel): void;

    /**
     * Get the unread messages count for a channel.
     */
    public function getUnreadMessagesCount(Channel $channel): int;
}
