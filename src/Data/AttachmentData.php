<?php

namespace Foxhound\Data;

use Spatie\LaravelData\Data;

class AttachmentData extends Data
{
    public function __construct(
        public string $name,
        public string $size,
        public string $url
    ) {
    }
}
