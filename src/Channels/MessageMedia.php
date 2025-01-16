<?php

namespace Foxhound\Channels;

use Foxhound\Data;
use RuntimeException;
use Foxhound\Manifest;
use Foxhound\Support\ChannelType;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Notifications\Events\NotificationSending;

class MessageMedia extends Vonage
{
    /**
     * {@inheritDoc}
     */
    public function intercept(NotificationSending | MessageSending $event, Manifest $manifest): void
    {
        // Message Media can only be intercepted when sending a notification.
        if (!($event instanceof NotificationSending)) {
            return;
        }

        throw_unless(method_exists($event->notification, 'toMessageMedia'), new RuntimeException('Notification does not have a "toMessageMedia" method.'));

        $message = $event->notification->toMessageMedia($event->notifiable);

        $manifest->data('message', $message->message);
        $manifest->data('to', $event->notifiable->routeNotificationFor('messagemedia', $event->notification));
        $manifest->data('from', $message->from);
    }

    /**
     * {@inheritDoc}
     */
    public function data(): Data\Response\ChannelData
    {
        return Data\Response\ChannelData::from([
            'key' => $this->key(),
            'name' => 'Message Media',
            'type' => ChannelType::Sms,
            'unreadMessagesCount' => $this->storage->getUnreadMessagesCount($this),
        ]);
    }
}
