<?php

namespace Foxhound\Http\Controllers;

use Foxhound\ChannelManager;
use Foxhound\Contracts\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class StorageCleanupController extends Controller
{
    /**
     * Cleanup the storage.
     */
    public function __invoke(ChannelManager $manager, Storage $storage): HttpResponse
    {
        foreach (config('foxhound.channels') as $channel) {
            $channel = $manager->driver($channel);

            $storage->deleteMessages($channel);
        }

        return Response::noContent();
    }
}
