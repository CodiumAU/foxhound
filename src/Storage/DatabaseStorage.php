<?php

namespace Foxhound\Storage;

use Exception;
use Foxhound\Manifest;
use Carbon\CarbonImmutable;
use Foxhound\Channels\Channel;
use Foxhound\Contracts\Storage;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

class DatabaseStorage implements Storage
{
    /**
     * Create database storage instance.
     */
    public function __construct(
        protected DatabaseManager $database,
        protected ?string $connection
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getManifest(Channel $channel, string $uuid): ?Manifest
    {
        $data = $this->connection()
            ->table('foxhound_manifests')
            ->where('channel', $channel->key())
            ->where('uuid', $uuid)
            ->first();

        if ($data) {
            try {
                return $this->parseManifestFromDatabase($data);
            } catch (Exception) {
                // If the manifest file exists, but is invalid, we'll delete it from the database as it likely has been corrupted due
                // to a change in underlying data.
                $this->connection()
                    ->table('foxhound_manifests')
                    ->where('channel', $channel->key())
                    ->where('uuid', $uuid)
                    ->delete();
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function saveManifest(Manifest $manifest): self
    {
        $this->connection()
            ->table('foxhound_manifests')
            ->upsert(
                values: [
                    'uuid' => $manifest->uuid,
                    'channel' => $manifest->channel,
                    'unread' => $manifest->unread,
                    'sent_at' => $manifest->sentAt->toDateTimeString(),
                    'html' => base64_encode($manifest->html),
                    'attachments' => serialize($manifest->attachments),
                    'data' => json_encode($manifest->data),
                ],
                uniqueBy: 'uuid',
                update: ['channel', 'unread', 'sent_at', 'html', 'attachments', 'data']
            );

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessages(Channel $channel): array
    {
        return $this->connection()
            ->table('foxhound_manifests')
            ->where('channel', $channel->key())
            ->latest('sent_at')
            ->get()
            ->map(fn ($data) => $channel->buildMessageData($this->parseManifestFromDatabase($data)))
            ->all();
    }

    /**
     * {@inheritDoc}
     */
    public function getUnreadMessagesCount(Channel $channel): int
    {
        return $this->connection()
            ->table('foxhound_manifests')
            ->where('channel', $channel->key())
            ->where('unread', true)
            ->count();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMessages(Channel $channel): void
    {
        $this->connection()
            ->table('foxhound_manifests')
            ->where('channel', $channel->key())
            ->delete();
    }

    /**
     * Get a database connection.
     */
    protected function connection(): Connection
    {
        return $this->database->connection($this->connection);
    }

    /**
     * Parse a manifest from database data.
     */
    protected function parseManifestFromDatabase(object $data): Manifest
    {
        return new Manifest(
            uuid: $data->uuid,
            channel: $data->channel,
            unread: boolval($data->unread),
            sentAt: CarbonImmutable::parse($data->sent_at),
            html: base64_decode($data->html),
            attachments: unserialize($data->attachments),
            data: json_decode($data->data, true),
        );
    }
}
