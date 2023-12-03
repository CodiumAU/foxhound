<?php

namespace Foxhound\Data;

use Foxhound\AttachmentType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Attributes\WithCast;

class AttachmentData extends Data
{
    public function __construct(
        public string $name,
        public string $size,
        public string $url,
        #[WithCast(EnumCast::class)]
        public AttachmentType $type
    ) {
    }
}
