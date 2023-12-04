<?php

namespace Foxhound\Channels;

use RuntimeException;
use Foxhound\Manifest;
use Foxhound\ChannelType;
use Foxhound\Data\ChannelData;
use Foxhound\Data\MessageData;
use Foxhound\Data\SmsMessageData;
use Foxhound\Data\MessageRecipientData;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Notifications\Events\NotificationSending;

class Vonage extends Channel
{
    /**
     * {@inheritDoc}
     */
    public function intercept(NotificationSending $event, Manifest $manifest): void
    {
        throw_unless(method_exists($event->notification, 'toVonage'), new RuntimeException('Notification does not have a "toVonage" method.'));

        /** @var \Illuminate\Notifications\Messages\VonageMessage */
        $message = $event->notification->toVonage($event->notifiable);

        $manifest->data('message', $message->content);
        $manifest->data('to', $event->notifiable->routeNotificationFor('vonage', $event->notification));
        $manifest->data('from', $message->from);
    }

    /**
     * {@inheritDoc}
     */
    public function buildMessageData(Manifest $manifest): MessageData
    {
        return MessageData::from([
            'uuid' => $manifest->uuid,
            'unread' => $manifest->unread,
            'hasAttachments' => false,
            'subject' => $manifest->data['message'],
            'recipients' => [
                MessageRecipientData::from(['address' => $manifest->data['to']])
            ],
            'from' => $manifest->data['from'],
            'sentAt' => $manifest->sentAt,
            'data' => new SmsMessageData,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function response(Manifest $manifest): HttpResponse
    {
        return Response::view('foxhound::sms', ['message' => $manifest->data['message']]);
    }

    /**
     * {@inheritDoc}
     */
    public function data(): ChannelData
    {
        return ChannelData::from([
            'key' => $this->key,
            'name' => 'Vonage',
            'type' => ChannelType::Sms,
            'unreadMessagesCount' => $this->unreadMessagesCount(),
        ]);
    }
}
