<?php

namespace Foxhound\Channels;

use Foxhound\Number;
use RuntimeException;
use Foxhound\Manifest;
use Foxhound\ChannelType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Foxhound\AttachmentType;
use Illuminate\Mail\Mailable;
use Foxhound\Data\ChannelData;
use Foxhound\Data\MessageData;
use Foxhound\Data\AttachmentData;
use Foxhound\Data\MailMessageData;
use Illuminate\Support\Facades\URL;
use Foxhound\Data\MessageRecipientData;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Events\NotificationSending;

class Mail extends Channel
{
    /**
     * Address that is always used as the "from" address.
     */
    protected array $alwaysFrom;

    /**
     * Addresses that are always used as the "to" addresses.
     */
    protected array $alwaysTo;

    /**
     * Addresses that are always used as the "reply to" addresses.
     */
    protected array $alwaysReplyTo;

    /**
     * {@inheritDoc}
     */
    public function intercept(NotificationSending $event, Manifest $manifest): void
    {
        throw_unless(method_exists($event->notification, 'toMail'), new RuntimeException('Notification does not have a "toMail" method.'));

        /** @var \Illuminate\Notifications\Messages\MailMessage|\Illuminate\Mail\Mailable */
        $mail = $event->notification->toMail($event->notifiable);

        // Generate and store the rendered mail.
        $this->store("{$manifest->uuid}/index.html", $mail->render());

        $manifest->data('subject', $this->subject($event, $mail));
        $manifest->data('from', $this->from($event, $mail));
        $manifest->data('replyTo', $this->replyTo($event, $mail));
        $manifest->data('to', $this->to($event, $mail));
        $manifest->data('cc', $this->normalizeAddresses($mail->cc));
        $manifest->data('bcc', $this->normalizeAddresses($mail->bcc));

        // Store mail attachments.
        $this->directory("{$manifest->uuid}/attachments");
        $attachments = [];

        /** @var array $attachment */
        foreach ($mail->attachments as $attachment) {
            $uuid = Str::uuid();

            $fileName = $attachment['options']['as'] ?? basename($attachment['file']);

            $this->store(
                "{$manifest->uuid}/attachments/{$uuid}",
                file_get_contents($attachment['file'])
            );

            $attachments[(string) $uuid] = [
                'name' => $fileName,
                'mime' => $attachment['options']['mime'] ?? null,
            ];
        }

        /** @var array $attachment */
        foreach ($mail->rawAttachments as $attachment) {
            if (!$attachment['data']) {
                continue;
            }

            $uuid = Str::uuid();

            $this->store(
                "{$manifest->uuid}/attachments/{$uuid}",
                $attachment['data']
            );

            $attachments[(string) $uuid] = [
                'name' => $attachment['name'],
                'mime' => $attachment['options']['mime'] ?? null,
            ];
        }

        $manifest->data('attachments', $attachments);
    }

    /**
     * {@inheritDoc}
     */
    public function buildMessageData(Manifest $manifest): MessageData
    {
        return MessageData::from([
            'uuid' => $manifest->uuid,
            'unread' => $manifest->unread,
            'hasAttachments' => !empty($manifest->data['attachments']),
            'subject' => $manifest->data['subject'],
            'recipients' => array_map(
                callback: fn (array $data) => MessageRecipientData::from($data),
                array: $manifest->data['to']
            ),
            'sentAt' => $manifest->sentAt,
            'data' => MailMessageData::from([
                'cc' => $manifest->data['cc'],
                'bcc' => $manifest->data['bcc'],
                'replyTo' => $manifest->data['replyTo'],
                'from' => $manifest->data['from'],
                'attachments' => $this->buildAttachmentsData($manifest),
            ])
        ]);
    }

    /**
     * Get the subject for the mail message.
     */
    protected function subject(NotificationSending $event, Mailable | MailMessage $mail): string
    {
        if (isset($mail->subject)) {
            return $mail->subject;
        }

        return Str::headline(class_basename($event->notification));
    }

    /**
     * Get the "to" addresses for the mail message.
     */
    protected function to(NotificationSending $event, Mailable | MailMessage $mail): array
    {
        $to = $mail instanceof Mailable ? $mail->to : [
            ['name' => null, 'address' => $event->notifiable->routeNotificationFor('mail')]
        ];

        if (isset($this->alwaysTo)) {
            $to[] = $this->alwaysTo;
        }

        return $this->normalizeAddresses($to);
    }

    /**
     * Get the "from" addresses for the mail message.
     */
    protected function from(NotificationSending $event, Mailable | MailMessage $mail): array
    {
        if (empty($mail->from)) {
            return $this->alwaysFrom ?? [];
        }

        return match ($mail instanceof Mailable) {
            true => $mail->from[0],
            false => [
                'address' => $mail->from[0],
                'name' => $mail->from[1],
            ],
        };
    }

    /**
     * Get the "reply to" addresses for the mail message.
     */
    protected function replyTo(NotificationSending $event, Mailable | MailMessage $mail): array
    {
        $replyTo = $mail->replyTo;

        if (isset($this->alwaysReplyTo)) {
            $replyTo[] = $this->alwaysReplyTo;
        }

        return $this->normalizeAddresses($replyTo);
    }

    /**
     * Normalize the given addresses into a consistent array format.
     */
    protected function normalizeAddresses(array $addresses): array
    {
        return array_map(fn ($address) => Arr::isAssoc($address) ? $address : ['address' => $address[0], 'name' => $address[1]], $addresses);
    }

    /**
     * {@inheritDoc}
     */
    public function response(Manifest $manifest): HttpResponse
    {
        return Response::make($this->filesystem->get($this->path("{$manifest->uuid}/index.html")), 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    /**
     * Set the address that is always used as the "from" address.
     */
    public function alwaysFrom(string $address, ?string $name): self
    {
        $this->alwaysFrom = compact('address', 'name');

        return $this;
    }

    /**
     * Set the addresses that are always used as the "to" addresses.
     */
    public function alwaysTo(string $address, ?string $name): self
    {
        $this->alwaysTo = compact('address', 'name');

        return $this;
    }

    /**
     * Set the addresses that are always used as the "reply to" addresses.
     */
    public function alwaysReplyTo(string $address, ?string $name): self
    {
        $this->alwaysReplyTo = compact('address', 'name');

        return $this;
    }

    /**
     * Build the attachments data for the given manifest.
     */
    public function buildAttachmentsData(Manifest $manifest): array
    {
        return collect($manifest->data['attachments'])
            ->map(fn (array $data, $uuid) => AttachmentData::from([
                'name' => $data['name'],
                'type' => AttachmentType::fromExtension(Str::afterLast($data['name'], '.')),
                'size' => Number::fileSize(
                    bytes: $this->filesystem->size($this->path("{$manifest->uuid}/attachments/{$uuid}")),
                    precision: 2
                ),
                'url' => URL::route('foxhound::attachment', [
                    'channel' => $this->data()->key,
                    'message' => $manifest->uuid,
                    'attachment' => $uuid,
                ]),
            ]))
            ->values()
            ->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function data(): ChannelData
    {
        return ChannelData::from([
            'key' => 'mail',
            'name' => 'Mail',
            'type' => ChannelType::Mail
        ]);
    }
}
