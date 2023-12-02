<?php

namespace Foxhound\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Foxhound\Data\MessageSummaryData
 **/
class MessageListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'unread' => $this->unread,
            'has_attachments' => $this->hasAttachments,
            'subject' => $this->subject,
            'recipients' => $this->recipients->toArray(),
            'sent_at' => $this->sentAt->toIso8601String(),
            'data' => $this->data,
        ];
    }
}
