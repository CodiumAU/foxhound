<?php

namespace Foxhound\Data\Response;

use Spatie\LaravelData\Data;

class MessageRecipientData extends Data
{
    public function __construct(
        public ?string $name,
        public ?string $address,
    ) {
    }
}
