<?php

namespace App\Http\Controllers;

use App\Foxhound\ChannelManager;

class ChannelHtmlController extends Controller
{
    public function __invoke(ChannelManager $manager, string $channel, string $uuid): mixed
    {
        $driver = $manager->driver($channel);

        $manifest = $driver->manifest($uuid);
        $manifest->markAsRead();

        $driver->save($manifest);

        return $driver->response($manifest);
    }
}
