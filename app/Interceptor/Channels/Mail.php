<?php

namespace App\Interceptor\Channels;

use App\Interceptor\Manifest;
use App\Interceptor\Interceptor;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Notifications\Events\NotificationSending;

class Mail extends Interceptor
{
    public function intercept(NotificationSending $event, Manifest $manifest): void
    {
        $mail = $event->notification->toMail($event->notifiable);

        // Store the rendered email.
        $this->filesystem->put(
            "{$this->rootStorageDirectory}/{$manifest->uuid}/index.html",
            $mail->render(),
        );

        $this->filesystem->makeDirectory("{$this->rootStorageDirectory}/{$manifest->uuid}/attachments");

        // Store mail attachments.
        /** @var array $attachment */
        foreach ($mail->attachments as $attachment) {
            $fileName = basename($attachment['file']);

            $this->filesystem->put(
                "{$this->rootStorageDirectory}/{$manifest->uuid}/attachments/{$fileName}",
                file_get_contents($attachment['file'])
            );
        }

        /** @var array $attachment */
        foreach ($mail->rawAttachments as $attachment) {
            $this->filesystem->put(
                "{$this->rootStorageDirectory}/{$manifest->uuid}/attachments/{$attachment['name']}",
                $attachment['data']
            );
        }
    }
}
