<?php

namespace Foxhound\Channels;

use RuntimeException;
use Foxhound\Manifest;
use Foxhound\ChannelType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Mail\Mailable;
use Foxhound\Data\ChannelData;
use Illuminate\Support\Number;
use Foxhound\Data\AttachmentData;
use Foxhound\Data\MailMessageData;
use Foxhound\Data\MessageSummaryData;
use Foxhound\Data\MessageRecipientData;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Events\NotificationSending;

class Mail extends Channel
{
    protected array $alwaysFrom;

    protected array $alwaysTo;

    protected array $alwaysReplyTo;

    public function intercept(NotificationSending $event, Manifest $manifest): void
    {
        throw_unless(method_exists($event->notification, 'toMail'), new RuntimeException('Notification does not have a "toMail" method.'));

        /** @var \Illuminate\Notifications\Messages\MailMessage|\Illuminate\Mail\Mailable */
        $mail = $event->notification->toMail($event->notifiable);

        // Generate and store the rendered mail.
        $this->filesystem->put(
            $this->relativePath("{$manifest->uuid}/index.html"),
            $mail->render(),
        );

        $manifest->data('subject', $mail->subject);
        $manifest->data('from', $this->from($event, $mail));
        $manifest->data('replyTo', $this->replyTo($event, $mail));
        $manifest->data('to', $this->to($event, $mail));
        $manifest->data('cc', $this->normalizeAddresses($mail->cc));
        $manifest->data('bcc', $this->normalizeAddresses($mail->bcc));

        $this->filesystem->makeDirectory($this->relativePath("{$manifest->uuid}/attachments"));

        // Store mail attachments.
        $attachments = [];

        /** @var array $attachment */
        foreach ($mail->attachments as $attachment) {
            $uuid = Str::uuid();

            $fileName = $attachment['options']['as'] ?? basename($attachment['file']);

            $this->filesystem->put(
                $this->relativePath("{$manifest->uuid}/attachments/{$uuid}"),
                file_get_contents($attachment['file'])
            );

            $attachments[(string) $uuid] = [
                'name' => $fileName,
                'mime' => $attachment['options']['mime'] ?? null,
            ];
        }

        /** @var array $attachment */
        foreach ($mail->rawAttachments as $attachment) {
            $uuid = Str::uuid();

            $this->filesystem->put(
                $this->relativePath("{$manifest->uuid}/attachments/{$uuid}"),
                $attachment['data']
            );

            $attachments[(string) $uuid] = [
                'name' => $attachment['name'],
                'mime' => $attachment['options']['mime'] ?? null,
            ];
        }

        $manifest->data('attachments', $attachments);
    }

    public function newMessageSummaryData(Manifest $manifest): MessageSummaryData
    {
        return MessageSummaryData::from([
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
                'attachments' => $this->attachments($manifest),
            ])
        ]);
    }

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

    protected function replyTo(NotificationSending $event, Mailable | MailMessage $mail): array
    {
        $replyTo = $mail->replyTo;

        if (isset($this->alwaysReplyTo)) {
            $replyTo[] = $this->alwaysReplyTo;
        }

        return $this->normalizeAddresses($replyTo);
    }

    protected function normalizeAddresses(array $addresses): array
    {
        return array_map(fn ($address) => Arr::isAssoc($address) ? $address : ['address' => $address[0], 'name' => $address[1]], $addresses);
    }

    public function response(Manifest $manifest): Response
    {
        return response($this->filesystem->get($this->relativePath("{$manifest->uuid}/index.html")), 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    public function data(): ChannelData
    {
        return ChannelData::from([
            'key' => 'mail',
            'name' => 'Mail',
            'type' => ChannelType::Mail
        ]);
    }

    public function alwaysFrom(string $address, ?string $name): self
    {
        $this->alwaysFrom = compact('address', 'name');

        return $this;
    }

    public function alwaysTo(string $address, ?string $name): self
    {
        $this->alwaysTo = compact('address', 'name');

        return $this;
    }

    public function alwaysReplyTo(string $address, ?string $name): self
    {
        $this->alwaysReplyTo = compact('address', 'name');

        return $this;
    }
}
