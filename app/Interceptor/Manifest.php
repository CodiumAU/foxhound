<?php

namespace App\Interceptor;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Events\NotificationSending;

class Manifest implements JsonSerializable, Arrayable
{
    public function __construct(
        public string $uuid,
        public string $channel,
        public NotificationSending $event,
        public array $data = [],
    ) {
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'channel' => $this->channel,
            'event' => serialize($this->event),
            'data' => $this->data,
        ];
    }
}
