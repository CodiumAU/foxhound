<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use App\Foxhound\ChannelType;
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
