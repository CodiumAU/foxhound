<?php

namespace Foxhound\Channels;

use Foxhound\Data;
use RuntimeException;
use Foxhound\Manifest;
use Foxhound\Support\ChannelType;
use Illuminate\Support\Facades\Response;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Notifications\Events\NotificationSending;

class Vonage extends Channel
{
    /**
     * {@inheritDoc}
     */
    public function intercept(NotificationSending | MessageSending $event, Manifest $manifest): void
    {
        // Vonage can only be intercepted when sending a notification.
        if (!($event instanceof NotificationSending)) {
            return;
        }

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
    public function buildMessageData(Manifest $manifest): Data\Response\MessageData
    {
        return Data\Response\MessageData::from([
            'uuid' => $manifest->uuid,
            'unread' => $manifest->unread,
            'subject' => $manifest->data['message'],
            'recipients' => [
                Data\Response\MessageRecipientData::from(['address' => $manifest->data['to']])
            ],
            'from' => $manifest->data['from'],
            'sentAt' => $manifest->sentAt,
            'data' => new Data\Response\SmsMessageData,
            'attachments' => [],
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
    public function data(): Data\Response\ChannelData
    {
        return Data\Response\ChannelData::from([
            'key' => $this->key(),
            'name' => 'Vonage',
            'type' => ChannelType::Sms,
            'unreadMessagesCount' => $this->storage->getUnreadMessagesCount($this),
        ]);
    }
}
