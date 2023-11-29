<?php

namespace App\Http\Controllers\Channels;

use App\Interceptor\Manifest;
use App\Data\MessageSummaryData;
use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use App\Interceptor\InterceptorManager;
use App\Http\Resources\MessageListResource;

class VonageController extends Controller
{
    public function index(InterceptorManager $manager, Filesystem $filesystem)
    {
        $driver = $manager->driver('vonage');

        $messages = [];

        foreach ($filesystem->glob($driver->path('*'), GLOB_ONLYDIR) as $folder) {
            $manifest = Manifest::parse(
                $filesystem->get("{$folder}/manifest.json")
            );

            $messages[$manifest->uuid] = MessageSummaryData::fromVonage($manifest);
        }

        krsort($messages, SORT_STRING);

        return MessageListResource::collection(array_values($messages));
    }
}
