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
    | Intercepting Environments
    |--------------------------------------------------------------------------
    |
    | The environments where Foxhound should intercept notifications.
    |
    */
    'environments' => [
        'local',
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

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | Configure how Foxhound stores the intercepted notifications.
    |
    */
    'storage' => [
        'driver' => env('FOXHOUND_STORAGE_DRIVER', 'filesystem'),

        'database' => [
            'connection' => env('FOXHOUND_DB_CONNECTION', null),
        ],

        'filesystem' => [
            'disk' => env('FOXHOUND_STORAGE_DISK', 'local'),
            'root' => 'foxhound',
        ]
    ]

];
