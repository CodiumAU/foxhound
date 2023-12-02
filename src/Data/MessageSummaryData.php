<?php

namespace Foxhound\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class MessageSummaryData extends Data
{
    public function __construct(
        public string $uuid,
        public bool $unread,
        public bool $hasAttachments,
        public ?string $subject,
        #[DataCollectionOf(MessageRecipientData::class)]
        public DataCollection $recipients,
        public CarbonImmutable $sentAt,
    ) {
    }
}
