<?php

namespace Foxhound\Http\Controllers;

use Foxhound\ChannelManager;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Foxhound\Http\Resources\MessageListResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChannelMessageController extends Controller
{
    /**
     * Get a list of messages for a given channel.
     */
    public function index(ChannelManager $manager, Filesystem $filesystem, string $channel): ResourceCollection
    {
        $channel = $manager->driver($channel);

        return MessageListResource::collection($channel->messages());
    }

    /**
     * Get the HTML markup for a given message and channel.
     */
    public function show(ChannelManager $manager, string $channel, string $uuid): HttpResponse
    {
        $channel = $manager->driver($channel);

        if ($manifest = $channel->buildManifest($uuid)) {
            $manifest->markAsRead();
            $manifest->save();

            return $channel->response($manifest);
        }

        return Response::view('foxhound::message_not_found');
    }

    /**
     * Delete all messages for a given channel.
     */
    public function destroy(ChannelManager $manager, Filesystem $filesystem, string $channel): HttpResponse
    {
        $channel = $manager->driver($channel);

        $channel->deleteMessages();

        return Response::noContent();
    }
}
