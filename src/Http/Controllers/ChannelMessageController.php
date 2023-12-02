<?php

namespace Foxhound\Http\Controllers;

use Foxhound\ChannelManager;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
use Foxhound\Http\Resources\MessageListResource;

class ChannelMessageController extends Controller
{
    public function index(ChannelManager $manager, Filesystem $filesystem, string $channel)
    {
        $driver = $manager->driver($channel);

        $messages = [];

        foreach ($filesystem->glob($driver->path('*'), GLOB_ONLYDIR) as $path) {
            $manifest = $driver->manifest(basename($path));

            $messages[$manifest->uuid] = $driver->newMessageSummaryData($manifest);
        }

        krsort($messages, SORT_STRING);

        return MessageListResource::collection(array_values($messages));
    }
}