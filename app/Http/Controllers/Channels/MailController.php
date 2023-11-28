<?php

namespace App\Http\Controllers\Channels;

use App\Interceptor\Manifest;
use App\Data\MessageSummaryData;
use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use App\Interceptor\InterceptorManager;
use App\Http\Resources\MessageListResource;

class MailController extends Controller
{
    public function index(InterceptorManager $manager, Filesystem $filesystem)
    {
        $mail = $manager->driver('mail');

        $folders = $filesystem->glob($mail->path('*'), GLOB_ONLYDIR);

        $messages = [];

        foreach ($folders as $folder) {
            $manifest = Manifest::parse(
                $filesystem->get("{$folder}/manifest.json")
            );

            $messages[$manifest->uuid] = MessageSummaryData::fromMail($manifest);
        }

        krsort($messages, SORT_STRING);

        return MessageListResource::collection(array_values($messages));
    }
}
