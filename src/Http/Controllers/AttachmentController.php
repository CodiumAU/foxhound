<?php

namespace Foxhound\Http\Controllers;

use Foxhound\ChannelManager;
use Foxhound\Contracts\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    /**
     * Get the attachment for a given message and channel.
     */
    public function __invoke(ChannelManager $manager, Storage $storage, string $channel, string $message, string $attachment): HttpResponse | StreamedResponse
    {
        $channel = $manager->driver($channel);

        if ($manifest = $storage->getManifest($channel, $message)) {
            if (isset($manifest->attachments[$attachment])) {
                $attachment = $manifest->attachments[$attachment];

                return Response::stream(function () use ($attachment) {
                    echo $attachment->data;
                }, 200, [
                    'Content-Type' => $attachment->mime,
                    'Content-Disposition' => sprintf('inline; filename="%s"', $attachment->name),
                ]);
            }
        }

        return Response::view('foxhound::attachment_not_found');
    }
}
