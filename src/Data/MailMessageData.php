<?php

namespace Foxhound\Data;

use Spatie\LaravelData\Data;
use Foxhound\Data\AttachmentData;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class MailMessageData extends Data
{
    public function __construct(
        #[DataCollectionOf(AttachmentData::class)]
        public DataCollection $attachments,
        public array $from,
        public array $replyTo,
        public array $cc,
        public array $bcc,
    ) {
    }
}
