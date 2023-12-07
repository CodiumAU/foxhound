<?php

namespace Foxhound\Data\Response;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class MessageData extends Data
{
    public function __construct(
        public string $uuid,
        public bool $unread,
        public string $subject,
        #[DataCollectionOf(MessageRecipientData::class)]
        public DataCollection $recipients,
        #[DataCollectionOf(AttachmentData::class)]
        public DataCollection $attachments,
        public CarbonImmutable $sentAt,
        public mixed $data,
    ) {
    }
}
