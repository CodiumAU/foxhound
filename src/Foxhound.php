<?php

namespace Foxhound;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class Foxhound
{
    /**
     * Indicates if Foxhound is enabled.
     */
    public static function enabled(): bool
    {
        return Config::get('foxhound.enabled') && App::environment(Config::get('foxhound.environments'));
    }
}
