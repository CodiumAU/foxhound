<?php

namespace App\Interceptor\Channels;

use RuntimeException;
use Illuminate\Support\Str;
use App\Interceptor\Manifest;
use Illuminate\Mail\Mailable;
use App\Interceptor\Interceptor;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Events\NotificationSending;

class Mail extends Channel
{
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
        $manifest->data('to', $this->to($event, $mail));
        $manifest->data('cc', $mail->cc);
        $manifest->data('bcc', $mail->bcc);

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

    public function relativePath(string $path = null): string
    {
        return "{$this->rootStorageDirectory}/mail/{$path}";
    }

    public function path(string $path = null): string
    {
        return $this->filesystem->path(
            $this->relativePath($path)
        );
    }

    protected function to(NotificationSending $event, Mailable | MailMessage $mail): array
    {
        if ($mail instanceof Mailable) {
            return $mail->to;
        }

        return [
            ['name' => null, 'address' => $event->notifiable->routeNotificationFor('mail')]
        ];
    }
}
