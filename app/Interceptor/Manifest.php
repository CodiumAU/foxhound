<?php

namespace App\Interceptor;

use JsonSerializable;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Events\NotificationSending;

class Manifest implements JsonSerializable, Arrayable
{
    public function __construct(
        public string $uuid,
        public string $channel,
        public CarbonImmutable $sentAt,
        public NotificationSending $event,
        public bool $unread = true,
        public array $data = [],
    ) {
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function markAsRead(): self
    {
        $this->unread = false;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'channel' => $this->channel,
            'event' => serialize($this->event),
            'sentAt' => $this->sentAt->toIso8601String(),
            'unread' => $this->unread,
            'data' => $this->data,
        ];
    }

    public function data(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    public static function parse(string $manifest): self
    {
        $manifest = json_decode($manifest, true);

        return new self(
            uuid: $manifest['uuid'],
            channel: $manifest['channel'],
            sentAt: CarbonImmutable::parse($manifest['sentAt']),
            event: unserialize($manifest['event']),
            unread: $manifest['unread'],
            data: $manifest['data'],
        );
    }
}
