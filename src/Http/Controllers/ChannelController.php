<?php

namespace Foxhound\Http\Controllers;

use Foxhound\ChannelManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Foxhound\Http\Resources\ChannelListResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChannelController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ChannelManager $channelManager): ResourceCollection
    {
        $channels = collect(config('foxhound.channels'))
            ->map(fn ($channel) => $channelManager->driver($channel)->data());

        return ChannelListResource::collection($channels);
    }
}
