<?php

namespace Foxhound\Data\Response;

use Spatie\LaravelData\Data;
use Foxhound\Support\AttachmentType;
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
