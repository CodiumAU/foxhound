<?php

namespace Foxhound\Http\Controllers;

use Exception;
use Foxhound\ChannelManager;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class ChannelHtmlController extends Controller
{
    public function __invoke(ChannelManager $manager, string $channel, string $uuid): mixed
    {
        $driver = $manager->driver($channel);

        if ($manifest = $driver->manifest($uuid)) {
            $manifest->markAsRead();

            $driver->save($manifest);

            return $driver->response($manifest);
        }

        return Response::view('foxhound::404');
    }
}
