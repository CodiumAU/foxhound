<?php

namespace Foxhound\Http\Controllers;

use Foxhound\ChannelManager;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    /**
     * Get the attachment for a given message and channel.
     */
    public function __invoke(ChannelManager $manager, string $channel, string $message, string $attachment): HttpResponse | StreamedResponse
    {
        $channel = $manager->driver($channel);

        if ($manifest = $channel->buildManifest($message)) {
            $attachments = $manifest->data['attachments'];

            if (isset($attachments[$attachment])) {
                return Response::stream(function () use ($channel, $message, $attachment) {
                    echo $channel->file("{$message}/attachments/{$attachment}");
                }, 200, [
                    'Content-Type' => $attachments[$attachment]['mime'],
                    'Content-Disposition' => sprintf('inline; filename="%s"', $attachments[$attachment]['name']),
                ]);
            }
        }

        return Response::view('foxhound::attachment_not_found');
    }
}