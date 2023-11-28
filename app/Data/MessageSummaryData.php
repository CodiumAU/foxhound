<?php

namespace App\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use App\Interceptor\Manifest;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class MessageSummaryData extends Data
{
    public function __construct(
        public string $uuid,
        public bool $unread,
        public string $subject,
        #[DataCollectionOf(MessageRecipientData::class)]
        public DataCollection $recipients,
        public CarbonImmutable $sentAt,
    ) {
    }

    public static function fromMail(Manifest $manifest): self
    {
        return static::from([
            'uuid' => $manifest->uuid,
            'unread' => $manifest->unread,
            'subject' => $manifest->data['subject'],
            'recipients' => array_map(
                callback: fn (array $data) => MessageRecipientData::from($data),
                array: $manifest->data['to']
            ),
            'sentAt' => $manifest->sentAt,
        ]);
    }
}
