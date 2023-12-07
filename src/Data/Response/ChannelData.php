<?php

namespace Foxhound\Data\Response;

use Spatie\LaravelData\Data;
use Foxhound\Support\ChannelType;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Attributes\WithCast;

class ChannelData extends Data
{
    public function __construct(
        public string $key,
        public string $name,
        #[WithCast(EnumCast::class)]
        public ChannelType $type,
        public int $unreadMessagesCount
    ) {
    }
}
