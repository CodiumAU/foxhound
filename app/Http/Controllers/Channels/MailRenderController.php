<?php

namespace App\Http\Controllers\Channels;

use Illuminate\Http\Request;
use App\Interceptor\Manifest;
use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use App\Interceptor\InterceptorManager;

class MailRenderController extends Controller
{
    public function __invoke(InterceptorManager $manager, Filesystem $filesystem, string $uuid)
    {
        $mail = $manager->driver('mail');

        if ($filesystem->exists($path = $mail->path("{$uuid}/index.html")) === false) {
            abort(404);
        }

        return response($filesystem->get($path), 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
