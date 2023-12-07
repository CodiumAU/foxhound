<?php

namespace Foxhound\Data\Response;

use Spatie\LaravelData\Data;

class MailMessageData extends Data
{
    public function __construct(
        public array $from,
        public array $replyTo,
        public array $cc,
        public array $bcc,
    ) {
    }
}
