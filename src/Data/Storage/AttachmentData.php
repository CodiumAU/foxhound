<?php

namespace Foxhound\Data\Storage;

use Spatie\LaravelData\Data;

class AttachmentData extends Data
{
    public function __construct(
        public string $name,
        public string $mime,
        public int $bytes,
        public string $data,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'mime' => $this->mime,
            'bytes' => $this->bytes,
            'data' => base64_encode($this->data),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->mime = $data['mime'];
        $this->bytes = $data['bytes'];
        $this->data = base64_decode($data['data']);
    }
}
