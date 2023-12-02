<?php

namespace Foxhound\Data;

use Foxhound\ChannelType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Attributes\WithCast;

class ChannelData extends Data
{
    public function __construct(
        public string $key,
        public string $name,
        #[WithCast(EnumCast::class)]
        public ChannelType $type,
    ) {
    }
}
