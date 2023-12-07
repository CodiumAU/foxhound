<?php

namespace Foxhound\Http\Controllers;

use Foxhound\ChannelManager;
use Foxhound\Contracts\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Foxhound\Http\Resources\MessageListResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MessageController extends Controller
{
    /**
     * Get a list of messages for a given channel.
     */
    public function index(ChannelManager $manager, Storage $storage, string $channel): ResourceCollection
    {
        $channel = $manager->driver($channel);

        return MessageListResource::collection($storage->getMessages($channel));
    }

    /**
     * Get the HTML markup for a given message and channel.
     */
    public function show(ChannelManager $manager, Storage $storage, string $channel, string $uuid): HttpResponse
    {
        $channel = $manager->driver($channel);

        if ($manifest = $storage->getManifest($channel, $uuid)) {
            $manifest->markAsRead();

            $storage->saveManifest($manifest);

            return $channel->response($manifest);
        }

        return Response::view('foxhound::message_not_found');
    }

    /**
     * Delete all messages for a given channel.
     */
    public function destroy(ChannelManager $manager, Storage $storage, string $channel): HttpResponse
    {
        $channel = $manager->driver($channel);

        $storage->deleteMessages($channel);

        return Response::noContent();
    }
}
