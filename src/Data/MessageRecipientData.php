<?php

namespace Foxhound\Data;

use Spatie\LaravelData\Data;

class MessageRecipientData extends Data
{
    public function __construct(
        public ?string $name,
        public ?string $address,
    ) {
    }
}
