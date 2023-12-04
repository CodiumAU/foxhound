<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Channels
    |--------------------------------------------------------------------------
    |
    | The channels that should be intercepted. Channels not listed in this
    | array will continue to operate as normal.
    |
    */
    'channels' => [
        'mail',
        'vonage',
    ],

    /*
    |--------------------------------------------------------------------------
    | Foxhound Master Switch
    |--------------------------------------------------------------------------
    |
    | Enable or disable Foxhound entirely.
    |
    */
    'enabled' => env('FOXHOUND_ENABLED', true),

];
