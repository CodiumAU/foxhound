<?php

namespace Foxhound;

use JsonSerializable;
use Carbon\CarbonImmutable;
use Foxhound\Channels\Channel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Events\NotificationSending;

class Manifest implements JsonSerializable, Arrayable
{
    /**
     * Create a new manfiest instance.
     */
    public function __construct(
        public Channel $channel,
        public string $uuid,
        public CarbonImmutable $sentAt,
        public NotificationSending $event,
        public bool $unread = true,
        public array $data = [],
    ) {
        $channel->directory($uuid);
    }

    /**
     * Save the manifest.
     */
    public function save(): self
    {
        $this->channel->store(
            "{$this->uuid}/manifest.json",
            json_encode($this),
        );

        return $this;
    }

    /**
     * Serialize the manifest to JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Mark the manifest as read.
     */
    public function markAsRead(): self
    {
        $this->unread = false;

        return $this;
    }

    /**
     * Convert the manifest to an array.
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'event' => serialize($this->event),
            'sentAt' => $this->sentAt->toIso8601String(),
            'unread' => $this->unread,
            'data' => $this->data,
        ];
    }

    /**
     * Add data to the manifest.
     */
    public function data(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Parse a manifest file into a manifest instance.
     */
    public static function parse(Channel $channel, string $manifest): self
    {
        $manifest = json_decode($manifest, true);

        return new self(
            channel: $channel,
            uuid: $manifest['uuid'],
            sentAt: CarbonImmutable::parse($manifest['sentAt']),
            event: unserialize($manifest['event']),
            unread: $manifest['unread'],
            data: $manifest['data'],
        );
    }
}
