<?php

namespace Foxhound\Http\Controllers;

use Exception;
use Foxhound\ChannelManager;
use Illuminate\Routing\Controller;

class ChannelHtmlController extends Controller
{
    public function __invoke(ChannelManager $manager, string $channel, string $uuid): mixed
    {
        $driver = $manager->driver($channel);

        try {
            $manifest = $driver->manifest($uuid);
            $manifest->markAsRead();

            $driver->save($manifest);

            return $driver->response($manifest);
        } catch (Exception) {
            return view('404');
        }
    }
}
