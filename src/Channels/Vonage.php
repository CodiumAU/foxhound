<?php

namespace Foxhound\Channels;

use RuntimeException;
use Foxhound\Manifest;
use Foxhound\ChannelType;
use Illuminate\Http\Response;
use Foxhound\Data\ChannelData;
use Foxhound\Data\MessageSummaryData;
use Foxhound\Data\MessageRecipientData;
use Illuminate\Notifications\Events\NotificationSending;

class Vonage extends Channel
{
    public function intercept(NotificationSending $event, Manifest $manifest): void
    {
        throw_unless(method_exists($event->notification, 'toVonage'), new RuntimeException('Notification does not have a "toVonage" method.'));

        /** @var \Illuminate\Notifications\Messages\VonageMessage */
        $message = $event->notification->toVonage($event->notifiable);

        $manifest->data('message', $message->content);
        $manifest->data('to', $event->notifiable->routeNotificationFor('vonage', $event->notification));
        $manifest->data('from', $message->from);
    }

    public function relativePath(string $path = null): string
    {
        return "{$this->rootStorageDirectory}/vonage/{$path}";
    }

    public function newMessageSummaryData(Manifest $manifest): MessageSummaryData
    {
        return MessageSummaryData::from([
            'uuid' => $manifest->uuid,
            'unread' => $manifest->unread,
            'hasAttachments' => false,
            'subject' => $manifest->data['message'],
            'recipients' => [
                MessageRecipientData::from(['address' => $manifest->data['to']])
            ],
            'from' => $manifest->data['from'],
            'sentAt' => $manifest->sentAt,
        ]);
    }

    public function response(Manifest $manifest): Response
    {
        return response(
            view('sms', ['message' => $manifest->data['message']])
        );
    }
    public function data(): ChannelData
    {
        return ChannelData::from([
            'key' => 'vonage',
            'name' => 'Vonage',
            'type' => ChannelType::Sms
        ]);
    }
}
