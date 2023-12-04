<?php

namespace Foxhound\Http\Controllers;

use Foxhound\ChannelManager;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class StorageCleanupController extends Controller
{
    /**
     * Cleanup the storage.
     */
    public function __invoke(ChannelManager $manager): HttpResponse
    {
        foreach (config('foxhound.channels') as $channel) {
            $channel = $manager->driver($channel);

            $channel->deleteMessages();
        }

        return Response::noContent();
    }
}
