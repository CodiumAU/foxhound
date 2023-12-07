<?php

namespace Foxhound;

use JsonSerializable;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Events\NotificationSending;

class Manifest implements JsonSerializable, Arrayable
{
    /**
     * Create a new manfiest instance.
     */
    public function __construct(
        public string $channel,
        public string $uuid,
        public CarbonImmutable $sentAt,
        public NotificationSending $event,
        public ?string $html = null,
        public bool $unread = true,
        public array $attachments = [],
        public array $data = [],
    ) {
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
            'channel' => $this->channel,
            'uuid' => $this->uuid,
            'event' => serialize($this->event),
            'sentAt' => $this->sentAt->toIso8601String(),
            'unread' => $this->unread,
            'html' => base64_encode($this->html),
            'attachments' => serialize($this->attachments),
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
    public static function fromJson(string $manifest): self
    {
        $manifest = json_decode($manifest, true);

        return new self(
            channel: $manifest['channel'],
            uuid: $manifest['uuid'],
            sentAt: CarbonImmutable::parse($manifest['sentAt']),
            event: unserialize($manifest['event']),
            html: base64_decode($manifest['html']),
            attachments: unserialize($manifest['attachments']),
            unread: $manifest['unread'],
            data: $manifest['data'],
        );
    }
}
