<?php
use App\Foxhound\ChannelType;

return [

    'channels' => [
        'mail' => [
            'name' => 'Mail',
            'type' => ChannelType::Mail,
        ],
        'vonage' => [
            'name' => 'Vonage',
            'type' => ChannelType::Sms,
        ]
    ]

];
