<?php

namespace App\Http\Controllers\Channels;

use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use App\Interceptor\InterceptorManager;

class MailRenderController extends Controller
{
    public function __invoke(InterceptorManager $manager, Filesystem $filesystem, string $uuid)
    {
        $driver = $manager->driver('mail');

        if ($filesystem->exists($path = $driver->path("{$uuid}/index.html")) === false) {
            abort(404);
        }

        $manifest = $driver->manifest($uuid);
        $manifest->markAsRead();

        $driver->save($manifest);

        return response($filesystem->get($path), 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
